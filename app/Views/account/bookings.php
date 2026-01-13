<!doctype html>
<html lang="de">
<head><meta charset="utf-8"><title>Meine Buchungen</title></head>
<body>
<h1>Meine Buchungen</h1>
<?php if ($success = session()->getFlashdata('success')): ?>
    <p style="color:green;"><?= esc($success) ?></p>
<?php endif; ?>

<p>(Platzhalter) Hier kommt später die Liste deiner Liegeplatz- und Bootsbuchungen rein.</p>

<p>
    <a href="<?= site_url('/') ?>">Zur Startseite</a> |
    <a href="<?= site_url('/mein-konto') ?>">Mein Konto</a>
</p>
</body>
</html>