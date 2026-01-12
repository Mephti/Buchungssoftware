<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Registrierung</title>
</head>
<body>
<h1>Registrierung</h1>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= esc($error) ?></p>
<?php endif; ?>

<form method="post" action="<?= site_url('/register') ?>">
    <?= csrf_field() ?>

    <div>
        <label>Vorname*</label><br>
        <input name="vorname" required value="<?= esc($old['vorname'] ?? '') ?>">
    </div>

    <div style="margin-top:8px;">
        <label>Nachname*</label><br>
        <input name="nachname" required value="<?= esc($old['nachname'] ?? '') ?>">
    </div>

    <div style="margin-top:8px;">
        <label>Geburtsdatum</label><br>
        <input type="date" name="geburtsdatum" value="<?= esc($old['geburtsdatum'] ?? '') ?>">
    </div>

    <div style="margin-top:8px;">
        <label>Geschlecht*</label><br>
        <select name="geschlecht" required>
            <?php $g = $old['geschlecht'] ?? ''; ?>
            <option value="" <?= $g === '' ? 'selected' : '' ?>>Bitte wählen</option>
            <option value="m" <?= $g === 'm' ? 'selected' : '' ?>>m</option>
            <option value="w" <?= $g === 'w' ? 'selected' : '' ?>>w</option>
            <option value="d" <?= $g === 'd' ? 'selected' : '' ?>>d</option>
        </select>
    </div>

    <div style="margin-top:8px;">
        <label>Straße</label><br>
        <input name="strasse" value="<?= esc($old['strasse'] ?? '') ?>">
    </div>

    <div style="margin-top:8px;">
        <label>Hausnr</label><br>
        <input name="hausnr" value="<?= esc($old['hausnr'] ?? '') ?>">
    </div>

    <div style="margin-top:8px;">
        <label>PLZ</label><br>
        <input name="plz" value="<?= esc($old['plz'] ?? '') ?>">
    </div>

    <div style="margin-top:8px;">
        <label>Ort</label><br>
        <input name="ort" value="<?= esc($old['ort'] ?? '') ?>">
    </div>

    <div style="margin-top:8px;">
        <label>Telefon</label><br>
        <input name="telefon" value="<?= esc($old['telefon'] ?? '') ?>">
    </div>

    <div style="margin-top:8px;">
        <label>E-Mail*</label><br>
        <input type="email" name="email" required value="<?= esc($old['email'] ?? '') ?>">
    </div>

    <div style="margin-top:8px;">
        <label>Passwort* (min. 8 Zeichen)</label><br>
        <input type="password" name="passwort" required>
    </div>

    <div style="margin-top:8px;">
        <label>Passwort wiederholen*</label><br>
        <input type="password" name="passwort2" required>
    </div>

    <div style="margin-top:12px;">
        <button type="submit">Registrieren</button>
    </div>
</form>

<p style="margin-top:12px;">
    Schon ein Konto? <a href="<?= site_url('/login') ?>">Zum Login</a>
</p>
</body>
</html>