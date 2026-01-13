<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1>Buchung – Schritt 2</h1>

<p>
    <strong>Von:</strong> <?= esc($booking['von']) ?><br>
    <strong>Bis:</strong> <?= esc($booking['bis']) ?><br>
    <strong>Typ:</strong> <?= esc($booking['typ']) ?>
</p>

<p>
    Hier kommt als Nächstes die Auswahl der konkreten
    <?= $booking['typ'] === 'boot' ? 'Boote' : 'Liegeplätze' ?>.
</p>

<a href="<?= site_url('/#buchung') ?>">Zurück</a>

<?= $this->endSection() ?>
