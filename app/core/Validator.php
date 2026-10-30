<?php

declare(strict_types=1);

namespace App\Core;

final class Validator
{
    public static function email(mixed $value): ?string
    {
        if (!is_string($value) || strlen($value) > 254) {
            return null;
        }

        $email = filter_var(trim($value), FILTER_VALIDATE_EMAIL);

        return is_string($email) ? strtolower($email) : null;
    }

    public static function password(mixed $value): ?string
    {
        return is_string($value) && $value !== '' && strlen($value) <= 4096
            ? $value
            : null;
    }
}
