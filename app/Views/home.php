<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$booking = session('booking') ?? [];
$typ = $booking['typ'] ?? 'liegeplatz';
?>

<section class="platzhalter">
    <div class="platzhalter__image" aria-label="Hafenansicht">
        <img src="<?= base_url('img/cover.webp') ?>" alt="Hafen am Plauer See"
             onerror="this.style.display='none'; this.parentElement.style.minHeight='320px';">
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

<h2 class="section-title" id="buchung">Buchung</h2>

<!-- Zeitraum + Typ -->
<form method="post" action="<?= site_url('/buchung/auswahl') ?>">
    <?= csrf_field() ?>

    <div class="booking__controls">
        <div class="field">
            <label for="von">Von</label>
            <input class="input" type="date" id="von" name="von" required
                   value="<?= esc($booking['von'] ?? '') ?>">
        </div>

        <div class="field">
            <label for="bis">Bis</label>
            <input class="input" type="date" id="bis" name="bis" required
                   value="<?= esc($booking['bis'] ?? '') ?>">
        </div>

        <button class="btn" type="submit">Datum übernehmen</button>
    </div>

    <div class="tabs" role="tablist" aria-label="Buchungstyp">
        <?php $current = ($booking['typ'] ?? 'liegeplatz'); ?>
        <label class="<?= $current === 'liegeplatz' ? 'active' : '' ?>">
            <input type="radio" name="typ" value="liegeplatz" <?= $current === 'liegeplatz' ? 'checked' : '' ?>>
            Liegeplatz
        </label>

        <label class="<?= $current === 'boot' ? 'active' : '' ?>">
            <input type="radio" name="typ" value="boot" <?= $current === 'boot' ? 'checked' : '' ?>>
            Boot
        </label>
    </div>
</form>

<!-- Filter -->
<?php if (($booking['typ'] ?? 'liegeplatz') === 'liegeplatz'): ?>
    <form method="post" action="<?= site_url('/buchung/filter') ?>" style="margin: 10px 0 14px;">
        <?= csrf_field() ?>
        <?php $only = (bool)($booking['filter']['only_available'] ?? false); ?>
        <?php $q = (string)($booking['filter']['q'] ?? ''); ?>

        <div class="booking__controls" style="justify-content:flex-start;">
            <label style="display:flex; align-items:center; gap:8px;">
                <input type="checkbox" name="only_available" value="1" <?= $only ? 'checked' : '' ?>>
                nur verfügbare
            </label>

            <div class="field" style="min-width:240px;">
                <label for="q_lp">Suche</label>
                <input class="input" id="q_lp" name="q" placeholder="z.B. A-3 oder 3" value="<?= esc($q) ?>">
            </div>

            <button class="btn" type="submit">Filtern</button>
        </div>
    </form>
<?php endif; ?>

<?php if (($booking['typ'] ?? null) === 'boot'): ?>
    <form method="post" action="<?= site_url('/buchung/boot-filter') ?>" style="margin: 10px 0 14px;">
        <?= csrf_field() ?>
        <?php $q = (string)($booking['boot_filter']['q'] ?? ''); ?>

        <div class="booking__controls" style="justify-content:flex-start;">
            <div class="field" style="min-width:260px;">
                <label for="q_b">Suche</label>
                <input class="input" id="q_b" name="q" placeholder="z.B. Ruderboot oder Seerose" value="<?= esc($q) ?>">
            </div>

            <button class="btn" type="submit">Filtern</button>
        </div>
    </form>
<?php endif; ?>

