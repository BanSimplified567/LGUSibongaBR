<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

final class GuestOnly
{
    public function __invoke(Request $request, callable $next): Response
    {
        if (Auth::user() !== null) {
            return Response::redirect('/dashboard');
        }

        return $next($request);
    }
}
