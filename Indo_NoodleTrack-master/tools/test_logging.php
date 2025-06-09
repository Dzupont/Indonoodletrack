<?php
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Test logging
error_log("Test error log entry");
error_log("This is a test message");

// Test error
trigger_error("This is a test error", E_USER_WARNING);

// Test error logging with custom message
error_log("Custom error message: " . print_r($_SERVER, true));

// Test database connection
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

$conn = getDBConnection();
error_log("Database connection successful");

// Test query
$sql = "SELECT * FROM returns";
$result = $conn->query($sql);
error_log("Query executed: " . ($result ? "success" : "failed"));

$conn->close();
?>
