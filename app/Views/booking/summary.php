<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Buchung – Zusammenfassung</h1>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= esc($error) ?></p>
<?php endif; ?>

<p>
    <strong>Zeitraum:</strong> <?= esc($von) ?> bis <?= esc($bis) ?><br>
    <strong>Typ:</strong> <?= esc($typ) ?>
</p>

<?php if ($typ === 'liegeplatz'): ?>
    <h3>Ausgewählte Liegeplätze</h3>
    <?php if (empty($items)): ?>
        <p>Keine Liegeplätze ausgewählt.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($items as $lp): ?>
                <li>
                    Anleger <?= esc($lp['anleger']) ?> – Platz <?= esc($lp['nummer']) ?>
                    · Kosten/Tag: <?= esc($lp['kosten_pt'] ?? 0) ?>
                    <?php if (!empty($daysCount)): ?>
                        · Gesamt: <?= esc($lp['kosten_total'] ?? 0) ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php endif; ?>

<?php if (!empty($selectedBoote)): ?>
    <h3>Ausgewählte Boote</h3>
    <ul>
        <?php foreach ($selectedBoote as $b): ?>
            <li>
                <?= esc($b['name']) ?><?= !empty($b['typ']) ? ' (' . esc($b['typ']) . ')' : '' ?>
                · Kosten/Tag: <?= esc($b['kosten_pt'] ?? 0) ?>
                <?php if (!empty($daysCount)): ?>
                    · Gesamt: <?= esc($b['kosten_total'] ?? 0) ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if ($typ === 'boot'): ?>
    <h3>Ausgewählte Boote</h3>
    <?php if (empty($items)): ?>
        <p>Keine Boote ausgewählt.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($items as $b): ?>
                <li>
                    <?= esc($b['name']) ?><?= !empty($b['typ']) ? ' (' . esc($b['typ']) . ')' : '' ?>
                    · Kosten/Tag: <?= esc($b['kosten_pt'] ?? 0) ?>
                    <?php if (!empty($daysCount)): ?>
                        · Gesamt: <?= esc($b['kosten_total'] ?? 0) ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php endif; ?>

<?php if (!empty($daysCount)): ?>
    <h3>Kosten</h3>
    <p>
        Liegeplatz gesamt (<?= esc($daysCount) ?> <?= $daysCount === 1 ? 'Tag' : 'Tage' ?>):
        <?= esc($kostenLiegeplatzTotal ?? 0) ?>
    </p>
    <p>
        Boot gesamt (<?= esc($daysCount) ?> <?= $daysCount === 1 ? 'Tag' : 'Tage' ?>):
        <?= esc($kostenBootTotal ?? 0) ?>
    </p>
<?php endif; ?>

<form method="post" action="<?= site_url('/buchung/abschliessen') ?>">
    <?= csrf_field() ?>
    <button type="submit">Buchung abschließen</button>
</form>

<p style="margin-top:12px;">
    <a href="<?= site_url('/#buchung') ?>">Zurück</a>
</p>

<?= $this->endSection() ?>
