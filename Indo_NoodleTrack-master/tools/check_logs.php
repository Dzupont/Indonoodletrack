<?php
$log_dir = __DIR__ . '/../logs';
$log_file = $log_dir . '/application.log';

if (file_exists($log_file)) {
    echo "Reading application log:\n";
    $lines = file($log_file);
    foreach ($lines as $line) {
        echo $line;
    }
} else {
    echo "Application log not found\n";
}

// Check database status
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';
$conn = getDBConnection();

// Check returns data
$sql = "SELECT * FROM returns ORDER BY created_at DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "\nLatest return record:\n";
    $row = $result->fetch_assoc();
    echo "ID: " . $row['id'] . "\n";
    echo "Stock ID: " . $row['stock_id'] . "\n";
    echo "Quantity: " . $row['quantity'] . "\n";
    echo "Reason: " . $row['reason'] . "\n";
    echo "Returned By: " . $row['returned_by'] . "\n";
    echo "Created At: " . $row['created_at'] . "\n";
} else {
    echo "\nNo return records found\n";
}

$conn->close();
?>
