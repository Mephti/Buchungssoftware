<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Login</title>
</head>
<body>
<h1>Login</h1>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= esc($error) ?></p>
<?php endif; ?>

<form method="post" action="<?= site_url('/login') ?>">
    <?= csrf_field() ?>

    <div>
        <label>E-Mail</label><br>
        <input type="email" name="email" required>
    </div>

    <div style="margin-top:8px;">
        <label>Passwort</label><br>
        <input type="password" name="passwort" required>
    </div>

    <div style="margin-top:12px;">
        <button type="submit">Einloggen</button>
    </div>
</form>
</body>
</html>