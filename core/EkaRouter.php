<?php

namespace Core;

class EkaRouter
{
    private static ?EkaRouter $instance = null;
    private array $routes = [];
    private array $middlewares = [];
    private string $currentGroup = '';

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function middleware(array $middlewares, callable $callback): void
    {
        $previousMiddlewares = $this->middlewares;
        $this->middlewares = array_merge($this->middlewares, $middlewares);
        
        $callback();
        
        $this->middlewares = $previousMiddlewares;
    }

    public function group(string $prefix, callable $callback): void
    {
        $previousGroup = $this->currentGroup;
        $this->currentGroup .= $prefix;
        
        $callback();
        
        $this->currentGroup = $previousGroup;
    }

    public function get(string $path, $controller, string $method): void
    {
        $this->addRoute('get', $path, $controller, $method);
    }

    public function post(string $path, $controller, string $method): void
    {
        $this->addRoute('post', $path, $controller, $method);
    }

    private function addRoute(string $httpMethod, string $path, $controller, string $method): void
    {
        $fullPath = rtrim($this->currentGroup . $path, '/') ?: '/';
        $this->routes[$httpMethod][$fullPath] = [
            'controller' => $controller,
            'method' => $method,
            'middlewares' => $this->middlewares
        ];
    }

    public function dispatch(EkaRequest $request, EkaResponse $response): void
    {
        $uri = $request->getUri();
        $method = $request->getMethod();
        
        foreach ($this->routes[$method] ?? [] as $routePath => $route) {
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_-]+)', $routePath);
            $pattern = "#^" . $pattern . "$#";
            
            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                $this->runMiddlewares($route['middlewares'], $request, $response);
                
                if (is_callable($route['controller'])) {
                    call_user_func_array($route['controller'], array_values($params));
                    return;
                }
                
                $controller = new $route['controller']();
                call_user_func_array([$controller, $route['method']], array_values($params));
                return;
            }
        }
        
        $response->setStatusCode(404);
        require VIEWS_PATH . '/errors/404.php';
        exit;
    }

    private function runMiddlewares(array $middlewares, EkaRequest $request, EkaResponse $response): void
    {
        foreach ($middlewares as $middleware) {
            $middlewareInstance = new $middleware();
            if (method_exists($middlewareInstance, 'handle')) {
                $middlewareInstance->handle($request, $response);
            }
        }
    }
}
