<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card form-card">
    <h1>LOGIN</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert--error"><?= esc($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('/login') ?>">
        <?= csrf_field() ?>

        <div class="field">
            <label for="email">E-Mail</label>
            <input class="input" type="email" id="email" name="email" required>
        </div>

        <div class="field" style="margin-top:10px;">
            <label for="passwort">Passwort</label>
            <input class="input" type="password" id="passwort" name="passwort" required>
        </div>

        <div class="form-actions">
            <button class="btn btn--primary" type="submit">Weiter ▾</button>
        </div>

        <p class="muted" style="text-align:center; margin-top:10px;">
            Noch nicht registriert? <a href="<?= site_url('/register') ?>">Registrierung</a>
        </p>
    </form>
</div>

<?= $this->endSection() ?>