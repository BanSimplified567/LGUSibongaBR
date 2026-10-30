<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Session
{
    /**
     * @param array{lifetime_minutes: int, same_site: string, http_only: bool, secure: bool} $config
     */
    public static function start(array $config): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $lifetimeMinutes = max(5, $config['lifetime_minutes']);
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');

        session_set_cookie_params([
            'lifetime' => $lifetimeMinutes * 60,
            'path' => '/',
            'secure' => $config['secure'],
            'httponly' => $config['http_only'],
            'samesite' => $config['same_site'],
        ]);

        if (!session_start()) {
            throw new RuntimeException('Unable to start session.');
        }

        $now = time();
        $idleLimit = $lifetimeMinutes * 60;
        $absoluteLimit = $lifetimeMinutes * 60 * 4;
        $lastActivity = $_SESSION['_last_activity'] ?? $now;
        $createdAt = $_SESSION['_created_at'] ?? $now;

        if (
            !is_int($lastActivity)
            || !is_int($createdAt)
            || $now - $lastActivity > $idleLimit
            || $now - $createdAt > $absoluteLimit
        ) {
            self::invalidate();
            if (!session_start()) {
                throw new RuntimeException('Unable to restart session.');
            }
            $now = time();
        }

        $_SESSION['_last_activity'] = $now;
        $_SESSION['_created_at'] ??= $now;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function invalidate(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Strict',
            ]);
        }
        session_destroy();
    }

    public static function regenerate(): void
    {
        if (!session_regenerate_id(true)) {
            throw new RuntimeException('Unable to rotate session ID.');
        }
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function pullFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);

        return $value;
    }
}
