<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Meine Buchungen</h1>
<?php if ($success = session()->getFlashdata('success')): ?>
    <p style="color:green;"><?= esc($success) ?></p>
<?php endif; ?>

<h2>Liegeplätze</h2>
<?php if (empty($liegeplatzBuchungen)): ?>
    <p>Keine Liegeplatz-Buchungen vorhanden.</p>
<?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
        <tr>
            <th>ID</th>
            <th>Anleger</th>
            <th>Nummer</th>
            <th>Von</th>
            <th>Bis</th>
            <th>Kosten</th>
            <th>Status</th>
            <th>Aktion</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($liegeplatzBuchungen as $b): ?>
            <tr>
                <td><?= esc($b['bid']) ?></td>
                <td><?= esc($b['anleger']) ?></td>
                <td><?= esc($b['nummer']) ?></td>
                <td><?= esc($b['von']) ?></td>
                <td><?= esc($b['bis']) ?></td>
                <td><?= esc($b['kosten'] ?? 0) ?></td>
                <td><?= esc($b['status']) ?></td>
                <td>
                    <?php if (($b['status'] ?? '') !== 'storniert'): ?>
                        <form method="post" action="<?= site_url('meine-buchungen/storno') ?>" style="display:inline;">
                            <input type="hidden" name="type" value="liegeplatz">
                            <input type="hidden" name="id" value="<?= esc($b['bid']) ?>">
                            <button type="submit" onclick="return confirm('Diese Liegeplatz-Buchung wirklich stornieren?')">
                                Stornieren
                            </button>
                        </form>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<hr>

<h2>Boote</h2>
<?php if (empty($bootBuchungen)): ?>
    <p>Keine Boot-Buchungen vorhanden.</p>
<?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Typ</th>
            <th>Plätze</th>
            <th>Von</th>
            <th>Bis</th>
            <th>Kosten</th>
            <th>Status</th>
            <th>Aktion</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($bootBuchungen as $b): ?>
            <tr>
                <td><?= esc($b['bbid']) ?></td>
                <td><?= esc($b['name']) ?></td>
                <td><?= esc($b['typ']) ?></td>
                <td><?= esc($b['plaetze']) ?></td>
                <td><?= esc($b['von']) ?></td>
                <td><?= esc($b['bis']) ?></td>
                <td><?= esc($b['kosten'] ?? 0) ?></td>
                <td><?= esc($b['status']) ?></td>
                <td>
                    <?php if (($b['status'] ?? '') !== 'storniert'): ?>
                        <form method="post" action="<?= site_url('meine-buchungen/storno') ?>" style="display:inline;">
                            <input type="hidden" name="type" value="boot">
                            <input type="hidden" name="id" value="<?= esc($b['bbid']) ?>">
                            <button type="submit" onclick="return confirm('Diese Boot-Buchung wirklich stornieren?')">
                                Stornieren
                            </button>
                        </form>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p>
    <a href="<?= site_url('/') ?>">Zur Startseite</a> |
    <a href="<?= site_url('/mein-konto') ?>">Mein Konto</a>
</p>

<?= $this->endSection() ?>
