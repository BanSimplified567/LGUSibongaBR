<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | <?= htmlspecialchars($appName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<main class="auth-shell">
    <section class="auth-card" aria-labelledby="page-title">
        <p class="eyebrow">Municipality of Sibonga</p>
        <h1 id="page-title">Staff sign in</h1>
        <p class="muted">Authorized personnel only.</p>

        <?php if (is_string($error) && $error !== ''): ?>
            <p class="alert" role="alert"><?= htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
        <?php endif; ?>

        <form method="post" action="/login">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <label for="email">Work email</label>
            <input id="email" name="email" type="email" autocomplete="username" maxlength="254" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>

            <button type="submit">Sign in</button>
        </form>

        <p class="privacy-note">Access to records is limited to authorized staff and is audited.</p>
    </section>
</main>
</body>
</html>
