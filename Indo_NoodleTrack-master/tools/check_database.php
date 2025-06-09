<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

$conn = getDBConnection();

// Check if database exists
$sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    echo "Database exists\n";
} else {
    echo "Database does not exist\n";
    exit();
}

// Check if returns table exists
$sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_SCHEMA = '" . DB_NAME . "' AND TABLE_NAME = 'returns'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    echo "Returns table exists\n";
} else {
    echo "Returns table does not exist\n";
    exit();
}

// Check table structure
$sql = "DESCRIBE returns";
$result = $conn->query($sql);

if ($result) {
    echo "Table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo "Field: " . $row['Field'] . ", Type: " . $row['Type'] . ", Null: " . $row['Null'] . ", Key: " . $row['Key'] . ", Default: " . $row['Default'] . "\n";
    }
    echo "\n";
} else {
    echo "Error checking table structure: " . $conn->error . "\n";
}

// Try to insert a test record
$test_stock_id = 1; // Tepung Terigu
$test_user_id = 3;  // lolo
$quantity = 10;
$reason = "Test retur";
$status = "pending";
$approved_by = null;
$created_at = date('Y-m-d H:i:s');

$sql = "INSERT INTO returns (stock_id, quantity, reason, status, returned_by, approved_by, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("idssiii", $test_stock_id, $quantity, $reason, $status, $test_user_id, $approved_by, $created_at);
    
    if ($stmt->execute()) {
        echo "Test record inserted successfully\n";
        echo "Last insert ID: " . $conn->insert_id . "\n";
    } else {
        echo "Error inserting test record: " . $stmt->error . "\n";
    }
} else {
    echo "Error preparing insert statement: " . $conn->error . "\n";
}

$conn->close();
?>
