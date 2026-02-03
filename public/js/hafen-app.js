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
                selectedBoats: window.__SELECTED_BOATS__ || [],
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

                const boat = this.draggingBoat;

                // ✅ Wir speichern (wie bisher) über den bestehenden Endpoint:
                // /buchung/liegeplatz-toggle  (Session "booking.liegeplaetze")
                await this.postToggleLiegeplatz(meta.lid, false);

                if (boat && boat.boid && !this.isBoatSelected(boat.boid)) {
                    await this.postToggleBoot(boat.boid, false);
                }

                this.reloadToBooking();
            },

            isBoatSelected(boid) {
                return this.selectedBoats.includes(Number(boid));
            },

            async postToggleLiegeplatz(lid, shouldReload = true) {
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

                    if (shouldReload) {
                        this.reloadToBooking();
                    }
                } catch (e) {
                    console.error(e);
                    alert('Speichern fehlgeschlagen (Netzwerk/Server).');
                }
            },

            async postToggleBoot(boid, shouldReload = true) {
                const url = window.__TOGGLE_BOOT_URL__;
                const csrfName = window.__CSRF_NAME__;
                const csrfHash = window.__CSRF_HASH__;

                const body = new URLSearchParams();
                body.set('boid', String(boid));
                if (csrfName && csrfHash) body.set(csrfName, csrfHash);

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                        body: body.toString(),
                        credentials: 'same-origin',
                    });

                    if (shouldReload) {
                        this.reloadToBooking();
                    }
                } catch (e) {
                    console.error(e);
                    alert('Speichern fehlgeschlagen (Netzwerk/Server).');
                }
            },

            reloadToBooking() {
                // Wir reloaden bewusst, damit rechts die Buchungsübersicht (PHP) garantiert aktuell ist.
                // (CI4 CSRF Hash kann sich ändern, daher ist Reload die robuste Variante.)
                window.location.hash = '#buchung';
                window.location.reload();
            }
        }
    }).mount('#hafenApp');
})();
