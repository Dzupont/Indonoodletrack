<?php
require_once __DIR__ . '/../config/database.php';

$conn = getDBConnection();

// Check if stocks table exists
$sql = "SHOW TABLES LIKE 'stocks'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "Stocks table exists\n";
    
    // Check table structure
    $sql = "DESCRIBE stocks";
    $result = $conn->query($sql);
    
    if ($result) {
        echo "\nStocks table structure:\n";
        while ($row = $result->fetch_assoc()) {
            echo $row['Field'] . " " . $row['Type'] . "\n";
        }
    }
} else {
    echo "Stocks table does not exist\n";
}

$conn->close();
?>
