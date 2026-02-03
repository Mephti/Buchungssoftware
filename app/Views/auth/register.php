<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card auth-panel">
    <h1>Registrierung</h1>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= esc($error) ?></p>
    <?php endif; ?>

    <form method="post" action="<?= site_url('/register') ?>">
        <?= csrf_field() ?>

        <div class="field">
            <label>Vorname*</label>
            <input type="text" name="vorname" required value="<?= esc($old['vorname'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Nachname*</label>
            <input type="text" name="nachname" required value="<?= esc($old['nachname'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Geburtsdatum</label>
            <input type="date" name="geburtsdatum" value="<?= esc($old['geburtsdatum'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Geschlecht*</label>
            <select name="geschlecht" required>
                <?php $g = $old['geschlecht'] ?? ''; ?>
                <option value="" <?= $g === '' ? 'selected' : '' ?>>Bitte wählen</option>
                <option value="m" <?= $g === 'm' ? 'selected' : '' ?>>m</option>
                <option value="w" <?= $g === 'w' ? 'selected' : '' ?>>w</option>
                <option value="d" <?= $g === 'd' ? 'selected' : '' ?>>d</option>
            </select>
        </div>

        <div class="field">
            <label>Straße</label>
            <input type="text" name="strasse" value="<?= esc($old['strasse'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Hausnr</label>
            <input type="text" name="hausnr" value="<?= esc($old['hausnr'] ?? '') ?>">
        </div>

        <div class="field">
            <label>PLZ</label>
            <input type="text" name="plz" value="<?= esc($old['plz'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Ort</label>
            <input type="text" name="ort" value="<?= esc($old['ort'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Telefon</label>
            <input type="text" name="telefon" value="<?= esc($old['telefon'] ?? '') ?>">
        </div>

        <div class="field">
            <label>E-Mail*</label>
            <input type="email" name="email" required value="<?= esc($old['email'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Passwort* (min. 8 Zeichen)</label>
            <input type="password" name="passwort" required>
        </div>

        <div class="field">
            <label>Passwort wiederholen*</label>
            <input type="password" name="passwort2" required>
        </div>

        <div style="margin-top:12px;">
            <button type="submit">Registrieren</button>
        </div>
    </form>

    <p style="margin-top:12px;">
        Schon ein Konto? <a href="<?= site_url('/login') ?>">Zum Login</a>
    </p>
</div>

<?= $this->endSection() ?>
