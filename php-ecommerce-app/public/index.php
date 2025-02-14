<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define the application root path
define('APP_ROOT', dirname(__DIR__));

// Composer autoloader
require_once APP_ROOT . '/vendor/autoload.php';

use Agora\Core\Context;
use Agora\Core\Http\Router;
use Agora\Core\Exceptions\InvalidRequestException;

try {
    // Initialize application context
    $context = new Context(APP_ROOT . '/config/website.conf');

    // Initialize router with context
    $router = new Router($context);

    // Define routes
    $router->addRoute('', 'HomeController', 'index');
    $router->addRoute('login', 'AuthController', 'login');
    $router->addRoute('register', 'RegistrationController', 'index');
    $router->addRoute('logout', 'AuthController', 'logout');
    $router->addRoute('product/([0-9]+)', 'ProductController', 'index');
    $router->addRoute('seller/dashboard', 'Dashboard/SellerDashboardController', 'index');
    $router->addRoute('business-admin/dashboard', 'Dashboard/AdminDashboardController', 'index');
    $router->addRoute('admin/dashboard', 'Dashboard/AdminDashboardController', 'index');
    $router->addRoute('buyer/dashboard', 'Dashboard/BuyerDashboardController', 'index');
    $router->addRoute('buyer/order', 'OrderController', 'view');
    $router->dispatch();

} catch (InvalidRequestException $e) {
    // Handle 404 and other routing errors
    header("HTTP/1.0 404 Not Found");
    echo "Page not found: " . $e->getMessage();
} catch (Exception $e) {
    // Handle other exceptions
    header("HTTP/1.0 500 Internal Server Error");
    echo "An error occurred: " . $e->getMessage();
    die();
}