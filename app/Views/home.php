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

    <!-- Hauptbereich -->
    <div style="display:flex; gap:16px; margin-top:12px;">
        <div style="flex:3; background:#eee; min-height:300px; padding:12px;">
            <?php if ((session('booking.typ') ?? 'liegeplatz') === 'liegeplatz'): ?>
                <h3>Liegeplätze</h3>

                <?php if (empty($liegeplaetze)): ?>
                    <p>Keine Liegeplätze gefunden. Filter anpassen oder zurücksetzen.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($liegeplaetze as $lp): ?>
                            <?php
                            $selected = session('booking.liegeplaetze') ?? [];
                            $isSelected = in_array($lp['lid'], $selected, true);
                            $label = (new \App\Models\LiegeplatzModel())->getStatusLabel($lp['status']);
                            ?>

                            <?php
                            $booking  = session('booking') ?? [];
                            $selected = $booking['liegeplaetze'] ?? [];
                            $isSelected = in_array((int)$lp['lid'], array_map('intval', $selected), true);
                            $label = (new \App\Models\LiegeplatzModel())->getStatusLabel($lp['status']);
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
                                        <?php if (!empty($lp['is_booked_in_range'])): ?>
                                            im Zeitraum gebucht
                                        <?php else: ?>
                                            nicht verfügbar
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>

                            </li>


                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

            <?php else: ?>
                <p>Bootsauswahl kommt als nächstes.</p>
            <?php endif; ?>
        </div>

        <div style="flex:1; border:1px solid #ccc; padding:12px;">
            <h3>Ihre Buchung</h3>

            <?php if (session('booking')): ?>
                <p>
                    <strong>Von:</strong> <?= esc(session('booking.von')) ?><br>
                    <strong>Bis:</strong> <?= esc(session('booking.bis')) ?><br>
                    <strong>Typ:</strong> <?= esc(session('booking.typ')) ?>
                </p>

                <?php if ((session('booking.typ') ?? null) === 'liegeplatz'): ?>

                    <?php if (!empty($selectedLiegeplaetze)): ?>
                        <p><strong>Ausgewählte Liegeplätze:</strong></p>
                        <ul>
                            <?php foreach ($selectedLiegeplaetze as $lp): ?>
                                <li>
                                    Anleger <?= esc($lp['anleger']) ?> – Platz <?= esc($lp['nummer']) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                <?php endif; ?>

                <?php
                $hasSelection = !empty(session('booking.liegeplaetze') ?? []);
                ?>

                <form method="get" action="<?= site_url('/buchung/zusammenfassung') ?>">
                    <button type="submit" <?= $hasSelection ? '' : 'disabled' ?>>
                        Weiter
                    </button>
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
                <p>Bitte Zeitraum und Typ wählen.</p>
                <button disabled>Weiter</button>
            <?php endif; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>