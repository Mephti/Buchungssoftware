<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1>Willkommen am Plauer See</h1>

<p>
    Buche bequem online einen Liegeplatz oder ein Boot.
</p>

<p>
    <a href="#buchung">
        <button>Buchung starten</button>
    </a>
</p>

<hr>

<section id="buchung">
    <h2>Buchung</h2>

    <form method="post" action="<?= site_url('/buchung/auswahl') ?>">
        <?= csrf_field() ?>

        <label>
            Von:
            <input type="date" name="von" required
                   value="<?= esc(session('booking.von') ?? '') ?>">
        </label>

        <label style="margin-left:8px;">
            Bis:
            <input type="date" name="bis" required
                   value="<?= esc(session('booking.bis') ?? '') ?>">
        </label>

        <label style="margin-left:16px;">
            <input type="radio" name="typ" value="liegeplatz"
                    <?= (session('booking.typ') ?? 'liegeplatz') === 'liegeplatz' ? 'checked' : '' ?>>
            Liegeplatz
        </label>

        <label style="margin-left:8px;">
            <input type="radio" name="typ" value="boot"
                    <?= (session('booking.typ') ?? '') === 'boot' ? 'checked' : '' ?>>
            Boot
        </label>

        <button type="submit" style="margin-left:16px;">Übernehmen</button>
    </form>

    <?php if ((session('booking.typ') ?? 'liegeplatz') === 'liegeplatz'): ?>
        <form method="post" action="<?= site_url('/buchung/filter') ?>" style="margin-top:10px;">
            <?= csrf_field() ?>

            <?php $only = (bool)(session('booking.filter.only_available') ?? false); ?>
            <?php $q = (string)(session('booking.filter.q') ?? ''); ?>

            <label>
                <input type="checkbox" name="only_available" value="1" <?= $only ? 'checked' : '' ?>>
                nur verfügbare
            </label>

            <label style="margin-left:12px;">
                Suche:
                <input name="q" placeholder="z.B. A-3 oder 3" value="<?= esc($q) ?>">
            </label>

            <button type="submit" style="margin-left:8px;">Filtern</button>
        </form>
    <?php endif; ?>

    <?php if ((session('booking.typ') ?? null) === 'boot'): ?>
        <form method="post" action="<?= site_url('/buchung/boot-filter') ?>" style="margin-top:10px;">
            <?= csrf_field() ?>
            <?php $q = (string)(session('booking.boot_filter.q') ?? ''); ?>

            <label>
                Suche:
                <input name="q" placeholder="z.B. Ruderboot oder Seerose" value="<?= esc($q) ?>">
            </label>

            <button type="submit" style="margin-left:8px;">Filtern</button>
        </form>
    <?php endif; ?>

    <?php
    $booking = session('booking') ?? [];
    $typ = $booking['typ'] ?? 'liegeplatz';
    ?>

    <!-- Hauptbereich -->
    <div style="display:flex; gap:16px; margin-top:12px;">
        <div style="flex:3; background:#eee; min-height:300px; padding:12px;">

            <?php if ($typ === 'liegeplatz'): ?>
                <h3>Liegeplätze</h3>

                <?php if (empty($liegeplaetze)): ?>
                    <p>Keine Liegeplätze gefunden. Filter anpassen oder zurücksetzen.</p>
                <?php else: ?>
                    <ul>
                        <?php
                        $selected = $booking['liegeplaetze'] ?? [];
                        $selectedInt = array_map('intval', $selected);
                        $lpModel = new \App\Models\LiegeplatzModel();
                        ?>

                        <?php foreach ($liegeplaetze as $lp): ?>
                            <?php
                            $isSelected = in_array((int)$lp['lid'], $selectedInt, true);
                            $label = $lpModel->getStatusLabel($lp['status']);
                            ?>

                            <li style="margin-bottom:6px;">
                                Anleger <?= esc($lp['anleger']) ?> – Platz <?= esc($lp['nummer']) ?>

                                <?php if (!empty($lp['is_booked_in_range'])): ?>
                                    <span class="status-badge status-im-zeitraum">im Zeitraum gebucht</span>
                                <?php else: ?>
                                    <span class="status-badge status-<?= esc($lp['status']) ?>">
                                        <?= esc($label) ?>
                                    </span>
                                <?php endif; ?>

                                <?php if (!empty($lp['is_available_in_range'])): ?>
                                    <form method="post"
                                          action="<?= site_url('/buchung/liegeplatz-toggle') ?>"
                                          style="display:inline; margin-left:8px;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="lid" value="<?= esc($lp['lid']) ?>">
                                        <button type="submit">
                                            <?= $isSelected ? 'Abwählen' : 'Auswählen' ?>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="margin-left:8px; color:#777;">
                                        <?= !empty($lp['is_booked_in_range']) ? 'im Zeitraum gebucht' : 'nicht verfügbar' ?>
                                    </span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

            <?php else: ?>
                <h3>Boote</h3>

                <?php if (empty($boote)): ?>
                    <p>Keine Boote gefunden. Filter anpassen oder zurücksetzen.</p>
                <?php else: ?>
                    <ul>
                        <?php
                        $selected = $booking['boote'] ?? [];
                        $selectedInt = array_map('intval', $selected);
                        $bootModel = new \App\Models\BootModel();
                        ?>

                        <?php foreach ($boote as $b): ?>
                            <?php
                            $isSelected = in_array((int)$b['boid'], $selectedInt, true);
                            $label = $bootModel->getStatusLabel($b['status']);
                            ?>

                            <li style="margin-bottom:6px;">
                                <?= esc($b['name']) ?>
                                <?php if (!empty($b['typ'])): ?>
                                    (<?= esc($b['typ']) ?>)
                                <?php endif; ?>

                                <?php if (!empty($b['is_booked_in_range'])): ?>
                                    <span class="status-badge status-im-zeitraum">im Zeitraum gebucht</span>
                                <?php else: ?>
                                    <span class="status-badge status-<?= esc($b['status']) ?>">
                                        <?= esc($label) ?>
                                    </span>
                                <?php endif; ?>

                                <?php if (!empty($b['is_available_in_range'])): ?>
                                    <form method="post"
                                          action="<?= site_url('/buchung/boot-toggle') ?>"
                                          style="display:inline; margin-left:8px;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="boid" value="<?= esc($b['boid']) ?>">
                                        <button type="submit">
                                            <?= $isSelected ? 'Abwählen' : 'Auswählen' ?>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="margin-left:8px; color:#777;">
                                        <?= !empty($b['is_booked_in_range']) ? 'im Zeitraum gebucht' : 'nicht verfügbar' ?>
                                    </span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>

        </div>

        <div style="flex:1; border:1px solid #ccc; padding:12px;">
            <h3>Ihre Buchung</h3>

            <?php if (!empty($booking)): ?>
                <p>
                    <strong>Von:</strong> <?= esc($booking['von'] ?? '') ?><br>
                    <strong>Bis:</strong> <?= esc($booking['bis'] ?? '') ?><br>
                    <strong>Typ:</strong> <?= esc($booking['typ'] ?? '') ?>
                </p>

                <?php if (($booking['typ'] ?? null) === 'liegeplatz'): ?>
                    <?php if (!empty($selectedLiegeplaetze)): ?>
                        <p><strong>Ausgewählte Liegeplätze:</strong></p>
                        <ul>
                            <?php foreach ($selectedLiegeplaetze as $lp): ?>
                                <li>Anleger <?= esc($lp['anleger']) ?> – Platz <?= esc($lp['nummer']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (($booking['typ'] ?? null) === 'boot'): ?>
                    <?php if (!empty($selectedBoote)): ?>
                        <p><strong>Ausgewählte Boote:</strong></p>
                        <ul>
                            <?php foreach ($selectedBoote as $b): ?>
                                <li><?= esc($b['name']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>

                <?php
                $hasSelection = false;
                if (($booking['typ'] ?? null) === 'liegeplatz') {
                    $hasSelection = !empty($booking['liegeplaetze'] ?? []);
                } elseif (($booking['typ'] ?? null) === 'boot') {
                    $hasSelection = !empty($booking['boote'] ?? []);
                }
                ?>

                <form method="get" action="<?= site_url('/buchung/zusammenfassung') ?>">
                    <button type="submit" <?= $hasSelection ? '' : 'disabled' ?>>
                        Weiter
                    </button>
                </form>

                <?php if (!$hasSelection): ?>
                    <p style="color:#777; font-size:0.9em;">
                        Bitte mindestens <?= ($booking['typ'] ?? '') === 'boot' ? 'ein Boot' : 'einen Liegeplatz' ?> auswählen.
                    </p>
                <?php endif; ?>

                <form method="post" action="<?= site_url('/buchung/reset') ?>" style="display:inline;">
                    <?= csrf_field() ?>
                    <button type="submit" style="margin-left:8px;">Zurücksetzen</button>
                </form>

            <?php else: ?>
                <p>Bitte Zeitraum und Typ wählen.</p>
                <button disabled>Weiter</button>
            <?php endif; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>