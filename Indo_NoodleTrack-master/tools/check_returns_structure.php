<?php
require_once __DIR__ . '/../config/database.php';

$conn = getDBConnection();

// Check returns table structure
$sql = "DESCRIBE returns";
$result = $conn->query($sql);

if ($result) {
    echo "Returns table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " " . $row['Type'] . "\n";
    }
} else {
    echo "Error checking returns table: " . $conn->error . "\n";
}

// Check foreign key constraints
$sql = "SELECT CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_NAME = 'returns' AND CONSTRAINT_SCHEMA = '" . DB_NAME . "'";
$result = $conn->query($sql);

if ($result) {
    echo "\nForeign key constraints:\n";
    while ($row = $result->fetch_assoc()) {
        echo "Constraint: " . $row['CONSTRAINT_NAME'] . ", References: " . $row['REFERENCED_TABLE_NAME'] . "." . $row['REFERENCED_COLUMN_NAME'] . "\n";
    }
} else {
    echo "Error checking foreign key constraints: " . $conn->error . "\n";
}

$conn->close();
?>
