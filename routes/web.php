<?php

declare(strict_types=1);

use App\Controllers\Auth\LoginController;
use App\Controllers\Auth\LogoutController;
use App\Controllers\DashboardController;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Middleware\Authenticate;
use App\Middleware\GuestOnly;
use App\Middleware\RequirePermission;
use App\Middleware\RateLimit;
use App\Middleware\VerifyCsrfToken;

/** @var Router $router */
$guestOnly = new GuestOnly();
$authenticate = new Authenticate();
$dashboardPermission = new RequirePermission('dashboard.view');
$csrf = new VerifyCsrfToken();
$loginLimiter = new RateLimit(
    (int) ($securityConfig['login_attempts'] ?? 5),
    (int) ($securityConfig['login_window_seconds'] ?? 900),
);
$loginController = new LoginController();
$logoutController = new LogoutController();
$dashboardController = new DashboardController();

$router->get('/', static fn (Request $request): Response => Response::redirect('/login'), name: 'home');
$router->get('/login', [$loginController, 'show'], [$guestOnly], 'login');
$router->post('/login', [$loginController, 'login'], [$guestOnly, $csrf, $loginLimiter], 'login.submit');
$router->post('/logout', [$logoutController, 'logout'], [$authenticate, $csrf], 'logout');
$router->get('/dashboard', [$dashboardController, 'index'], [$authenticate, $dashboardPermission], 'dashboard');
