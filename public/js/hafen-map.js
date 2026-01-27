// public/js/hafen-map.js
// Feste Slot-Positionen für public/img/anlieger.png (2048 x 1365)
// x,y,w,h in Prozent (0..100)

(function () {
    const TOP_Y = 26.374;     // Start Y der oberen Reihe
    const BOTTOM_Y = 65.201;  // Start Y der unteren Reihe
    const SLOT_H = 24.176;    // Höhe der klickbaren Zone

    // Reihe A (oben) – 17 Plätze
    const A = [
        { anleger: "A", nummer: 1,  x: 3.052,  y: TOP_Y,    w: 2.380, h: SLOT_H },
        { anleger: "A", nummer: 2,  x: 7.520,  y: TOP_Y,    w: 3.425, h: SLOT_H },
        { anleger: "A", nummer: 3,  x: 12.939, y: TOP_Y,    w: 3.616, h: SLOT_H },
        { anleger: "A", nummer: 4,  x: 18.311, y: TOP_Y,    w: 3.737, h: SLOT_H },
        { anleger: "A", nummer: 5,  x: 24.072, y: TOP_Y,    w: 3.900, h: SLOT_H },
        { anleger: "A", nummer: 6,  x: 30.225, y: TOP_Y,    w: 3.675, h: SLOT_H },
        { anleger: "A", nummer: 7,  x: 36.548, y: TOP_Y,    w: 3.675, h: SLOT_H },
        { anleger: "A", nummer: 8,  x: 42.822, y: TOP_Y,    w: 3.675, h: SLOT_H },
        { anleger: "A", nummer: 9,  x: 48.999, y: TOP_Y,    w: 3.738, h: SLOT_H },
        { anleger: "A", nummer: 10, x: 54.517, y: TOP_Y,    w: 3.675, h: SLOT_H },
        { anleger: "A", nummer: 11, x: 60.171, y: TOP_Y,    w: 3.675, h: SLOT_H },
        { anleger: "A", nummer: 12, x: 65.625, y: TOP_Y,    w: 3.550, h: SLOT_H },
        { anleger: "A", nummer: 13, x: 71.396, y: TOP_Y,    w: 3.613, h: SLOT_H },
        { anleger: "A", nummer: 14, x: 77.057, y: TOP_Y,    w: 3.675, h: SLOT_H },
        { anleger: "A", nummer: 15, x: 82.764, y: TOP_Y,    w: 3.612, h: SLOT_H },
        { anleger: "A", nummer: 16, x: 88.208, y: TOP_Y,    w: 3.675, h: SLOT_H },
        { anleger: "A", nummer: 17, x: 94.702, y: TOP_Y,    w: 6.252, h: SLOT_H },
    ];

    // Reihe B (unten) – 18 Plätze
    const B = [
        { anleger: "B", nummer: 1,  x: 3.787,  y: BOTTOM_Y, w: 3.516, h: SLOT_H },
        { anleger: "B", nummer: 2,  x: 9.033,  y: BOTTOM_Y, w: 3.304, h: SLOT_H },
        { anleger: "B", nummer: 3,  x: 13.793, y: BOTTOM_Y, w: 2.985, h: SLOT_H },
        { anleger: "B", nummer: 4,  x: 18.311, y: BOTTOM_Y, w: 3.327, h: SLOT_H },
        { anleger: "B", nummer: 5,  x: 23.340, y: BOTTOM_Y, w: 3.327, h: SLOT_H },
        { anleger: "B", nummer: 6,  x: 28.320, y: BOTTOM_Y, w: 3.390, h: SLOT_H },
        { anleger: "B", nummer: 7,  x: 33.472, y: BOTTOM_Y, w: 3.361, h: SLOT_H },
        { anleger: "B", nummer: 8,  x: 38.647, y: BOTTOM_Y, w: 3.361, h: SLOT_H },
        { anleger: "B", nummer: 9,  x: 43.750, y: BOTTOM_Y, w: 3.361, h: SLOT_H },
        { anleger: "B", nummer: 10, x: 48.682, y: BOTTOM_Y, w: 3.327, h: SLOT_H },
        { anleger: "B", nummer: 11, x: 53.613, y: BOTTOM_Y, w: 3.327, h: SLOT_H },
        { anleger: "B", nummer: 12, x: 58.862, y: BOTTOM_Y, w: 3.425, h: SLOT_H },
        { anleger: "B", nummer: 13, x: 64.160, y: BOTTOM_Y, w: 3.425, h: SLOT_H },
        { anleger: "B", nummer: 14, x: 69.336, y: BOTTOM_Y, w: 3.425, h: SLOT_H },
        { anleger: "B", nummer: 15, x: 74.439, y: BOTTOM_Y, w: 3.425, h: SLOT_H },
        { anleger: "B", nummer: 16, x: 79.592, y: BOTTOM_Y, w: 3.425, h: SLOT_H },
        { anleger: "B", nummer: 17, x: 84.717, y: BOTTOM_Y, w: 3.425, h: SLOT_H },
        { anleger: "B", nummer: 18, x: 91.455, y: BOTTOM_Y, w: 3.174, h: SLOT_H },
    ];

    // Export für Vue
    window.__HAFEN_SLOTS__ = [...A, ...B];
})();