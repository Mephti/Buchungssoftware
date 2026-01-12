<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Buchungssoftware</title>
</head>
<body>

<header style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; border-bottom:1px solid #ddd;">
    <div>
        <strong>Buchungssoftware</strong>
    </div>

    <nav>
        <?php if (session('isLoggedIn')): ?>
            <span style="margin-right:12px;">
        <?= esc(session('name')) ?>
    </span>

            <a href="<?= site_url('/mein-konto') ?>">Mein Konto</a>
            <span style="margin:0 8px;">|</span>
            <a href="<?= site_url('/meine-buchungen') ?>">Meine Buchungen</a>
            <span style="margin:0 8px;">|</span>
            <a href="<?= site_url('/logout') ?>">Logout</a>
        <?php else: ?>
            <a href="<?= site_url('/login') ?>">Login</a>
            <span style="margin:0 8px;">|</span>
            <a href="<?= site_url('/register') ?>">Registrierung</a>
        <?php endif; ?>
    </nav>
</header>

<main style="padding:16px;">
    <h1>Willkommen</h1>
    <p>Das ist die Startseite. Als nächstes bauen wir den Buchungsbereich gemäß Mockup.</p>
</main>

</body>
</html>