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

<?php
$weather = null;
$weatherError = null;
$apiKey = trim((string)getenv('OPENWEATHER_API_KEY'));
if ($apiKey !== '') {
    $cache = \Config\Services::cache();
    $cacheKey = 'weather_plau_am_see';
    $weather = $cache->get($cacheKey);
    if (!$weather) {
        $url = 'https://api.openweathermap.org/data/2.5/weather?q='
            . rawurlencode('Plau am See,de')
            . '&units=metric&lang=de&appid=' . rawurlencode($apiKey);
        $context = stream_context_create([
            'http' => ['timeout' => 2],
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
        ]);
        $json = @file_get_contents($url, false, $context);
        if ($json !== false) {
            $data = json_decode($json, true);
            if (
                is_array($data)
                && array_key_exists('temp', $data['main'] ?? [])
                && isset($data['weather'][0]['icon'])
            ) {
                $weather = [
                    'temp' => (float)$data['main']['temp'],
                    'wind' => isset($data['wind']['speed']) ? (float)$data['wind']['speed'] : null,
                    'icon' => (string)$data['weather'][0]['icon'],
                    'desc' => (string)($data['weather'][0]['description'] ?? ''),
                ];
                $cache->save($cacheKey, $weather, 600);
            } else {
                $weatherError = 'Keine Wetterdaten.';
            }
        } else {
            $weatherError = 'Wetter nicht verfuegbar.';
        }
    }
}
?>

<header class="header">
    <div class="header__inner container">
        <a class="brand" href="<?= site_url('/') ?>" aria-label="Startseite">
            <img src="<?= base_url('img/logo.png') ?>" alt="Plauer See">
        </a>

        <div class="header__weather" aria-live="polite">
            <?php if ($weather): ?>
                <div class="weather">
                    <img
                        class="weather__icon"
                        src="<?= esc('https://openweathermap.org/img/wn/' . $weather['icon'] . '@2x.png') ?>"
                        alt="<?= esc($weather['desc']) ?>"
                    >
                    <div class="weather__meta">
                        <div class="weather__temp"><?= esc(round($weather['temp'])) ?>°C</div>
                        <?php if ($weather['wind'] !== null): ?>
                            <div class="weather__wind">Wind: <?= esc($weather['wind']) ?> m/s</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif ($apiKey !== ''): ?>
                <div class="weather weather--empty"><?= esc($weatherError ?? 'Wetter nicht verfuegbar.') ?></div>
            <?php endif; ?>
        </div>

        <nav>
            <?php if (session('isLoggedIn')): ?>
                <span style="margin-right:12px;"><?= esc(session('name')) ?></span>
                <a href="<?= site_url('/mein-konto') ?>">Mein Konto</a> |
                <a href="<?= site_url(session('role') === 'mitarbeiter' ? '/mitarbeiter' : '/meine-buchungen') ?>">Meine Buchungen</a> |
                <a href="<?= site_url('/logout') ?>">Logout</a>
            <?php else: ?>
                <a href="<?= site_url('/login') ?>">Login</a> |
                <a href="<?= site_url('/register') ?>">Registrierung</a>
            <?php endif; ?>
        </nav>
    </div>
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
