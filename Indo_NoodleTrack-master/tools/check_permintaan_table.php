<?php
require_once __DIR__ . '/../config/database.php';

$conn = getDBConnection();

// Check permintaan table structure
$sql = "DESCRIBE permintaan";
$result = $conn->query($sql);

if ($result) {
    echo "Permintaan table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " " . $row['Type'] . "\n";
    }
} else {
    echo "Error checking permintaan table: " . $conn->error . "\n";
}

// Check dashboard table structure
$sql = "DESCRIBE dashboard";
$result = $conn->query($sql);

if ($result) {
    echo "\nDashboard table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " " . $row['Type'] . "\n";
    }
} else {
    echo "Error checking dashboard table: " . $conn->error . "\n";
}

$conn->close();
?>
