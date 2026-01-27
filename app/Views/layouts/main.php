<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title><?= esc($title ?? 'PlauerSee') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/gridstack@11.3.0/dist/gridstack.min.css">
</head>
<body>

<header class="header">
    <div class="container header__inner">
        <a class="brand" href="<?= site_url('/') ?>" aria-label="Zur Startseite">
            <img src="<?= base_url('img/logo.png') ?>" alt="PlauerSee Logo">
        </a>

        <div class="account">
            <details>
                <summary>
                    <span aria-hidden="true">👤</span>
                    Account
                    <span aria-hidden="true">▾</span>
                </summary>

                <div class="account__menu card" role="menu" aria-label="Account Menü">
                    <?php if (session('isLoggedIn')): ?>
                        <div class="account__hint">
                            Angemeldet als:<br>
                            <strong><?= esc(session('name')) ?></strong>
                        </div>
                        <a role="menuitem" href="<?= site_url('/mein-konto') ?>">Mein Konto</a>
                        <a role="menuitem" href="<?= site_url('/meine-buchungen') ?>">Meine Buchungen</a>
                        <a role="menuitem" href="<?= site_url('/logout') ?>">Logout</a>
                    <?php else: ?>
                        <a role="menuitem" href="<?= site_url('/login') ?>">Login</a>
                        <a role="menuitem" href="<?= site_url('/register') ?>">Registrierung</a>
                    <?php endif; ?>
                </div>
            </details>
        </div>
    </div>
</header>

<main class="main">
    <div class="container">
        <?= $this->renderSection('content') ?>
    </div>
</main>

<footer class="footer">
    <div class="container">
        <div class="footer__grid">
            <div>
                <h4>Kontakt</h4>
                <div><a href="#">Telefon</a></div>
                <div><a href="#">E-Mail</a></div>
            </div>

            <div>
                <h4>Social Media</h4>
                <div class="social">
                    <a href="#" aria-label="Facebook">f</a>
                    <a href="#" aria-label="Instagram">◎</a>
                </div>
            </div>

            <div>
                <h4>Über uns</h4>
                <div><a href="#">Team</a></div>
                <div><a href="#">Standort</a></div>
            </div>

            <div>
                <h4>Rechtliches</h4>
                <div><a href="#">Impressum</a></div>
                <div><a href="#">Datenschutz</a></div>
            </div>
        </div>

        <div class="footer__brand">
            <img src="<?= base_url('img/logo.png') ?>" alt="PlauerSee">
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gridstack@11.3.0/dist/gridstack-all.js"></script>
<script src="<?= base_url('js/hafen-map.js') ?>"></script>
<script src="<?= base_url('js/harbor-app.js') ?>"></script>

</body>
</html>