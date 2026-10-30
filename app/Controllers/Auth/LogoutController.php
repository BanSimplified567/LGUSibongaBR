<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;

final class LogoutController
{
    public function logout(Request $request): Response
    {
        AuthService::logout();

        return Response::redirect('/login');
    }
}
