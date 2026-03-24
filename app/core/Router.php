<?php
namespace App\Core;

class Router {
    private array $routes = [];

    public function get(string $path, string $handler): void {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, string $handler): void {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        if (isset($this->routes[$method][$uri])) {
            [$controllerName, $action] = explode('@', $this->routes[$method][$uri]);
            $class = "App\\Controllers\\$controllerName";
            $controller = new $class();
            $controller->$action();
        } else {
            http_response_code(404);
            echo "404 Not Found";
        }
    }
}
