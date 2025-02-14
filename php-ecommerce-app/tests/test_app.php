<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('APP_ROOT', __DIR__);
require_once APP_ROOT . '/vendor/autoload.php';

echo "<h2>Comprehensive Application Test</h2>";

function testRoute($path)
{
    echo "<div style='margin: 10px; padding: 10px; border: 1px solid #ccc;'>";
    echo "<strong>Testing route: " . htmlspecialchars($path) . "</strong><br>";

    $_SERVER['REQUEST_URI'] = '/Agora_V.3/' . $path;
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['HTTP_HOST'] = 'localhost';

    try {
        $context = new Agora\Core\Context(APP_ROOT . '/config/website.conf');
        echo "URI Path: " . $context->getURI()->getPath() . "<br>";
        echo "Full URL: " . $context->getURI()->getSite() . "/" . $path . "<br>";
        echo "<a href='" . $context->getURI()->getSite() . "/" . $path . "' target='_blank'>Test this route</a>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    echo "</div>";
}

// Test various routes
$routes = [
    '',
    'login',
    'register',
    'admin/dashboard',
    'seller/dashboard'
];

foreach ($routes as $route) {
    testRoute($route);
}

// Test database connection
echo "<h3>Database Connection Test:</h3>";
try {
    $context = new Agora\Core\Context(APP_ROOT . '/config/website.conf');
    $result = $context->getDB()->query("SELECT * FROM Region");
    echo "<pre>";
    print_r($result);
    echo "</pre>";
} catch (Exception $e) {
    echo "Database Error: " . $e->getMessage();
}