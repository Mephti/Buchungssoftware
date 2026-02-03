<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card auth-panel">
    <h1>Login</h1>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= esc($error) ?></p>
    <?php endif; ?>

    <form method="post" action="<?= site_url('/login') ?>">
        <?= csrf_field() ?>

        <div class="field">
            <label>E-Mail</label>
            <input type="email" name="email" required>
        </div>

        <div class="field">
            <label>Passwort</label>
            <input type="password" name="passwort" required>
        </div>

        <div style="margin-top:12px;">
            <button type="submit">Einloggen</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
