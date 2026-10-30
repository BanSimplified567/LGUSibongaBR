<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;

final class AuthService
{
    private const DUMMY_PASSWORD_HASH = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';

    public static function login(Request $request): bool
    {
        $email = Validator::email($request->input('email'));
        $password = Validator::password($request->input('password'));
        if ($email === null || $password === null) {
            AuditLogService::record(null, 'auth.login', 'failure');
            return false;
        }

        $user = User::findForAuthentication($email);
        $verified = password_verify($password, $user['password_hash'] ?? self::DUMMY_PASSWORD_HASH);
        if ($user === null || !$verified || !$user['is_active']) {
            AuditLogService::record($user['id'] ?? null, 'auth.login', 'failure');
            return false;
        }

        AuditLogService::record($user['id'], 'auth.login', 'success');
        Session::regenerate();
        Session::put('user_id', $user['id']);
        RateLimiter::clearLogin($request, $email);

        return true;
    }

    public static function logout(): void
    {
        $user = Auth::user();
        AuditLogService::record($user['id'] ?? null, 'auth.logout', 'success');
        Session::invalidate();
    }
}
