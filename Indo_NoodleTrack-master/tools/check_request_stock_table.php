<?php
require_once __DIR__ . '/../config/database.php';

$conn = getDBConnection();

// Check request_stock table structure
$sql = "DESCRIBE request_stock";
$result = $conn->query($sql);

if ($result) {
    echo "Request Stock table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " " . $row['Type'] . "\n";
    }
} else {
    echo "Error checking request_stock table: " . $conn->error . "\n";
}

$conn->close();
?>
