<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private Request $request;
    private Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, $callback): void
    {
        $this->routes['GET'][$this->normalizePath($path)] = $callback;
    }

    public function post(string $path, $callback): void
    {
        $this->routes['POST'][$this->normalizePath($path)] = $callback;
    }

    private function normalizePath(string $path): string
    {
        return '/' . trim($path, '/');
    }

    public function resolve()
    {
        $method = $this->request->getMethod();
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        $path = $this->request->getPath();

        $routesForMethod = $this->routes[$method] ?? [];

        // Exact match
        if (isset($routesForMethod[$path])) {
            return $this->executeCallback($routesForMethod[$path], []);
        }

        // Regex parameter matching (e.g. /orders/{id} atau /orders/{id}/status)
        foreach ($routesForMethod as $routePattern => $callback) {
            $patternRegex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routePattern);
            $patternRegex = '#^' . $patternRegex . '$#';

            if (preg_match($patternRegex, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return $this->executeCallback($callback, $params);
            }
        }

        // Not Found 404
        $this->response->setStatusCode(404);
        return View::render('errors/404', ['title' => 'Halaman Tidak Ditemukan']);
    }

    private function executeCallback($callback, array $params = [])
    {
        if (is_callable($callback)) {
            return call_user_func_array($callback, [$this->request, $this->response, $params]);
        }

        if (is_array($callback)) {
            [$controllerClass, $method] = $callback;
            $controller = new $controllerClass();
            return call_user_func_array([$controller, $method], [$this->request, $this->response, $params]);
        }

        throw new \Exception("Route callback tidak valid.");
    }
}

