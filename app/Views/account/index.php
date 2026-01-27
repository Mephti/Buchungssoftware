<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card" style="padding:16px;">
    <h1 style="margin:0 0 10px;">Mein Konto</h1>
    <p class="muted" style="margin:0 0 10px;">
        Angemeldet als: <strong><?= esc(session('name')) ?></strong> (<?= esc(session('role')) ?>)<br>
        E-Mail: <strong><?= esc(session('email')) ?></strong>
    </p>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn" href="<?= site_url('/') ?>">Zur Startseite</a>
        <a class="btn btn--primary" href="<?= site_url('/meine-buchungen') ?>">Meine Buchungen</a>
    </div>
</div>

<?= $this->endSection() ?>