(() => {
    const mountEl = document.getElementById("harbor-app");
    if (!mountEl) return;

    const data = window.__HARBOR_DATA__ || {};
    const map = window.__HAFEN_SLOTS__ || [];

    // Helper: CSRF aus der Seite ziehen (CI4 csrf_field erzeugt i.d.R. input[name^="csrf_"])
    function getCsrf() {
        const el = document.querySelector('input[name^="csrf_"]');
        if (!el) return null;
        return { name: el.getAttribute("name"), value: el.value };
    }

    function post(path, fields) {
        const form = document.createElement("form");
        form.method = "POST";
        form.action = path;

        Object.entries(fields).forEach(([k, v]) => {
            const i = document.createElement("input");
            i.type = "hidden";
            i.name = k;
            i.value = String(v);
            form.appendChild(i);
        });

        const csrf = getCsrf();
        if (csrf) {
            const c = document.createElement("input");
            c.type = "hidden";
            c.name = csrf.name;
            c.value = csrf.value;
            form.appendChild(c);
        }

        document.body.appendChild(form);
        form.submit();
    }

    function esc(str) {
        return String(str ?? "")
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }

    const app = Vue.createApp({
        data() {
            return {
                liegeplaetze: data.liegeplaetze || [],
                boote: data.boote || [],

                selectedLids: new Set((data.selected_lids || []).map((x) => parseInt(x, 10))),
                selectedBoids: new Set((data.selected_boids || []).map((x) => parseInt(x, 10))),
                assignments: data.assignments || {},

                draggingBoid: null,
                map,
            };
        },

        computed: {
            boatsAvailable() {
                return this.boote.filter((b) => !!b.is_available_in_range);
            },
            boatsUnavailable() {
                return this.boote.filter((b) => !b.is_available_in_range);
            },
            lpByKey() {
                const m = new Map();
                for (const lp of this.liegeplaetze) {
                    m.set(`${lp.anleger}-${lp.nummer}`, lp);
                }
                return m;
            },
            boatById() {
                const m = new Map();
                for (const b of this.boote) m.set(parseInt(b.boid, 10), b);
                return m;
            },
        },

        mounted() {
            mountEl.innerHTML = `
        <div style="display:grid; grid-template-columns: 1.4fr 0.6fr; gap:16px; align-items:start;">

          <div class="card" style="padding:16px;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
              <h3 style="margin:0;">Anlegerplan</h3>
              <div class="muted" style="font-size:13px;">
                Klick = Einzelbuchung • Boot ziehen = Boot+Liegeplatz
              </div>
            </div>

            <div style="position:relative; margin-top:12px; border-radius:16px; overflow:hidden; border:1px solid #e5e7eb;">
              <img src="/img/anlieger.png" alt="Anleger" style="width:100%; display:block;">
              <div id="overlay" style="position:absolute; inset:0;"></div>
            </div>

            <div style="margin-top:12px; display:flex; gap:12px; flex-wrap:wrap;">
              <span class="badge badge--green">verfügbar</span>
              <span class="badge badge--purple">im Zeitraum gebucht</span>
              <span class="badge badge--gray">nicht verfügbar</span>
              <span class="badge badge--blue">ausgewählt</span>
              <span class="badge badge--orange">Boot zugewiesen</span>
            </div>
          </div>

          <div class="card" style="padding:16px;">
            <h3 style="margin:0 0 10px;">Boote</h3>

            <div class="muted" style="font-size:13px; margin-bottom:10px;">
              Ziehe ein Boot auf einen Liegeplatz oder klicke für Einzelbuchung.
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;" id="boat-list"></div>

            <div style="margin-top:14px;">
              <h4 style="margin:0 0 8px; font-size:14px;">Nicht verfügbar</h4>
              <div style="display:flex; flex-direction:column; gap:8px;" id="boat-list-na"></div>
            </div>
          </div>

        </div>
      `;

            this.injectMiniStyles();
            this.renderOverlay();
            this.renderBoatLists();
        },

        methods: {
            injectMiniStyles() {
                if (document.getElementById("harbor-mini-styles")) return;
                const s = document.createElement("style");
                s.id = "harbor-mini-styles";
                s.textContent = `
          .card { background:#fff; border:1px solid #e5e7eb; border-radius:18px; box-shadow:0 10px 30px rgba(2,6,23,.06); }
          .muted { color:#475569; }
          .badge { display:inline-flex; align-items:center; gap:6px; padding:2px 10px; border-radius:999px; font-size:12px; font-weight:700; border:1px solid #cbd5e1; }
          .badge--green { border-color:#16a34a; color:#16a34a; }
          .badge--purple { border-color:#7c3aed; color:#7c3aed; }
          .badge--gray { border-color:#64748b; color:#64748b; }
          .badge--blue { border-color:#2563eb; color:#2563eb; }
          .badge--orange { border-color:#ea580c; color:#ea580c; }

          /* Slots: nur Klick/Drop-Flächen, keine Texte */
          .dock-slot { position:absolute; border-radius:10px; border:2px solid rgba(37,99,235,0); background:rgba(37,99,235,0); cursor:pointer; pointer-events:auto; }
          .dock-slot:hover { border-color: rgba(37,99,235,.55); background: rgba(37,99,235,.08); }
          .dock-slot.na { cursor:not-allowed; opacity:.55; }
          .dock-slot.selected { border-color: rgba(37,99,235,.9); background: rgba(37,99,235,.14); }
          .dock-slot.assigned { border-color: rgba(234,88,12,.9); background: rgba(234,88,12,.12); }

          .boat-item { border:1px solid #e5e7eb; border-radius:14px; padding:10px 12px; background:#fff; box-shadow:0 10px 25px rgba(2,6,23,.06); }
          .boat-item.na { opacity:.55; cursor:not-allowed; }
          .boat-item .top { display:flex; justify-content:space-between; gap:10px; align-items:flex-start; }
          .boat-item .name { font-weight:900; font-size:13px; line-height:1.2; }
          .boat-item .meta { color:#475569; font-size:12px; margin-top:6px; }
          .boat-item.draggable { cursor:grab; }
          .boat-item.draggable:active { cursor:grabbing; }
        `;
                document.head.appendChild(s);
            },

            renderOverlay() {
                const overlay = mountEl.querySelector("#overlay");
                if (!overlay) return;
                overlay.innerHTML = "";

                for (const slot of this.map) {
                    const lp = this.lpByKey.get(`${slot.anleger}-${slot.nummer}`);
                    if (!lp) continue;

                    const lid = parseInt(lp.lid, 10);
                    const isAvail = !!lp.is_available_in_range;

                    const isSelected = this.selectedLids.has(lid);
                    const assignedBoid = this.assignments[lid] ? parseInt(this.assignments[lid], 10) : null;

                    const div = document.createElement("div");
                    div.className = "dock-slot";
                    div.style.left = `${slot.x}%`;
                    div.style.top = `${slot.y}%`;
                    div.style.width = `${slot.w}%`;
                    div.style.height = `${slot.h}%`;

                    if (!isAvail) div.classList.add("na");
                    if (isSelected) div.classList.add("selected");
                    if (assignedBoid) div.classList.add("assigned");

                    // Klick = Liegeplatz einzeln togglen (nur wenn verfügbar)
                    div.addEventListener("click", (e) => {
                        e.preventDefault();
                        if (!isAvail) return;
                        post("/buchung/liegeplatz-toggle", { lid });
                    });

                    // Drop-Zone für Boote: dragover MUSS preventDefault, sonst gibt es keinen drop
                    div.addEventListener("dragover", (e) => {
                        if (!isAvail) return;
                        e.preventDefault();
                        if (e.dataTransfer) e.dataTransfer.dropEffect = "move";
                    });

                    div.addEventListener("drop", (e) => {
                        if (!isAvail) return;
                        e.preventDefault();

                        // Stabil: boid aus dataTransfer holen
                        let boid = 0;
                        try {
                            boid = parseInt(e.dataTransfer?.getData("text/plain") || "0", 10);
                        } catch (_) {
                            boid = 0;
                        }

                        // Fallback (falls Browser kein dataTransfer liefert)
                        if (!boid) boid = this.draggingBoid || 0;

                        if (!boid) return;

                        post("/buchung/assign", { lid, boid, mode: "attach" });
                    });

                    overlay.appendChild(div);
                }
            },

            renderBoatLists() {
                const list = mountEl.querySelector("#boat-list");
                const listNa = mountEl.querySelector("#boat-list-na");
                if (!list || !listNa) return;

                list.innerHTML = "";
                listNa.innerHTML = "";

                const renderBoat = (b, target, isAvail) => {
                    const boid = parseInt(b.boid, 10);
                    const selected = this.selectedBoids.has(boid);

                    const div = document.createElement("div");
                    div.className = "boat-item";
                    if (isAvail) div.classList.add("draggable");
                    else div.classList.add("na");

                    if (isAvail) {
                        div.setAttribute("draggable", "true");

                        div.addEventListener("dragstart", (e) => {
                            this.draggingBoid = boid;

                            // Stabilität: dataTransfer setzen
                            if (e.dataTransfer) {
                                e.dataTransfer.setData("text/plain", String(boid));
                                e.dataTransfer.effectAllowed = "move";
                            }
                        });

                        div.addEventListener("dragend", () => {
                            this.draggingBoid = null;
                        });
                    }

                    div.innerHTML = `
            <div class="top">
              <div>
                <div class="name">${esc(b.name)}${b.typ ? " (" + esc(b.typ) + ")" : ""}</div>
                <div class="meta">Plätze: ${esc(b.plaetze ?? "")}</div>
              </div>
              <span class="badge ${selected ? "badge--blue" : "badge--gray"}">
                ${selected ? "ausgewählt" : (isAvail ? "frei" : "nicht frei")}
              </span>
            </div>
          `;

                    // Klick = Boot einzeln togglen (nur wenn verfügbar)
                    div.addEventListener("click", (e) => {
                        e.preventDefault();
                        if (!isAvail) return;
                        post("/buchung/boot-toggle", { boid });
                    });

                    target.appendChild(div);
                };

                this.boatsAvailable.forEach((b) => renderBoat(b, list, true));
                this.boatsUnavailable.forEach((b) => renderBoat(b, listNa, false));
            },
        },
    });

    app.mount("#harbor-app");
})();