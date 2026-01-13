<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Buchung – Zusammenfassung</h1>

<?php if ($error = session()->getFlashdata('error')): ?>
    <p style="color:red;"><?= esc($error) ?></p>
<?php endif; ?>

<p>
    <strong>Zeitraum:</strong> <?= esc($von) ?> bis <?= esc($bis) ?>
</p>

<h3>Ausgewählte Liegeplätze</h3>
<?php if (empty($selectedLiegeplaetze)): ?>
    <p>Keine Liegeplätze ausgewählt.</p>
<?php else: ?>
    <ul>
        <?php foreach ($selectedLiegeplaetze as $lp): ?>
            <li>Anleger <?= esc($lp['anleger']) ?> – Platz <?= esc($lp['nummer']) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post" action="<?= site_url('/buchung/abschliessen') ?>">
    <?= csrf_field() ?>
    <button type="submit">Buchung abschließen</button>
</form>

<p style="margin-top:12px;">
    <a href="<?= site_url('/#buchung') ?>">Zurück</a>
</p>

<?= $this->endSection() ?>