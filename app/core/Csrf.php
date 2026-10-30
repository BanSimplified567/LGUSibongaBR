<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Exceptions\HttpException;

final class Csrf
{
    public static function token(): string
    {
        $token = Session::get('_csrf_token');
        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            Session::put('_csrf_token', $token);
        }

        return $token;
    }

    public static function verify(Request $request): void
    {
        $submitted = $request->input('_token');
        $known = Session::get('_csrf_token');

        if (!is_string($submitted) || !is_string($known) || !hash_equals($known, $submitted)) {
            throw new HttpException(419);
        }
    }
}
