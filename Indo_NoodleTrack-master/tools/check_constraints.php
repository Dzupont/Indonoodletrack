<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

$conn = getDBConnection();

// Check foreign key constraints
$sql = "SELECT TABLE_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, COLUMN_NAME, REFERENCED_COLUMN_NAME 
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'returns' AND REFERENCED_TABLE_NAME IS NOT NULL";
$result = $conn->query($sql);

if ($result) {
    echo "Foreign key constraints:\n";
    while ($row = $result->fetch_assoc()) {
        echo "Constraint: " . $row['CONSTRAINT_NAME'] . ", References: " . $row['REFERENCED_TABLE_NAME'] . "." . $row['REFERENCED_COLUMN_NAME'] . "\n";
    }
    echo "\n";
} else {
    echo "Error checking foreign keys: " . $conn->error . "\n";
}

// Check if there are any issues with the foreign keys
$sql = "SELECT r.*, s.nama as stock_name, u1.username as returned_by_name, u2.username as approved_by_name
        FROM returns r
        LEFT JOIN stocks s ON r.stock_id = s.id 
        LEFT JOIN users u1 ON r.returned_by = u1.id 
        LEFT JOIN users u2 ON r.approved_by = u2.id
        WHERE s.id IS NULL OR u1.id IS NULL OR (r.approved_by IS NOT NULL AND u2.id IS NULL)";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "Found issues with foreign key references:\n";
    while ($row = $result->fetch_assoc()) {
        echo "Return ID: " . $row['id'] . "\n";
        echo "Stock ID: " . $row['stock_id'] . "\n";
        echo "Returned By: " . $row['returned_by'] . "\n";
        echo "Approved By: " . $row['approved_by'] . "\n";
        echo "Stock Name: " . ($row['stock_name'] ?? 'NULL') . "\n";
        echo "Returned By Name: " . ($row['returned_by_name'] ?? 'NULL') . "\n";
        echo "Approved By Name: " . ($row['approved_by_name'] ?? 'NULL') . "\n\n";
    }
} else {
    echo "No foreign key reference issues found\n";
}

$conn->close();
?>
