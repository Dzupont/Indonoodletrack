<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

$conn = getDBConnection();

// Check database name
$sql = "SELECT DATABASE() as current_db";
$result = $conn->query($sql);
$current_db = $result->fetch_assoc()['current_db'];

echo "Current database: " . $current_db . "\n\n";

echo "Database information:\n";
$sql = "SELECT TABLE_NAME, TABLE_COMMENT FROM INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_SCHEMA = '" . DB_NAME . "'";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "Table: " . $row['TABLE_NAME'] . ", Comment: " . $row['TABLE_COMMENT'] . "\n";
    }
    echo "\n";
} else {
    echo "Error checking tables: " . $conn->error . "\n";
}

// Check returns table structure
$sql = "DESCRIBE returns";
$result = $conn->query($sql);

if ($result) {
    echo "Returns table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo "Field: " . $row['Field'] . ", Type: " . $row['Type'] . ", Null: " . $row['Null'] . ", Key: " . $row['Key'] . "\n";
    }
    echo "\n";
} else {
    echo "Error checking returns structure: " . $conn->error . "\n";
}

// Check foreign key constraints
$sql = "SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = '" . DB_NAME . "' AND TABLE_NAME = 'returns'";
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

$conn->close();
?>
