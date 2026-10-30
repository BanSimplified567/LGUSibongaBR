<?php
$messages = [
    403 => 'You are not authorized to access this page.',
    404 => 'The requested page could not be found.',
    405 => 'This request method is not allowed.',
    419 => 'Your session token expired. Please reload the page and try again.',
    429 => 'Too many sign-in attempts. Please wait before trying again.',
    500 => 'The request could not be completed. Please contact the system administrator.',
];
$message = $messages[$status] ?? 'The request could not be completed.';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= (int) $status ?> | LGU Sibonga Birth Records</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<main class="auth-shell">
    <section class="auth-card">
        <p class="eyebrow">Request error</p>
        <h1><?= (int) $status ?></h1>
        <p><?= htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
        <p class="muted">Reference: <?= htmlspecialchars($requestId, ENT_QUOTES, 'UTF-8') ?></p>
        <a href="/login">Return to sign in</a>
    </section>
</main>
</body>
</html>
