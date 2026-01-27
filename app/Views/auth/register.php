<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card form-card" style="max-width: 760px;">
    <h1>Registrierung</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert--error"><?= esc($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('/register') ?>">
        <?= csrf_field() ?>

        <?php $g = $old['geschlecht'] ?? ''; ?>

        <div class="form-grid">
            <div class="field">
                <label for="vorname">Vorname*</label>
                <input class="input" id="vorname" name="vorname" required value="<?= esc($old['vorname'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="nachname">Nachname*</label>
                <input class="input" id="nachname" name="nachname" required value="<?= esc($old['nachname'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="geburtsdatum">Geburtsdatum</label>
                <input class="input" type="date" id="geburtsdatum" name="geburtsdatum" value="<?= esc($old['geburtsdatum'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="geschlecht">Geschlecht*</label>
                <select id="geschlecht" name="geschlecht" required>
                    <option value="" <?= $g === '' ? 'selected' : '' ?>>Bitte wählen</option>
                    <option value="m" <?= $g === 'm' ? 'selected' : '' ?>>m</option>
                    <option value="w" <?= $g === 'w' ? 'selected' : '' ?>>w</option>
                    <option value="d" <?= $g === 'd' ? 'selected' : '' ?>>d</option>
                </select>
            </div>

            <div class="field">
                <label for="strasse">Straße</label>
                <input class="input" id="strasse" name="strasse" value="<?= esc($old['strasse'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="hausnr">Hausnr</label>
                <input class="input" id="hausnr" name="hausnr" value="<?= esc($old['hausnr'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="plz">PLZ</label>
                <input class="input" id="plz" name="plz" value="<?= esc($old['plz'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="ort">Ort</label>
                <input class="input" id="ort" name="ort" value="<?= esc($old['ort'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="telefon">Telefon</label>
                <input class="input" id="telefon" name="telefon" value="<?= esc($old['telefon'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="email">E-Mail*</label>
                <input class="input" type="email" id="email" name="email" required value="<?= esc($old['email'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="passwort">Passwort* (min. 8 Zeichen)</label>
                <input class="input" type="password" id="passwort" name="passwort" required>
            </div>

            <div class="field">
                <label for="passwort2">Passwort wiederholen*</label>
                <input class="input" type="password" id="passwort2" name="passwort2" required>
            </div>

            <div class="field full" style="margin-top:6px;">
                <label style="display:flex; align-items:center; gap:10px;">
                    <input type="checkbox" required>
                    Ich habe die <a href="#">AGB</a> und <a href="#">Datenschutzerklärung</a> gelesen und akzeptiert.
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button class="btn btn--primary" type="submit">Weiter ▾</button>
        </div>

        <p class="muted" style="text-align:center; margin-top:10px;">
            Bereits registriert? <a href="<?= site_url('/login') ?>">Zum Login</a>
        </p>
    </form>
</div>

<?= $this->endSection() ?>