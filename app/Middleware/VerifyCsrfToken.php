<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;

final class VerifyCsrfToken
{
    public function __invoke(Request $request, callable $next): Response
    {
        Csrf::verify($request);

        return $next($request);
    }
}
