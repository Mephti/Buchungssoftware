// public/js/hafen-app.js
// Vue 3 (global) + HTML5 Drag & Drop

(function () {
    const { createApp } = window.Vue;

    createApp({
        data() {
            return {
                boats: window.__BOATS__ || [],
                slots: window.__HAFEN_SLOTS__ || [],
                slotMeta: window.__SLOT_META__ || {},   // key => { lid, available, bookedInRange, selected }
                draggingBoat: null,
            };
        },

        computed: {
            boatsAvailable() {
                // Du kannst hier filtern (z.B. nur verfügbare). Aktuell: alles anzeigen.
                return this.boats;
            }
        },

        methods: {
            slotKey(slot) {
                return `${slot.anleger}-${slot.nummer}`;
            },

            slotStyle(slot) {
                return {
                    left: slot.x + '%',
                    top: slot.y + '%',
                    width: slot.w + '%',
                    height: slot.h + '%',
                };
            },

            slotClass(slot) {
                const key = this.slotKey(slot);
                const meta = this.slotMeta[key];
                return {
                    'dock-slot': true,
                    'is-unavailable': meta && !meta.available,
                    'is-selected': meta && meta.selected,
                };
            },

            onDragStart(boat, ev) {
                this.draggingBoat = boat;
                ev.dataTransfer.effectAllowed = 'move';
                ev.dataTransfer.setData('text/plain', String(boat.boid ?? boat.id ?? boat.name ?? ''));
            },

            onDragEnd() {
                this.draggingBoat = null;
            },

            async onDrop(slot, ev) {
                ev.preventDefault();

                const key = this.slotKey(slot);
                const meta = this.slotMeta[key];

                if (!meta || !meta.lid) {
                    alert('Dieser Slot ist nicht mit einem LID verknüpft.');
                    return;
                }

                if (!meta.available) {
                    alert('Dieser Liegeplatz ist im Zeitraum nicht verfügbar.');
                    return;
                }

                // ✅ Wir speichern (wie bisher) über den bestehenden Endpoint:
                // /buchung/liegeplatz-toggle  (Session "booking.liegeplaetze")
                await this.postToggleLiegeplatz(meta.lid);
            },

            async postToggleLiegeplatz(lid) {
                const url = window.__TOGGLE_LP_URL__;
                const csrfName = window.__CSRF_NAME__;
                const csrfHash = window.__CSRF_HASH__;

                const body = new URLSearchParams();
                body.set('lid', String(lid));
                if (csrfName && csrfHash) body.set(csrfName, csrfHash);

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                        body: body.toString(),
                        credentials: 'same-origin',
                    });

                    // Wir reloaden bewusst, damit rechts die Buchungsübersicht (PHP) garantiert aktuell ist.
                    // (CI4 CSRF Hash kann sich ändern, daher ist Reload die robuste Variante.)
                    window.location.hash = '#buchung';
                    window.location.reload();
                } catch (e) {
                    console.error(e);
                    alert('Speichern fehlgeschlagen (Netzwerk/Server).');
                }
            }
        }
    }).mount('#hafenApp');
})();
