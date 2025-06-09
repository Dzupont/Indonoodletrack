<?php
// Logging configuration
$log_dir = __DIR__ . '/../logs';

// Create logs directory if not exists
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0777, true);
}

// Set error log path
$log_file = $log_dir . '/application.log';
ini_set('error_log', $log_file);

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    error_log("[" . date('Y-m-d H:i:s') . "] Error: [$errno] $errstr in $errfile on line $errline");
    return false;
}

// Custom exception handler
function customExceptionHandler($exception) {
    error_log("[" . date('Y-m-d H:i:s') . "] Exception: " . $exception->getMessage() . 
              " in " . $exception->getFile() . 
              " on line " . $exception->getLine() . 
              "\nTrace: " . $exception->getTraceAsString());
}

// Log functions for tambahretur.php
function logSessionStatus() {
    error_log("[" . date('Y-m-d H:i:s') . "] Session status:");
    error_log("[" . date('Y-m-d H:i:s') . "] Session ID: " . session_id());
    error_log("[" . date('Y-m-d H:i:s') . "] Session variables:");
    foreach ($_SESSION as $key => $value) {
        error_log("[" . date('Y-m-d H:i:s') . "] $key: $value");
    }
}

function logDatabaseStatus($conn) {
    error_log("[" . date('Y-m-d H:i:s') . "] Database connection status:");
    if ($conn) {
        error_log("[" . date('Y-m-d H:i:s') . "] Database connection successful");
        error_log("[" . date('Y-m-d H:i:s') . "] Server info: " . $conn->server_info);
        error_log("[" . date('Y-m-d H:i:s') . "] Database: " . DB_NAME);
        
        // Check if returns table exists
        $sql = "SHOW TABLES LIKE 'returns'";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            error_log("[" . date('Y-m-d H:i:s') . "] Returns table exists");
        } else {
            error_log("[" . date('Y-m-d H:i:s') . "] Returns table does not exist");
        }
        
        // Check if stocks table exists
        $sql = "SHOW TABLES LIKE 'stocks'";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            error_log("[" . date('Y-m-d H:i:s') . "] Stocks table exists");
        } else {
            error_log("[" . date('Y-m-d H:i:s') . "] Stocks table does not exist");
        }
    } else {
        error_log("[" . date('Y-m-d H:i:s') . "] Database connection failed: " . $conn->connect_error);
    }
}

function logFormData($stock_id, $quantity, $reason) {
    error_log("[" . date('Y-m-d H:i:s') . "] Form data received:");
    error_log("[" . date('Y-m-d H:i:s') . "] stock_id: $stock_id");
    error_log("[" . date('Y-m-d H:i:s') . "] quantity: $quantity");
    error_log("[" . date('Y-m-d H:i:s') . "] reason: $reason");
}

function logInsertAttempt($stock_id, $quantity, $reason, $returned_by, $approved_by, $created_at) {
    error_log("[" . date('Y-m-d H:i:s') . "] Attempting to insert return with data:");
    error_log("[" . date('Y-m-d H:i:s') . "] stock_id: $stock_id");
    error_log("[" . date('Y-m-d H:i:s') . "] quantity: $quantity");
    error_log("[" . date('Y-m-d H:i:s') . "] reason: $reason");
    error_log("[" . date('Y-m-d H:i:s') . "] returned_by: $returned_by");
    error_log("[" . date('Y-m-d H:i:s') . "] approved_by: $approved_by");
    error_log("[" . date('Y-m-d H:i:s') . "] created_at: $created_at");
}

function logAutocommitStatus($conn) {
    $autocommit = $conn->autocommit(true);
    error_log("[" . date('Y-m-d H:i:s') . "] Autocommit status: " . ($autocommit ? "enabled" : "disabled"));
}

function logTransactionStart() {
    error_log("[" . date('Y-m-d H:i:s') . "] Transaction started");
}

function logStatementPrepared() {
    error_log("[" . date('Y-m-d H:i:s') . "] Statement prepared successfully");
}

function logParametersBound() {
    error_log("[" . date('Y-m-d H:i:s') . "] Parameters bound successfully");
}

function logStatementExecuted() {
    error_log("[" . date('Y-m-d H:i:s') . "] Statement executed successfully");
}

function logInsertID($last_id) {
    error_log("[" . date('Y-m-d H:i:s') . "] Last insert ID: $last_id");
}

function logTransactionCommit() {
    error_log("[" . date('Y-m-d H:i:s') . "] Transaction committed");
}

function logDataVerification($row) {
    error_log("[" . date('Y-m-d H:i:s') . "] Data verified successfully:");
    error_log("[" . date('Y-m-d H:i:s') . "] ID: " . $row['id']);
    error_log("[" . date('Y-m-d H:i:s') . "] Stock ID: " . $row['stock_id']);
    error_log("[" . date('Y-m-d H:i:s') . "] Quantity: " . $row['quantity']);
    error_log("[" . date('Y-m-d H:i:s') . "] Reason: " . $row['reason']);
    error_log("[" . date('Y-m-d H:i:s') . "] Returned By: " . $row['returned_by']);
    error_log("[" . date('Y-m-d H:i:s') . "] Created At: " . $row['created_at']);
}

function logErrorAndRollback($error) {
    error_log("[" . date('Y-m-d H:i:s') . "] Error: $error");
    error_log("[" . date('Y-m-d H:i:s') . "] Transaction rolled back");
}

function logPrepareErrorAndRollback($error) {
    error_log("[" . date('Y-m-d H:i:s') . "] Prepare error: $error");
    error_log("[" . date('Y-m-d H:i:s') . "] Transaction rolled back");
}

// Set handlers
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');
?>
