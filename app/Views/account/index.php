<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Mein Konto</h1>
<p>Angemeldet als: <?= esc(session('name')) ?> (<?= esc(session('role')) ?>)</p>
<p>E-Mail: <?= esc(session('email')) ?></p>

<p>
    <a href="<?= site_url('/') ?>">Zur Startseite</a> |
    <a href="<?= site_url('/meine-buchungen') ?>">Meine Buchungen</a>
</p>

<?= $this->endSection() ?>
