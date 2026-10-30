<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Exceptions\HttpException;
use App\Core\Request;
use App\Core\Response;

final class RequirePermission
{
    public function __construct(
        private readonly string $permission,
    ) {
    }

    public function __invoke(Request $request, callable $next): Response
    {
        $user = Auth::user();
        if ($user === null) {
            throw new HttpException(403);
        }

        if (!Auth::checkPermission($user['id'], $this->permission)) {
            throw new HttpException(403);
        }

        return $next($request);
    }
}
