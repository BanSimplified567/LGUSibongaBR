<?php

declare(strict_types=1);

return [
    'lifetime_minutes' => max(5, (int) ($_ENV['SESSION_LIFETIME'] ?? 30)),
    'same_site' => 'Strict',
    'http_only' => true,
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
];
