<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Services\AuthService;

final class LoginController
{
    public function show(Request $request): Response
    {
        return View::render('auth/login', [
            'appName' => $_ENV['APP_NAME'] ?? 'LGU Sibonga Birth Records',
            'csrfToken' => Csrf::token(),
            'error' => Session::pullFlash('error'),
        ]);
    }

    public function login(Request $request): Response
    {
        if (AuthService::login($request)) {
            return Response::redirect('/dashboard');
        }

        Session::flash('error', 'The email or password is incorrect.');

        return Response::redirect('/login');
    }
}
