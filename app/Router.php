<?php

class Router
{
    private array $routes = [];

    public function get(string $path, $handler): void { $this->add('GET', $path, $handler); }
    public function post(string $path, $handler): void { $this->add('POST', $path, $handler); }

    private function add(string $method, string $path, $handler): void
    {
        $pattern = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $path);
        $this->routes[] = [$method, '#^' . $pattern . '$#', $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        foreach ($this->routes as [$m, $regex, $handler]) {
            if ($m !== $method) continue;
            if (preg_match($regex, $path, $matches)) {
                $params = [];
                foreach ($matches as $k => $v) {
                    if (!is_int($k)) $params[$k] = $v;
                }
                $this->invoke($handler, $params);
                return;
            }
        }
        http_response_code(404);
        echo '<h1>404 Not Found</h1>';
    }

    private function invoke($handler, array $params): void
    {
        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler);
            $instance = new $class();
            $instance->{$method}($params);
        } elseif (is_callable($handler)) {
            $handler($params);
        } else {
            throw new RuntimeException('Invalid route handler');
        }
    }
}
