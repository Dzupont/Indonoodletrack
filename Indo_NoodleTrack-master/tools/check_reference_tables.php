<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

$conn = getDBConnection();

// Check stocks
$sql = "SELECT id, nama, stok FROM stocks";
$result = $conn->query($sql);

if ($result) {
    echo "Stocks:\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . ", Name: " . $row['nama'] . ", Stock: " . $row['stok'] . "\n";
    }
    echo "\n";
} else {
    echo "Error checking stocks: " . $conn->error . "\n";
}

// Check users
$sql = "SELECT id, username, role FROM users";
$result = $conn->query($sql);

if ($result) {
    echo "Users:\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . ", Username: " . $row['username'] . ", Role: " . $row['role'] . "\n";
    }
    echo "\n";
} else {
    echo "Error checking users: " . $conn->error . "\n";
}

$conn->close();
?>
