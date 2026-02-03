<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="platzhalter">
    <div class="platzhalter__image" aria-label="Hafenansicht">
        <img src="<?= base_url('img/cover.webp') ?>" alt="Hafen am Plauer See">
    </div>

    <div class="platzhalter__content">
        <div class="platzhalter__meta">
            <div></div>
            <div class="rating" aria-label="Bewertung 4,8 von 5">
                <span class="star" aria-hidden="true"></span>
                4,8 / 5
            </div>
        </div>

        <h1>Bootsverleih<br>am Plauer See</h1>

        <p>
            Buche bequem online einen Liegeplatz oder ein Boot.
            Wähle Zeitraum, sieh die Verfügbarkeit und schließe die Buchung direkt ab.
        </p>

        <a class="btn btn--primary" href="#buchung">Buchung ▾</a>
    </div>
</section>

<hr>

<section id="buchung">
    <h2>Buchung</h2>

    <form method="post" action="<?= site_url('/buchung/auswahl') ?>">
        <?= csrf_field() ?>

        <label>
            Von:
            <input type="date" name="von" required value="<?= esc(session('booking.von') ?? '') ?>">
        </label>

        <label style="margin-left:8px;">
            Bis:
            <input type="date" name="bis" required value="<?= esc(session('booking.bis') ?? '') ?>">
        </label>

        <!-- ✅ Radios entfernt, aber Backend bleibt kompatibel -->
        <input type="hidden" name="typ" value="liegeplatz">

        <button type="submit" style="margin-left:16px;">Übernehmen</button>
    </form>

    <?php
    $booking = session('booking') ?? [];
    ?>

    <div class="booking" style="margin-top:12px;">
    <!-- Links: Drag & Drop -->
        <div style="flex:3; min-height:300px; padding:12px;" class="card">
            <h3>Drag & Drop</h3>

            <div id="hafenApp" class="dd-grid">
                <!-- Links: Hafenbild + Drop-Zonen -->
                <div class="dd-map">
                    <div class="dd-map__wrap">
                        <img src="<?= base_url('img/anlieger.png') ?>" alt="Liegeplätze" class="dd-map__img">

                        <div
                                v-for="slot in slots"
                                :key="slot.anleger + '-' + slot.nummer"
                                :class="slotClass(slot)"
                                :style="slotStyle(slot)"
                                @dragover.prevent
                                @drop="onDrop(slot, $event)"
                        >
                            <span class="dock-slot__label">{{ slot.anleger }}-{{ slot.nummer }}</span>
                        </div>
                    </div>

                    <p class="dd-hint">Boot rechts greifen und auf einen freien Liegeplatz ziehen.</p>
                    <div class="dd-legend">
                        <span class="dd-legend__item is-available">frei</span>
                        <span class="dd-legend__item is-selected">ausgewählt</span>
                        <span class="dd-legend__item is-booked">vermietet</span>
                        <span class="dd-legend__item is-blocked">gesperrt</span>
                        <span class="dd-legend__item is-occupied">belegt</span>
                    </div>
                </div>

                <!-- Rechts: Boote -->
                <div class="dd-boats">
                    <h4>Boote</h4>

                    <div v-if="boatsAvailable.length === 0" class="dd-empty">
                        Keine Boote geladen.
                    </div>

                    <div
                            v-for="b in boatsAvailable"
                            :key="b.boid"
                            class="boat-item"
                            :class="{ 'is-disabled': b.status !== 'verfuegbar' }"
                            :draggable="b.status === 'verfuegbar'"
                            @dragstart="onDragStart(b, $event)"
                            @dragend="onDragEnd"
                    >
                        <div class="boat-item__title">{{ b.name }}</div>
                        <div class="boat-item__meta">
                            Typ: {{ b.typ || '-' }} · Plätze: {{ b.plaetze ?? '-' }} · Status: {{ b.status }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rechts: Buchungsübersicht bleibt -->
        <aside id="bookingSidebar" class="card sidebar">
            <h3>Ihre Buchung</h3>

            <?php if (!empty($booking)): ?>
                <p>
                    <strong>Von:</strong> <?= esc($booking['von'] ?? '') ?><br>
                    <strong>Bis:</strong> <?= esc($booking['bis'] ?? '') ?><br>
                    <strong>Typ:</strong> <?= esc($booking['typ'] ?? 'liegeplatz') ?>
                </p>

                <?php
                $hasDateRange = !empty($booking['von']) && !empty($booking['bis']);
                ?>

                <?php if (!empty($selectedLiegeplaetze)): ?>
                    <p><strong>Ausgewählte Liegeplätze:</strong></p>
                    <ul>
                        <?php foreach ($selectedLiegeplaetze as $lp): ?>
                            <li>
                                Anleger <?= esc($lp['anleger']) ?> – Platz <?= esc($lp['nummer']) ?>
                                · Kosten/Tag: <?= esc($lp['kosten_pt'] ?? 0) ?>
                                <?php if ($hasDateRange): ?>
                                    · Gesamt: <?= esc($lp['kosten_total'] ?? 0) ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if (!empty($selectedBoote)): ?>
                    <p><strong>Ausgewählte Boote:</strong></p>
                    <ul>
                        <?php foreach ($selectedBoote as $b): ?>
                            <li>
                                <?= esc($b['name']) ?><?= !empty($b['typ']) ? ' (' . esc($b['typ']) . ')' : '' ?>
                                · Kosten/Tag: <?= esc($b['kosten_pt'] ?? 0) ?>
                                <?php if ($hasDateRange): ?>
                                    · Gesamt: <?= esc($b['kosten_total'] ?? 0) ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($hasDateRange): ?>
                    <p><strong>Kosten:</strong></p>
                    <p>
                        Liegeplatz gesamt (<?= esc($daysCount) ?> <?= $daysCount === 1 ? 'Tag' : 'Tage' ?>):
                        <?= esc($kostenLiegeplatzTotal ?? 0) ?>
                    </p>
                    <p>
                        Boot gesamt (<?= esc($daysCount) ?> <?= $daysCount === 1 ? 'Tag' : 'Tage' ?>):
                        <?= esc($kostenBootTotal ?? 0) ?>
                    </p>
                <?php endif; ?>

                <?php
                $hasSelection = !empty($booking['liegeplaetze'] ?? []);
                ?>

                <form method="get" action="<?= site_url('/buchung/zusammenfassung') ?>">
                    <button type="submit" <?= $hasSelection ? '' : 'disabled' ?>>Weiter</button>
                </form>

                <?php if (!$hasSelection): ?>
                    <p style="color:#777; font-size:0.9em;">
                        Bitte mindestens einen Liegeplatz auswählen.
                    </p>
                <?php endif; ?>

                <form method="post" action="<?= site_url('/buchung/reset') ?>" style="display:inline;">
                    <?= csrf_field() ?>
                    <button type="submit" style="margin-left:8px;">Zurücksetzen</button>
                </form>

            <?php else: ?>
                <p>Bitte Zeitraum wählen.</p>
                <button disabled>Weiter</button>
            <?php endif; ?>
        </aside>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="<?= base_url('js/hafen-map.js') ?>"></script>

<?php
$booking = session('booking') ?? [];
$selectedLids = array_map('intval', $booking['liegeplaetze'] ?? []);
$selectedBoids = array_map('intval', $booking['boote'] ?? []);

// SlotMeta für Vue: key "A-1" => lid, available, selected
$slotMeta = [];
foreach (($liegeplaetze ?? []) as $lp) {
    $key = ($lp['anleger'] ?? '') . '-' . ($lp['nummer'] ?? '');
    $slotMeta[$key] = [
            'lid' => (int)($lp['lid'] ?? 0),
            'available' => !empty($lp['is_available_in_range']),
            'bookedInRange' => !empty($lp['is_booked_in_range']),
            'selected' => in_array((int)($lp['lid'] ?? 0), $selectedLids, true),
            'status' => (string)($lp['status'] ?? ''),
    ];
}

// Boote für Vue
$boatsForUi = [];
foreach (($boote ?? []) as $b) {
    $boatsForUi[] = [
            'boid' => (int)($b['boid'] ?? 0),
            'name' => (string)($b['name'] ?? ''),
            'typ' => (string)($b['typ'] ?? ''),
            'plaetze' => isset($b['plaetze']) ? (int)$b['plaetze'] : null,
            'status' => (string)($b['status'] ?? ''),
            'is_available_in_range' => !empty($b['is_available_in_range']),
            'is_booked_in_range' => !empty($b['is_booked_in_range']),
    ];
}
?>

<script>
    window.__BOATS__ = <?= json_encode($boatsForUi, JSON_UNESCAPED_UNICODE) ?>;
    window.__SLOT_META__ = <?= json_encode($slotMeta, JSON_UNESCAPED_UNICODE) ?>;

    window.__TOGGLE_LP_URL__ = <?= json_encode(site_url('/buchung/liegeplatz-toggle')) ?>;
    window.__TOGGLE_BOOT_URL__ = <?= json_encode(site_url('/buchung/boot-toggle')) ?>;
    window.__CSRF_NAME__ = <?= json_encode(csrf_token()) ?>;
    window.__CSRF_HASH__ = <?= json_encode(csrf_hash()) ?>;
    window.__SELECTED_BOATS__ = <?= json_encode($selectedBoids, JSON_UNESCAPED_UNICODE) ?>;
</script>

<script src="<?= base_url('js/hafen-app.js') ?>"></script>

<?= $this->endSection() ?>
