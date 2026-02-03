<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Buchung anlegen (Mitarbeiter)</h1>

<?php if (!empty($success)): ?>
    <p style="color:green;"><?= esc($success) ?></p>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <p style="color:red;"><?= esc($error) ?></p>
<?php endif; ?>

<form method="get" action="<?= site_url('/mitarbeiter/buchung') ?>" class="card" style="padding:16px;">
    <div class="booking__controls">
        <div class="field">
            <label>Von</label>
            <input type="date" name="von" required value="<?= esc($von ?? '') ?>">
        </div>
        <div class="field">
            <label>Bis</label>
            <input type="date" name="bis" required value="<?= esc($bis ?? '') ?>">
        </div>
        <div class="field">
            <label>Typ</label>
            <select name="typ" required>
                <option value="liegeplatz" <?= ($typ ?? '') === 'liegeplatz' ? 'selected' : '' ?>>Liegeplatz</option>
                <option value="boot" <?= ($typ ?? '') === 'boot' ? 'selected' : '' ?>>Boot</option>
            </select>
        </div>
        <div>
            <button type="submit">Verfügbarkeit laden</button>
        </div>
    </div>
</form>

<form method="post" action="<?= site_url('/mitarbeiter/buchung') ?>" style="margin-top:16px;">
    <?= csrf_field() ?>
    <input type="hidden" name="von" value="<?= esc($von ?? '') ?>">
    <input type="hidden" name="bis" value="<?= esc($bis ?? '') ?>">
    <input type="hidden" name="typ" value="<?= esc($typ ?? 'liegeplatz') ?>">

    <div class="card" style="padding:16px; margin-bottom:16px;">
        <h3>Kunde auswählen</h3>

        <div class="field" style="margin-bottom:8px;">
            <label>Bestehender Kunde</label>
            <select name="kid">
                <option value="">Bitte wählen</option>
                <?php foreach ($kunden as $k): ?>
                    <option value="<?= esc($k['kid']) ?>">
                        <?= esc(($k['nachname'] ?? '') . ', ' . ($k['vorname'] ?? '')) ?> (<?= esc($k['email'] ?? '') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field" style="margin-bottom:12px;">
            <label>
                <input type="checkbox" name="new_customer" value="1">
                Neuen Kunden anlegen
            </label>
        </div>

        <div class="card" style="padding:12px;">
            <h4>Neuer Kunde</h4>

            <div class="field">
                <label>Vorname*</label>
                <input type="text" name="vorname" value="<?= esc($old['vorname'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Nachname*</label>
                <input type="text" name="nachname" value="<?= esc($old['nachname'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Geburtsdatum*</label>
                <input type="date" name="geburtsdatum" value="<?= esc($old['geburtsdatum'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Geschlecht*</label>
                <select name="geschlecht">
                    <?php $g = $old['geschlecht'] ?? ''; ?>
                    <option value="" <?= $g === '' ? 'selected' : '' ?>>Bitte wählen</option>
                    <option value="m" <?= $g === 'm' ? 'selected' : '' ?>>m</option>
                    <option value="w" <?= $g === 'w' ? 'selected' : '' ?>>w</option>
                    <option value="d" <?= $g === 'd' ? 'selected' : '' ?>>d</option>
                </select>
            </div>
            <div class="field">
                <label>Straße*</label>
                <input type="text" name="strasse" value="<?= esc($old['strasse'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Hausnr*</label>
                <input type="text" name="hausnr" value="<?= esc($old['hausnr'] ?? '') ?>">
            </div>
            <div class="field">
                <label>PLZ*</label>
                <input type="text" name="plz" value="<?= esc($old['plz'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Ort*</label>
                <input type="text" name="ort" value="<?= esc($old['ort'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Telefon</label>
                <input type="text" name="telefon" value="<?= esc($old['telefon'] ?? '') ?>">
            </div>
            <div class="field">
                <label>E-Mail*</label>
                <input type="email" name="email" value="<?= esc($old['email'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Passwort*</label>
                <input type="password" name="passwort">
            </div>
            <div class="field">
                <label>Passwort wiederholen*</label>
                <input type="password" name="passwort2">
            </div>
        </div>
    </div>

    <div class="card" style="padding:16px;">
        <?php if (($typ ?? 'liegeplatz') === 'liegeplatz'): ?>
            <h3>Liegeplätze</h3>
            <?php if (empty($liegeplaetze)): ?>
                <p>Bitte Zeitraum wählen und Verfügbarkeit laden.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Auswahl</th>
                        <th>Anleger</th>
                        <th>Nummer</th>
                        <th>Status</th>
                        <th>Verfügbar</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($liegeplaetze as $lp): ?>
                        <?php $isAvailable = !empty($lp['is_available_in_range']); ?>
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    name="liegeplaetze[]"
                                    value="<?= esc($lp['lid']) ?>"
                                    <?= $isAvailable ? '' : 'disabled' ?>
                                >
                            </td>
                            <td><?= esc($lp['anleger']) ?></td>
                            <td><?= esc($lp['nummer']) ?></td>
                            <td><?= esc($lp['status']) ?></td>
                            <td><?= $isAvailable ? 'ja' : 'nein' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php else: ?>
            <h3>Boote</h3>
            <?php if (empty($boote)): ?>
                <p>Bitte Zeitraum wählen und Verfügbarkeit laden.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Auswahl</th>
                        <th>Name</th>
                        <th>Typ</th>
                        <th>Plätze</th>
                        <th>Status</th>
                        <th>Verfügbar</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($boote as $b): ?>
                        <?php $isAvailable = !empty($b['is_available_in_range']); ?>
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    name="boote[]"
                                    value="<?= esc($b['boid']) ?>"
                                    <?= $isAvailable ? '' : 'disabled' ?>
                                >
                            </td>
                            <td><?= esc($b['name']) ?></td>
                            <td><?= esc($b['typ']) ?></td>
                            <td><?= esc($b['plaetze']) ?></td>
                            <td><?= esc($b['status']) ?></td>
                            <td><?= $isAvailable ? 'ja' : 'nein' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    </div>

<div style="margin-top:12px;">
    <button type="submit">Buchung anlegen</button>
    <a class="btn" href="<?= site_url('/mitarbeiter') ?>" style="margin-left:8px;">Abbruch</a>
</div>
</form>

<?= $this->endSection() ?>
