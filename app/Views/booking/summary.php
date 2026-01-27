<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Buchung – Zusammenfassung</h1>

<?php if (!empty($error)): ?>
    <p style="color:#c62828; font-weight:700;">
        <?= esc($error) ?>
    </p>
<?php endif; ?>

<p>
    <strong>Zeitraum:</strong>
    <?= esc($von) ?> bis <?= esc($bis) ?>
</p>

<hr>

<?php
// Hilfsstrukturen
$pairedLids = array_keys($assignments ?? []);
$pairedBoids = array_values($assignments ?? []);

// Map boid => lid
$boidToLid = [];
foreach (($assignments ?? []) as $lid => $boid) {
    $boidToLid[(int)$boid] = (int)$lid;
}
?>

<h2>Gebuchte Kombinationen</h2>

<?php if (empty($assignments)): ?>
    <p style="color:#555;">Keine Boot–Liegeplatz-Kombinationen.</p>
<?php else: ?>
    <ul>
        <?php foreach ($assignments as $lid => $boid): ?>
            <?php
            $lp = null;
            $bt = null;

            foreach ($liegeplaetze as $l) {
                if ((int)$l['lid'] === (int)$lid) { $lp = $l; break; }
            }
            foreach ($boote as $b) {
                if ((int)$b['boid'] === (int)$boid) { $bt = $b; break; }
            }
            ?>
            <li style="margin-bottom:6px;">
                <strong>
                    Liegeplatz <?= esc($lp['anleger'] ?? '?') ?>-<?= esc($lp['nummer'] ?? '?') ?>
                </strong>
                ↔
                <strong>
                    Boot <?= esc($bt['name'] ?? 'Unbekannt') ?>
                </strong>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<hr>

<h2>Einzeln gebuchte Liegeplätze</h2>

<?php
$singleLps = array_filter($liegeplaetze, fn($lp) =>
!in_array((int)$lp['lid'], $pairedLids, true)
);
?>

<?php if (empty($singleLps)): ?>
    <p style="color:#555;">Keine einzelnen Liegeplätze.</p>
<?php else: ?>
    <ul>
        <?php foreach ($singleLps as $lp): ?>
            <li>
                Anleger <?= esc($lp['anleger']) ?> – Platz <?= esc($lp['nummer']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<hr>

<h2>Einzeln gebuchte Boote</h2>

<?php
$singleBoats = array_filter($boote, fn($b) =>
!in_array((int)$b['boid'], $pairedBoids, true)
);
?>

<?php if (empty($singleBoats)): ?>
    <p style="color:#555;">Keine einzelnen Boote.</p>
<?php else: ?>
    <ul>
        <?php foreach ($singleBoats as $b): ?>
            <li>
                <?= esc($b['name']) ?>
                <?= !empty($b['typ']) ? ' (' . esc($b['typ']) . ')' : '' ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<hr>

<form method="post" action="<?= site_url('/buchung/abschliessen') ?>">
    <?= csrf_field() ?>
    <button type="submit" style="font-size:1.1em; padding:10px 16px;">
        Buchung abschließen
    </button>
</form>

<p style="margin-top:12px;">
    <a href="<?= site_url('/#buchung') ?>">Zurück zur Auswahl</a>
</p>

<?= $this->endSection() ?>
