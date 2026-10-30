<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Exceptions\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;

$router = new Router();
$router->get('/records/{id}', static function (Request $request): Response {
    return Response::html($request->routeParam('id') ?? '');
});

$response = $router->dispatch(new Request('GET', '/records/42'));
if ($response->body() !== '42') {
    throw new RuntimeException('Route parameter matching failed.');
}

try {
    $router->dispatch(new Request('POST', '/records/42'));
    throw new RuntimeException('Method mismatch should have returned 405.');
} catch (HttpException $exception) {
    if ($exception->statusCode !== 405) {
        throw $exception;
    }
}

try {
    $router->dispatch(new Request('GET', '/missing'));
    throw new RuntimeException('Unknown route should have returned 404.');
} catch (HttpException $exception) {
    if ($exception->statusCode !== 404) {
        throw $exception;
    }
}

echo "Smoke tests passed.\n";
