<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Exceptions\HttpException;

final class Router
{
    /**
     * @var list<array{
     *     method: string,
     *     pattern: string,
     *     handler: callable,
     *     middleware: list<callable>,
     *     name: ?string
     * }>
     */
    private array $routes = [];

    /**
     * @param list<callable> $middleware
     */
    public function get(string $path, callable $handler, array $middleware = [], ?string $name = null): void
    {
        $this->add('GET', $path, $handler, $middleware, $name);
    }

    /**
     * @param list<callable> $middleware
     */
    public function post(string $path, callable $handler, array $middleware = [], ?string $name = null): void
    {
        $this->add('POST', $path, $handler, $middleware, $name);
    }

    /**
     * @param list<callable> $middleware
     */
    public function patch(string $path, callable $handler, array $middleware = [], ?string $name = null): void
    {
        $this->add('PATCH', $path, $handler, $middleware, $name);
    }

    /**
     * @param list<callable> $middleware
     */
    public function put(string $path, callable $handler, array $middleware = [], ?string $name = null): void
    {
        $this->add('PUT', $path, $handler, $middleware, $name);
    }

    /**
     * @param list<callable> $middleware
     */
    public function delete(string $path, callable $handler, array $middleware = [], ?string $name = null): void
    {
        $this->add('DELETE', $path, $handler, $middleware, $name);
    }

    public function dispatch(Request $request): Response
    {
        $pathMatched = false;

        foreach ($this->routes as $route) {
            $params = $this->match($route['pattern'], $request->path());
            if ($params === null) {
                continue;
            }

            $pathMatched = true;
            if ($route['method'] !== $request->method()) {
                continue;
            }

            $routedRequest = $request->withRouteParams($params);
            $next = static fn (Request $current): Response => ($route['handler'])($current);

            foreach (array_reverse($route['middleware']) as $middleware) {
                $next = static fn (Request $current): Response => $middleware($current, $next);
            }

            return $next($routedRequest);
        }

        throw new HttpException($pathMatched ? 405 : 404);
    }

    /**
     * @param list<callable> $middleware
     */
    private function add(string $method, string $pattern, callable $handler, array $middleware, ?string $name): void
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware,
            'name' => $name,
        ];
    }

    /**
     * @return array<string, string>|null
     */
    private function match(string $pattern, string $path): ?array
    {
        preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $pattern, $tokens, PREG_OFFSET_CAPTURE);
        $regex = '';
        $offset = 0;

        foreach ($tokens[0] as $index => [$token, $tokenOffset]) {
            $regex .= preg_quote(substr($pattern, $offset, $tokenOffset - $offset), '#');
            $regex .= '(?P<' . $tokens[1][$index][0] . '>[^/]+)';
            $offset = $tokenOffset + strlen($token);
        }

        $regex .= preg_quote(substr($pattern, $offset), '#');
        if (preg_match('#^' . $regex . '/?$#D', $path, $matches) !== 1) {
            return null;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = rawurldecode($value);
            }
        }

        return $params;
    }
}
