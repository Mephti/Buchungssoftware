<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Mitarbeiterbereich</h1>

<p>
    <a class="btn" href="<?= site_url('/mitarbeiter/buchung') ?>">Buchung anlegen</a>
</p>

<div class="card" style="padding:16px; margin-bottom:16px;">
    <h3>Boot anlegen</h3>
    <form method="post" action="<?= site_url('mitarbeiter/boote/anlegen') ?>">
        <?= csrf_field() ?>
        <div class="booking__controls">
            <div class="field">
                <label>Name*</label>
                <input type="text" name="name" required>
            </div>
            <div class="field">
                <label>Typ</label>
                <input type="text" name="typ">
            </div>
            <div class="field">
                <label>Plätze*</label>
                <input type="number" name="plaetze" min="1" required>
            </div>
            <div class="field">
                <label>Status*</label>
                <select name="status" required>
                    <option value="verfuegbar">verfügbar</option>
                    <option value="gesperrt">gesperrt</option>
                    <option value="wartung">wartung</option>
                    <option value="unterwegs">unterwegs</option>
                </select>
            </div>
            <div class="field">
                <label>Kosten/Tag</label>
                <input type="number" name="kosten_pt" min="0" step="1">
            </div>
            <div style="align-self:flex-end;">
                <button type="submit">Boot anlegen</button>
            </div>
        </div>
    </form>
</div>

<form method="get" action="<?= site_url('/mitarbeiter') ?>" style="margin-bottom:12px;">
    <label style="font-weight:700;">
        <input type="checkbox" name="only_active" value="1" <?= !empty($onlyActive) ? 'checked' : '' ?>>
        Nur aktive Buchungen anzeigen
    </label>
    <button type="submit" style="margin-left:8px;">Filtern</button>
</form>

<?php if (!empty($success)): ?>
    <p style="color:green;"><?= esc($success) ?></p>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <p style="color:red;"><?= esc($error) ?></p>
<?php endif; ?>

<h2>Liegeplatz-Buchungen</h2>
<?php if (empty($liegeplatzBuchungen)): ?>
    <p>Keine Liegeplatz-Buchungen vorhanden.</p>
<?php else: ?>
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Kunde</th>
            <th>E-Mail</th>
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
                <td><?= esc(($b['vorname'] ?? '') . ' ' . ($b['nachname'] ?? '')) ?></td>
                <td><?= esc($b['email'] ?? '') ?></td>
                <td><?= esc($b['anleger']) ?></td>
                <td><?= esc($b['nummer']) ?></td>
                <td><?= esc($b['von']) ?></td>
                <td><?= esc($b['bis']) ?></td>
                <td><?= esc($b['kosten'] ?? 0) ?></td>
                <td><?= esc($b['status']) ?></td>
                <td>
                    <?php if (($b['status'] ?? '') !== 'storniert'): ?>
                        <form method="post" action="<?= site_url('mitarbeiter/buchungen/storno') ?>" style="display:inline;">
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

<hr class="hr">

<h2>Liegeplatz-Status ändern</h2>
<?php if (empty($liegeplaetze)): ?>
    <p>Keine Liegeplätze vorhanden.</p>
<?php else: ?>
    <table class="table">
        <thead>
        <tr>
            <th>LID</th>
            <th>Anleger</th>
            <th>Nummer</th>
            <th>Aktueller Status</th>
            <th>Neuer Status</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($liegeplaetze as $lp): ?>
            <tr>
                <td><?= esc($lp['lid']) ?></td>
                <td><?= esc($lp['anleger']) ?></td>
                <td><?= esc($lp['nummer']) ?></td>
                <td><?= esc($lp['status']) ?></td>
                <td>
                    <form method="post" action="<?= site_url('mitarbeiter/status') ?>" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="type" value="liegeplatz">
                        <input type="hidden" name="id" value="<?= esc($lp['lid']) ?>">
                        <select name="status">
                            <?php foreach (['verfuegbar','gesperrt','vermietet','belegt'] as $st): ?>
                                <option value="<?= esc($st) ?>" <?= $st === ($lp['status'] ?? '') ? 'selected' : '' ?>><?= esc($st) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" style="margin-left:8px;">Speichern</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<hr class="hr">

<h2>Boot-Buchungen</h2>
<?php if (empty($bootBuchungen)): ?>
    <p>Keine Boot-Buchungen vorhanden.</p>
<?php else: ?>
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Kunde</th>
            <th>E-Mail</th>
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
                <td><?= esc(($b['vorname'] ?? '') . ' ' . ($b['nachname'] ?? '')) ?></td>
                <td><?= esc($b['email'] ?? '') ?></td>
                <td><?= esc($b['name']) ?></td>
                <td><?= esc($b['typ']) ?></td>
                <td><?= esc($b['plaetze']) ?></td>
                <td><?= esc($b['von']) ?></td>
                <td><?= esc($b['bis']) ?></td>
                <td><?= esc($b['kosten'] ?? 0) ?></td>
                <td><?= esc($b['status']) ?></td>
                <td>
                    <?php if (($b['status'] ?? '') !== 'storniert'): ?>
                        <form method="post" action="<?= site_url('mitarbeiter/buchungen/storno') ?>" style="display:inline;">
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

<hr class="hr">

<h2>Boot-Status ändern</h2>
<?php if (empty($boote)): ?>
    <p>Keine Boote vorhanden.</p>
<?php else: ?>
    <table class="table">
        <thead>
        <tr>
            <th>BOID</th>
            <th>Name</th>
            <th>Typ</th>
            <th>Plätze</th>
            <th>Aktueller Status</th>
            <th>Neuer Status</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($boote as $b): ?>
            <tr>
                <td><?= esc($b['boid']) ?></td>
                <td><?= esc($b['name']) ?></td>
                <td><?= esc($b['typ']) ?></td>
                <td><?= esc($b['plaetze']) ?></td>
                <td><?= esc($b['status']) ?></td>
                <td>
                    <form method="post" action="<?= site_url('mitarbeiter/status') ?>" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="type" value="boot">
                        <input type="hidden" name="id" value="<?= esc($b['boid']) ?>">
                        <select name="status">
                            <?php foreach (['verfuegbar','gesperrt','wartung','unterwegs'] as $st): ?>
                                <option value="<?= esc($st) ?>" <?= $st === ($b['status'] ?? '') ? 'selected' : '' ?>><?= esc($st) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" style="margin-left:8px;">Speichern</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?= $this->endSection() ?>
