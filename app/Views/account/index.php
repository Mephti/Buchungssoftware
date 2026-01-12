<!doctype html>
<html lang="de">
<head><meta charset="utf-8"><title>Mein Konto</title></head>
<body>
<h1>Mein Konto</h1>
<p>Angemeldet als: <?= esc(session('name')) ?> (<?= esc(session('role')) ?>)</p>
<p>E-Mail: <?= esc(session('email')) ?></p>

<p>
    <a href="<?= site_url('/') ?>">Zur Startseite</a> |
    <a href="<?= site_url('/meine-buchungen') ?>">Meine Buchungen</a>
</p>
</body>
</html>