<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1 style="margin:0 0 14px;">Meine Buchungen</h1>

<?php if ($success = session()->getFlashdata('success')): ?>
    <div class="alert alert--success"><?= esc($success) ?></div>
<?php endif; ?>

<div class="card" style="padding:16px; margin-bottom:16px;">
    <h2 style="margin:0 0 12px; font-size:18px;">Liegeplätze</h2>

    <?php if (empty($liegeplatzBuchungen)): ?>
        <p class="muted">Keine Liegeplatz-Buchungen vorhanden.</p>
    <?php else: ?>
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Anleger</th>
                <th>Nummer</th>
                <th>Von</th>
                <th>Bis</th>
                <th>Status</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($liegeplatzBuchungen as $b): ?>
                <?php
                $badge = (($b['status'] ?? '') === 'storniert') ? 'badge--gray' : 'badge--green';
                ?>
                <tr>
                    <td><?= esc($b['bid']) ?></td>
                    <td><?= esc($b['anleger']) ?></td>
                    <td><?= esc($b['nummer']) ?></td>
                    <td><?= esc($b['von']) ?></td>
                    <td><?= esc($b['bis']) ?></td>
                    <td><span class="badge <?= esc($badge) ?>"><?= esc($b['status']) ?></span></td>
                    <td>
                        <?php if (($b['status'] ?? '') !== 'storniert'): ?>
                            <form method="post" action="<?= site_url('meine-buchungen/storno') ?>" style="margin:0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="type" value="liegeplatz">
                                <input type="hidden" name="id" value="<?= esc($b['bid']) ?>">
                                <button class="btn" type="submit"
                                        style="padding:8px 10px; border-radius:10px;"
                                        onclick="return confirm('Diese Liegeplatz-Buchung wirklich stornieren?')">
                                    Stornieren
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="muted">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card" style="padding:16px;">
    <h2 style="margin:0 0 12px; font-size:18px;">Boote</h2>

    <?php if (empty($bootBuchungen)): ?>
        <p class="muted">Keine Boot-Buchungen vorhanden.</p>
    <?php else: ?>
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Typ</th>
                <th>Plätze</th>
                <th>Von</th>
                <th>Bis</th>
                <th>Status</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($bootBuchungen as $b): ?>
                <?php
                $badge = (($b['status'] ?? '') === 'storniert') ? 'badge--gray' : 'badge--green';
                ?>
                <tr>
                    <td><?= esc($b['bbid']) ?></td>
                    <td><?= esc($b['name']) ?></td>
                    <td><?= esc($b['typ']) ?></td>
                    <td><?= esc($b['plaetze']) ?></td>
                    <td><?= esc($b['von']) ?></td>
                    <td><?= esc($b['bis']) ?></td>
                    <td><span class="badge <?= esc($badge) ?>"><?= esc($b['status']) ?></span></td>
                    <td>
                        <?php if (($b['status'] ?? '') !== 'storniert'): ?>
                            <form method="post" action="<?= site_url('meine-buchungen/storno') ?>" style="margin:0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="type" value="boot">
                                <input type="hidden" name="id" value="<?= esc($b['bbid']) ?>">
                                <button class="btn" type="submit"
                                        style="padding:8px 10px; border-radius:10px;"
                                        onclick="return confirm('Diese Boot-Buchung wirklich stornieren?')">
                                    Stornieren
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="muted">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
    <a class="btn" href="<?= site_url('/') ?>">Zur Startseite</a>
    <a class="btn" href="<?= site_url('/mein-konto') ?>">Mein Konto</a>
</div>

<?= $this->endSection() ?>