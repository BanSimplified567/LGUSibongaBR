<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

final class DashboardController
{
    public function index(Request $request): Response
    {
        return View::render('dashboard/index', [
            'appName' => $_ENV['APP_NAME'] ?? 'LGU Sibonga Birth Records',
            'user' => Auth::user(),
        ]);
    }
}
