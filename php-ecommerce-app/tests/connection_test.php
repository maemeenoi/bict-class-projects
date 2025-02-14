<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';

use Agora\Core\Database\MySQL;

try {
    // Create database connection
    $db = new MySQL(
        'localhost:3306',
        'root',
        'VDFCGQ1t!',
        'agora_v3'
    );

    // Connect to server
    $db->connectToServer();

    // Test query
    $result = $db->query("SELECT * FROM User");

    echo "<h2>Connection Test Successful!</h2>";
    echo "<h3>Regions in Database:</h3>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";

} catch (Exception $e) {
    echo "<h2>Connection Test Failed</h2>";
    echo "Error: " . $e->getMessage();
}