<div class="booking">
    <div class="card" style="padding:16px;">
        <div id="harbor-app"
             data-typ="<?= esc($typ) ?>"
             data-von="<?= esc($booking['von'] ?? '') ?>"
             data-bis="<?= esc($booking['bis'] ?? '') ?>">
        </div>

        <!-- Optionaler Fallback: aktuelle Listenansicht -->
        <div style="margin-top:14px;">
            <?php if ($typ === 'liegeplatz'): ?>
                <h3 style="margin:0 0 10px;">Liegeplätze (Liste)</h3>

                <?php if (empty($liegeplaetze)): ?>
                    <p class="muted">Keine Liegeplätze gefunden.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Anleger</th>
                            <th>Nr.</th>
                            <th>Status</th>
                            <th>Aktion</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $selected = $booking['liegeplaetze'] ?? [];
                        $selectedInt = array_map('intval', $selected);
                        $lpModel = new \App\Models\LiegeplatzModel();
                        ?>
                        <?php foreach ($liegeplaetze as $lp): ?>
                            <?php
                            $isSelected = in_array((int)$lp['lid'], $selectedInt, true);
                            $label = $lpModel->getStatusLabel($lp['status']);

                            $badgeClass = match ($lp['status']) {
                                'verfuegbar' => 'badge--green',
                                'gesperrt' => 'badge--red',
                                'vermietet' => 'badge--orange',
                                'belegt' => 'badge--blue',
                                default => 'badge--gray',
                            };
                            ?>
                            <tr>
                                <td><?= esc($lp['anleger']) ?></td>
                                <td><?= esc($lp['nummer']) ?></td>
                                <td>
                                    <?php if (!empty($lp['is_booked_in_range'])): ?>
                                        <span class="badge badge--purple">im Zeitraum</span>
                                    <?php else: ?>
                                        <span class="badge <?= esc($badgeClass) ?>"><?= esc($label) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($lp['is_available_in_range'])): ?>
                                        <form method="post" action="<?= site_url('/buchung/liegeplatz-toggle') ?>" style="margin:0;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="lid" value="<?= esc($lp['lid']) ?>">
                                            <button class="btn" type="submit" style="padding:8px 10px; border-radius:10px;">
                                                <?= $isSelected ? 'Abwählen' : 'Auswählen' ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="muted">nicht verfügbar</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

            <?php else: ?>
                <h3 style="margin:0 0 10px;">Boote (Liste)</h3>

                <?php if (empty($boote)): ?>
                    <p class="muted">Keine Boote gefunden.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Typ</th>
                            <th>Status</th>
                            <th>Aktion</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $selected = $booking['boote'] ?? [];
                        $selectedInt = array_map('intval', $selected);
                        $bootModel = new \App\Models\BootModel();
                        ?>
                        <?php foreach ($boote as $b): ?>
                            <?php
                            $isSelected = in_array((int)$b['boid'], $selectedInt, true);
                            $label = $bootModel->getStatusLabel($b['status']);

                            $badgeClass = match ($b['status']) {
                                'verfuegbar' => 'badge--green',
                                'gesperrt' => 'badge--red',
                                'wartung' => 'badge--orange',
                                'unterwegs' => 'badge--blue',
                                default => 'badge--gray',
                            };
                            ?>
                            <tr>
                                <td><?= esc($b['name']) ?></td>
                                <td><?= esc($b['typ'] ?? '') ?></td>
                                <td>
                                    <?php if (!empty($b['is_booked_in_range'])): ?>
                                        <span class="badge badge--purple">im Zeitraum</span>
                                    <?php else: ?>
                                        <span class="badge <?= esc($badgeClass) ?>"><?= esc($label) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($b['is_available_in_range'])): ?>
                                        <form method="post" action="<?= site_url('/buchung/boot-toggle') ?>" style="margin:0;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="boid" value="<?= esc($b['boid']) ?>">
                                            <button class="btn" type="submit" style="padding:8px 10px; border-radius:10px;">
                                                <?= $isSelected ? 'Abwählen' : 'Auswählen' ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="muted">nicht verfügbar</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar Ihre Buchung -->
    <aside class="card sidebar">
        <?php
        $booking = session('booking') ?? [];

        $von = $booking['von'] ?? '';
        $bis = $booking['bis'] ?? '';

        $selectedLids = array_map('intval', $booking['selected_lids'] ?? []);
        $selectedBoids = array_map('intval', $booking['selected_boids'] ?? []);
        $assignments = $booking['assignments'] ?? [];

        $lpById = [];
        foreach (($liegeplaetze ?? []) as $lp) $lpById[(int)$lp['lid']] = $lp;

        $bootById = [];
        foreach (($boote ?? []) as $b) $bootById[(int)$b['boid']] = $b;

        $hasSelection = !empty($selectedLids) || !empty($selectedBoids) || !empty($assignments);
        ?>

        <h3>Ihre Buchung</h3>

        <?php if (!empty($booking) && $von && $bis): ?>
            <div class="muted">
                Zeitraum: <strong><?= esc($von) ?></strong> bis <strong><?= esc($bis) ?></strong>
            </div>

            <hr class="hr">

            <h4 style="margin:0 0 8px;">Kombinationen (Boot → Liegeplatz)</h4>
            <?php if (empty($assignments)): ?>
                <p class="muted">Keine Kombinationen.</p>
            <?php else: ?>
                <ul style="margin:0 0 12px; padding-left:18px;">
                    <?php foreach ($assignments as $lidStr => $boidVal): ?>
                        <?php
                        $lid = (int)$lidStr;
                        $boid = (int)$boidVal;
                        $lp = $lpById[$lid] ?? null;
                        $bt = $bootById[$boid] ?? null;
                        ?>
                        <li>
                            <?= $lp ? 'Liegeplatz ' . esc($lp['anleger']) . '-' . esc($lp['nummer']) : 'Liegeplatz #' . esc($lid) ?>
                            ↔
                            <?= $bt ? 'Boot ' . esc($bt['name']) : 'Boot #' . esc($boid) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <h4 style="margin:0 0 8px;">Einzel-Liegeplätze</h4>
            <?php
            $pairedLids = array_map('intval', array_keys($assignments));
            $singleLids = array_values(array_diff($selectedLids, $pairedLids));
            ?>
            <?php if (empty($singleLids)): ?>
                <p class="muted">Keine.</p>
            <?php else: ?>
                <ul style="margin:0 0 12px; padding-left:18px;">
                    <?php foreach ($singleLids as $lid): ?>
                        <?php $lp = $lpById[$lid] ?? null; ?>
                        <li><?= $lp ? 'Liegeplatz ' . esc($lp['anleger']) . '-' . esc($lp['nummer']) : 'Liegeplatz #' . esc($lid) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <h4 style="margin:0 0 8px;">Einzel-Boote</h4>
            <?php
            $pairedBoids = array_map('intval', array_values($assignments));
            $singleBoids = array_values(array_diff($selectedBoids, $pairedBoids));
            ?>
            <?php if (empty($singleBoids)): ?>
                <p class="muted">Keine.</p>
            <?php else: ?>
                <ul style="margin:0 0 12px; padding-left:18px;">
                    <?php foreach ($singleBoids as $boid): ?>
                        <?php $bt = $bootById[$boid] ?? null; ?>
                        <li><?= $bt ? 'Boot ' . esc($bt['name']) : 'Boot #' . esc($boid) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div style="margin-top:14px;">
                <form method="get" action="<?= site_url('/buchung/zusammenfassung') ?>" style="margin:0;">
                    <button class="btn btn--block" type="submit" <?= $hasSelection ? '' : 'disabled' ?>>
                        Weiter →
                    </button>
                </form>

                <?php if (!$hasSelection): ?>
                    <p class="muted" style="margin-top:10px; font-size:0.9em;">
                        Bitte mindestens ein Element auswählen.
                    </p>
                <?php endif; ?>

                <form method="post" action="<?= site_url('/buchung/reset') ?>" style="margin-top:10px;">
                    <?= csrf_field() ?>
                    <button class="btn btn--block" type="submit">Zurücksetzen</button>
                </form>
            </div>

        <?php else: ?>
            <div class="muted">Bitte Zeitraum wählen.</div>
            <div style="margin-top:12px;">
                <button class="btn btn--block" disabled>Weiter →</button>
            </div>
        <?php endif; ?>
    </aside>
</div>

<!-- NUR EINMAL, und exakt die Keys, die harbor-app.js nutzt -->
<script>
    window.__HARBOR_DATA__ = {
        liegeplaetze: <?= json_encode($liegeplaetze ?? []) ?>,
        boote: <?= json_encode($boote ?? []) ?>,
        selected_lids: <?= json_encode(session('booking.selected_lids') ?? []) ?>,
        selected_boids: <?= json_encode(session('booking.selected_boids') ?? []) ?>,
        assignments: <?= json_encode(session('booking.assignments') ?? new stdClass()) ?>,
    };
</script>

<?= $this->endSection() ?>