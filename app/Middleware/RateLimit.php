<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Response;

final class RateLimit
{
    public function __construct(
        private readonly int $maximum,
        private readonly int $windowSeconds,
    ) {
    }

    public function __invoke(Request $request, callable $next): Response
    {
        RateLimiter::checkLogin($request, $this->maximum, $this->windowSeconds);

        return $next($request);
    }
}
