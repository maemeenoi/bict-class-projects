<?php
namespace Agora\Core\Http;

use Agora\Core\Context;
use Agora\Core\Exceptions\InvalidRequestException;

class Router
{
    private $context;
    private $routes = [];
    private $patterns = [];

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function addRoute(string $path, string $controller, string $action = 'index'): void
    {
        // Check if route contains parameters
        if (strpos($path, ':') !== false || strpos($path, '([') !== false) {
            $this->patterns[$path] = [
                'controller' => $controller,
                'action' => $action
            ];
        } else {
            $this->routes[$path] = [
                'controller' => $controller,
                'action' => $action
            ];
        }
    }

    public function dispatch(): void
    {
        $path = $this->context->getURI()->getPath();
        error_log("Router Path: " . $path);

        // If path is empty, use default route
        if (empty($path)) {
            $path = '';
        }

        // First check exact matches
        if (isset($this->routes[$path])) {
            $this->executeRoute($this->routes[$path]);
            return;
        }

        // Then check pattern matches
        foreach ($this->patterns as $pattern => $route) {
            // Convert route pattern to regex
            $regex = $this->patternToRegex($pattern);

            if (preg_match($regex, $path, $matches)) {
                // Store parameters in context for controller access
                array_shift($matches); // Remove full match
                $this->context->getURI()->setParams($matches);

                $this->executeRoute($route);
                return;
            }
        }

        error_log("Route not found: " . $path);
        throw new InvalidRequestException("Route not found: $path");
    }

    private function patternToRegex($pattern)
    {
        // Already in regex format
        if (strpos($pattern, '([') !== false) {
            return "#^" . $pattern . "$#";
        }

        // Convert :parameter format to regex
        $regex = preg_replace('/:[a-zA-Z]+/', '([^/]+)', $pattern);
        return "#^" . $regex . "$#";
    }

    private function executeRoute($route)
    {
        $controllerClass = str_replace('/', '\\', $route['controller']);
        $controllerName = "Agora\\Controllers\\" . $controllerClass;

        if (!class_exists($controllerName)) {
            error_log("Controller not found: " . $controllerName);
            throw new InvalidRequestException("Controller not found: {$route['controller']}");
        }

        $controller = new $controllerName($this->context);
        $controller->process();
    }
}