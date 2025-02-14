<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('APP_ROOT', __DIR__);
require_once APP_ROOT . '/vendor/autoload.php';

use Agora\Core\Context;

try {
    echo "<h2>Application Test</h2>";

    // Test Context creation
    echo "<h3>1. Creating Context</h3>";
    $context = new Context(APP_ROOT . '/config/website.conf');
    echo "✓ Context created successfully<br>";

    // Test Config
    echo "<h3>2. Testing Config</h3>";
    $config = $context->getConfig();
    echo "Mode: " . $config->get('mode') . "<br>";
    echo "Database Host: " . $config->get('db.dbHost') . "<br>";

    // Test Database
    echo "<h3>3. Testing Database</h3>";
    $db = $context->getDB();
    $result = $db->query("SELECT * FROM Region LIMIT 1");
    echo "First Region: <pre>" . print_r($result, true) . "</pre>";

    // Test Session
    echo "<h3>4. Testing Session</h3>";
    $session = $context->getSession();
    $session->set('test', 'working');
    echo "Session Test Value: " . $session->get('test') . "<br>";

    // Test URI
    echo "<h3>5. Testing URI</h3>";
    $uri = $context->getURI();
    echo "Current Path: " . $uri->getPath() . "<br>";
    echo "Site URL: " . $uri->getSite() . "<br>";

    echo "<h3>All Tests Passed!</h3>";

} catch (Exception $e) {
    echo "<h2>Test Failed</h2>";
    echo "Error: " . $e->getMessage();
    echo "<br>Stack Trace:<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}