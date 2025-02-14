<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';

try {
    $context = new Agora\Core\Context(__DIR__ . '/config/website.conf');
    $db = $context->getDB();

    echo "<h2>Database Content Check</h2>";

    // Check Regions
    echo "<h3>Regions:</h3>";
    $regions = $db->query("SELECT * FROM Region");
    echo "<pre>";
    print_r($regions);
    echo "</pre>";

    // Check Businesses
    echo "<h3>Businesses:</h3>";
    $businesses = $db->query("SELECT * FROM Business");
    echo "<pre>";
    print_r($businesses);
    echo "</pre>";

    // Check Users
    echo "<h3>Users:</h3>";
    $users = $db->query("SELECT user_id, user_name, email, role, is_active FROM User");
    echo "<pre>";
    print_r($users);
    echo "</pre>";

} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo $e->getMessage();
}