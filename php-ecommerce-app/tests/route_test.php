<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('APP_ROOT', __DIR__);
require_once APP_ROOT . '/vendor/autoload.php';

echo "<h2>Route Testing</h2>";

// Test URI parsing
$_SERVER['REQUEST_URI'] = '/Agora_V.3/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';

use Agora\Core\Context;
use Agora\Core\Http\Router;

try {
    $context = new Context(APP_ROOT . '/config/website.conf');

    echo "<h3>URI Information:</h3>";
    echo "Path: " . $context->getURI()->getPath() . "<br>";
    echo "Site: " . $context->getURI()->getSite() . "<br>";

    $router = new Router($context);

    // Add test routes
    $router->addRoute('', 'HomeController', 'index');
    $router->addRoute('login', 'AuthController', 'login');

    echo "<h3>Testing Routes:</h3>";
    echo "<a href='" . $context->getURI()->getSite() . "'>Test Home Route</a><br>";
    echo "<a href='" . $context->getURI()->getSite() . "/login'>Test Login Route</a><br>";

} catch (Exception $e) {
    echo "<h3>Error:</h3>";
    echo $e->getMessage();
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}