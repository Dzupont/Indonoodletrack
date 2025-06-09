<?php
require_once __DIR__ . '/../config/database.php';

$conn = getDBConnection();

// Check requests table structure
$sql = "DESCRIBE requests";
$result = $conn->query($sql);

if ($result) {
    echo "Requests table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " " . $row['Type'] . "\n";
    }
} else {
    echo "Error checking requests table: " . $conn->error . "\n";
}

// Check request_items table structure
$sql = "DESCRIBE request_items";
$result = $conn->query($sql);

if ($result) {
    echo "\nRequest Items table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " " . $row['Type'] . "\n";
    }
} else {
    echo "Error checking request_items table: " . $conn->error . "\n";
}

$conn->close();
?>
