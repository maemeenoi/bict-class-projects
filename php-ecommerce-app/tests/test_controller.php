// test_controller.php
<?php
require_once __DIR__ . '/vendor/autoload.php';

try {
    $controllerName = "Agora\\Controllers\\Dashboard\\AdminDashboardController";
    echo "Looking for controller: " . $controllerName . "\n";

    if (class_exists($controllerName)) {
        echo "Controller found!\n";
    } else {
        echo "Controller not found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}