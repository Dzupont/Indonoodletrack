<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

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

// Check raw_materials table structure
$sql = "DESCRIBE raw_materials";
$result = $conn->query($sql);

if ($result) {
    echo "\nRaw materials table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " " . $row['Type'] . "\n";
    }
} else {
    echo "Error checking raw materials table: " . $conn->error . "\n";
}

// Check foreign key constraints
$sql = "SELECT TABLE_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, COLUMN_NAME, REFERENCED_COLUMN_NAME 
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'returns' AND REFERENCED_TABLE_NAME IS NOT NULL";
$result = $conn->query($sql);

if ($result) {
    echo "\nForeign key constraints:\n";
    echo "Foreign key constraints:\n";
    while ($row = $result->fetch_assoc()) {
        echo "Constraint: " . $row['CONSTRAINT_NAME'] . ", References: " . $row['REFERENCED_TABLE_NAME'] . "." . $row['REFERENCED_COLUMN_NAME'] . "\n";
    }
    echo "\n";
} else {
    echo "Error checking foreign keys: " . $conn->error . "\n";
}

$conn->close();
?>
