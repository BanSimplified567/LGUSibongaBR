<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Exceptions\HttpException;
use RuntimeException;

final class RateLimiter
{
    public static function checkLogin(Request $request, int $maximum, int $windowSeconds): void
    {
        self::update(self::key($request), $maximum, $windowSeconds, false);
    }

    public static function clearLogin(Request $request, string $email): void
    {
        self::update(self::key($request, $email), 0, 0, true);
    }

    private static function key(Request $request, ?string $email = null): string
    {
        $submittedEmail = $email ?? $request->input('email', '');
        $normalizedEmail = is_string($submittedEmail) ? strtolower(trim($submittedEmail)) : '';
        $ip = $request->server('REMOTE_ADDR', '');
        $remoteAddress = is_string($ip) ? $ip : '';

        return hash('sha256', $remoteAddress . "\0" . $normalizedEmail);
    }

    private static function update(string $key, int $maximum, int $windowSeconds, bool $clear): void
    {
        $directory = BASE_PATH . '/storage/framework/rate-limits';
        if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
            throw new RuntimeException('Unable to initialize login rate limiting.');
        }

        $handle = fopen($directory . '/' . $key . '.json', 'c+');
        if ($handle === false) {
            throw new RuntimeException('Unable to initialize login rate limiting.');
        }

        try {
            if (!flock($handle, LOCK_EX)) {
                throw new RuntimeException('Unable to lock login rate limit state.');
            }

            rewind($handle);
            $contents = stream_get_contents($handle);
            $state = is_string($contents) && $contents !== '' ? json_decode($contents, true) : [];
            if (!is_array($state)) {
                throw new RuntimeException('Login rate limit state is invalid.');
            }

            $now = time();
            $windowStart = $state['window_start'] ?? 0;
            $attempts = $state['attempts'] ?? 0;
            if (!is_int($windowStart) || !is_int($attempts)) {
                throw new RuntimeException('Login rate limit state is invalid.');
            }

            if ($clear || $now - $windowStart >= $windowSeconds) {
                $state = ['window_start' => $now, 'attempts' => 0];
            } elseif ($attempts >= $maximum) {
                throw new HttpException(429);
            }

            if (!$clear) {
                $state['attempts']++;
            }

            rewind($handle);
            if (!ftruncate($handle, 0) || fwrite($handle, json_encode($state, JSON_THROW_ON_ERROR)) === false) {
                throw new RuntimeException('Unable to persist login rate limit state.');
            }
            fflush($handle);
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
