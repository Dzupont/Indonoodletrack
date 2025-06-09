<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

$conn = getDBConnection();

// Insert a simple return record
$sql = "INSERT INTO returns (stock_id, quantity, reason, status, returned_by, approved_by, created_at) 
        VALUES (1, 10, 'Test retur', 'pending', 3, NULL, NOW())";

if ($conn->query($sql) === TRUE) {
    echo "Record inserted successfully\n";
    echo "Last insert ID: " . $conn->insert_id . "\n";
} else {
    echo "Error: " . $sql . "\n" . $conn->error . "\n";
}

$conn->close();
?>
