// public/js/hafen-map.js
// Feste Slot-Positionen für public/img/anlieger.png (2048 x 1365)
// x,y,w,h in Prozent (0..100)

(function () {
    const TOP_Y = 39;     // Start Y der oberen Reihe
    const BOTTOM_Y = 70;  // Start Y der unteren Reihe
    const SLOT_H = 18;    // Höhe der klickbaren Zone
    const SLOT_W = 3.5    // Breite der klickbaren Zone

    // Reihe A (oben) – 17 Plätze
    const A = [
        { anleger: "A", nummer: 1,  x: 1,  y: TOP_Y,    w: SLOT_W, h: SLOT_H },
        { anleger: "A", nummer: 2,  x: 6,  y: TOP_Y,    w: SLOT_W, h: SLOT_H },
        { anleger: "A", nummer: 3,  x: 11, y: TOP_Y,    w: SLOT_W, h: SLOT_H },
        { anleger: "A", nummer: 4,  x: 17, y: TOP_Y,    w: SLOT_W, h: SLOT_H },
        { anleger: "A", nummer: 5,  x: 23, y: TOP_Y,    w: SLOT_W, h: SLOT_H },
        { anleger: "A", nummer: 6,  x: 29, y: TOP_Y,    w: SLOT_W, h: SLOT_H },
        { anleger: "A", nummer: 7,  x: 34, y: TOP_Y,    w: SLOT_W, h: SLOT_H },
        { anleger: "A", nummer: 8,  x: 40, y: TOP_Y,    w: SLOT_W, h: SLOT_H },
        { anleger: "A", nummer: 9,  x: 46, y: TOP_Y,    w: SLOT_W, h: SLOT_H },
        { anleger: "A", nummer: 10, x: 51, y: TOP_Y,    w: SLOT_W, h: SLOT_H },
        { anleger: "A", nummer: 11, x: 57, y: TOP_Y,    w: SLOT_W, h: SLOT_H },
        { anleger: "A", nummer: 12, x: 62, y: TOP_Y,    w: SLOT_W, h: SLOT_H },
        { anleger: "A", nummer: 13, x: 68, y: TOP_Y,    w: SLOT_W, h: SLOT_H },
        { anleger: "A", nummer: 14, x: 74, y: TOP_Y,    w: SLOT_W, h: SLOT_H },
        { anleger: "A", nummer: 15, x: 79, y: TOP_Y,    w: SLOT_W, h: SLOT_H },
        { anleger: "A", nummer: 16, x: 85, y: TOP_Y,    w: SLOT_W, h: SLOT_H },
        { anleger: "A", nummer: 17, x: 92, y: TOP_Y,    w: SLOT_W, h: SLOT_H },
    ];

    // Reihe B (unten) – 18 Plätze
    const B = [
        { anleger: "B", nummer: 1,  x: 1.5,  y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 2,  x: 7,  y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 3,  x: 12, y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 4,  x: 17, y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 5,  x: 22, y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 6,  x: 27, y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 7,  x: 32.5, y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 8,  x: 37.5, y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 9,  x: 43, y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 10, x: 47.5, y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 11, x: 53, y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 12, x: 58, y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 13, x: 63.5, y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 14, x: 69, y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 15, x: 74.5, y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 16, x: 79.5, y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 17, x: 85, y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
        { anleger: "B", nummer: 18, x: 90, y: BOTTOM_Y, w: SLOT_W, h: SLOT_H },
    ];

    // Export für Vue
    window.__HAFEN_SLOTS__ = [...A, ...B];
})();