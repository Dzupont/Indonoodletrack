<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

$conn = getDBConnection();

// Check stock reference
$sql = "SELECT id, nama FROM stocks WHERE id = 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "Stock reference found:\n";
    echo "ID: " . $row['id'] . "\n";
    echo "Name: " . $row['nama'] . "\n\n";
} else {
    echo "Stock reference not found\n";
}

// Check user reference
$sql = "SELECT id, username FROM users WHERE id = 3";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "User reference found:\n";
    echo "ID: " . $row['id'] . "\n";
    echo "Username: " . $row['username'] . "\n\n";
} else {
    echo "User reference not found\n";
}

// Check returns table data without joins
$sql = "SELECT * FROM returns";
$result = $conn->query($sql);

if ($result) {
    echo "Raw returns data:\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . "\n";
        echo "Stock ID: " . $row['stock_id'] . "\n";
        echo "Quantity: " . $row['quantity'] . "\n";
        echo "Reason: " . $row['reason'] . "\n";
        echo "Status: " . $row['status'] . "\n";
        echo "Returned By: " . $row['returned_by'] . "\n";
        echo "Created At: " . $row['created_at'] . "\n\n";
    }
} else {
    echo "Error checking raw returns data: " . $conn->error . "\n";
}

$conn->close();
?>
