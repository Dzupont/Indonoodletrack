<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

$conn = getDBConnection();

// Check stocks
$sql = "SELECT id, nama FROM stocks";
$result = $conn->query($sql);

if ($result) {
    echo "Stocks:\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . ", Name: " . $row['nama'] . "\n";
    }
    echo "\n";
} else {
    echo "Error checking stocks: " . $conn->error . "\n";
}

// Check users with role produksi
$sql = "SELECT id, username, role FROM users WHERE role = 'produksi'";
$result = $conn->query($sql);

if ($result) {
    echo "Produksi Users:\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . ", Username: " . $row['username'] . ", Role: " . $row['role'] . "\n";
    }
    echo "\n";
} else {
    echo "Error checking users: " . $conn->error . "\n";
}

$conn->close();
?>
