<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | <?= htmlspecialchars($appName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<main class="page-shell">
    <header class="page-header">
        <div>
            <p class="eyebrow">Municipality of Sibonga</p>
            <h1>Records dashboard</h1>
        </div>
        <form method="post" action="/logout">
            <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
            <button class="button-secondary" type="submit">Sign out</button>
        </form>
    </header>
    <section class="notice">
        <h2>System foundation</h2>
        <p>Birth-record workflows are not enabled until the LGU approves the required fields, access policy, and records procedures.</p>
    </section>
    <p class="muted">Signed in as <?= htmlspecialchars($user['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>.</p>
</main>
</body>
</html>
