<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Exceptions\HttpException;
use Throwable;

final class App
{
    public function __construct(
        private readonly Router $router,
        private readonly array $securityHeaders,
    ) {
    }

    public function run(): void
    {
        $requestId = bin2hex(random_bytes(12));
        $_SERVER['APP_REQUEST_ID'] = $requestId;

        try {
            $response = $this->router->dispatch(Request::fromGlobals());
        } catch (HttpException $exception) {
            $response = Response::html(
                View::render('errors/error', [
                    'status' => $exception->statusCode,
                    'requestId' => $requestId,
                ])->body(),
                $exception->statusCode,
            );
        } catch (Throwable $exception) {
            error_log(sprintf('Request %s failed (%s).', $requestId, $exception::class));
            $response = Response::html(
                View::render('errors/error', ['status' => 500, 'requestId' => $requestId])->body(),
                500,
            );
        }

        $response->withHeaders($this->securityHeaders + ['X-Request-ID' => $requestId])->send();
    }
}
