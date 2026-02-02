<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title><?= esc($title ?? 'Buchungssoftware') ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ✅ app.css aus public/css -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">

    <!-- (Optional) Seite-spezifische Styles -->
    <?= $this->renderSection('styles') ?>

    <style>
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 0.9em;
            border: 1px solid #ccc;
            background: #fff;
        }
        .status-verfuegbar { border-color: #2e7d32; color: #2e7d32; }
        .status-gesperrt   { border-color: #c62828; color: #c62828; }
        .status-vermietet  { border-color: #ef6c00; color: #ef6c00; }
        .status-belegt     { border-color: #1565c0; color: #1565c0; }
        .status-im-zeitraum { border-color: #6a1b9a; color: #6a1b9a; }
        .legend { display:flex; gap:12px; flex-wrap:wrap; margin:8px 0 12px; }
    </style>
</head>
<body>

<header style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; border-bottom:1px solid #ddd;">
    <a class="brand" href="<?= site_url('/') ?>" aria-label="Startseite">
        <img src="<?= base_url('img/logo.png') ?>" alt="Plauer See">
    </a>

    <nav>
        <?php if (session('isLoggedIn')): ?>
            <span style="margin-right:12px;"><?= esc(session('name')) ?></span>
            <a href="<?= site_url('/mein-konto') ?>">Mein Konto</a> |
            <a href="<?= site_url('/meine-buchungen') ?>">Meine Buchungen</a> |
            <a href="<?= site_url('/logout') ?>">Logout</a>
        <?php else: ?>
            <a href="<?= site_url('/login') ?>">Login</a> |
            <a href="<?= site_url('/register') ?>">Registrierung</a>
        <?php endif; ?>
    </nav>
</header>

<main class="container" style="padding:16px 0;">
    <?= $this->renderSection('content') ?>
</main>

<footer style="padding:12px 16px; border-top:1px solid #ddd; margin-top:24px; font-size:0.9em;">
    &copy; <?= date('Y') ?> Buchungssoftware
</footer>

<!-- ✅ Seite-spezifische Scripts -->
<?= $this->renderSection('scripts') ?>

</body>
</html>
