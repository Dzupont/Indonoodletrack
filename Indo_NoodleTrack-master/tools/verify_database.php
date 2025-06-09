<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

$conn = getDBConnection();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Database connection successful\n";

// Check if database exists and selected
if ($conn->select_db(DB_NAME)) {
    echo "Database selected successfully\n";
} else {
    die("Error selecting database: " . $conn->error);
}

// Check if returns table exists and has correct structure
$sql = "SHOW CREATE TABLE returns";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "Returns table structure:\n" . $row['Create Table'] . "\n";
} else {
    die("Error: Returns table does not exist or cannot be accessed: " . $conn->error);
}

// Check if data exists
$sql = "SELECT COUNT(*) as count FROM returns";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "Number of returns: " . $row['count'] . "\n";
} else {
    die("Error checking returns count: " . $conn->error);
}

// Try to insert and immediately select
$test_stock_id = 1;
$test_user_id = 3;
$quantity = 10;
$reason = "Test retur";
$status = "pending";
$approved_by = null;

// Use simple query without NOW()
$sql = "INSERT INTO returns (stock_id, quantity, reason, status, returned_by, approved_by, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $created_at = date('Y-m-d H:i:s');
    $stmt->bind_param("idssiiis", $test_stock_id, $quantity, $reason, $status, $test_user_id, $approved_by, $created_at);
    
    if ($stmt->execute()) {
        echo "Test record inserted successfully\n";
        
        // Immediately select the inserted record
        $sql = "SELECT * FROM returns WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $conn->insert_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "Inserted data verified:\n";
            echo "ID: " . $row['id'] . "\n";
            echo "Stock ID: " . $row['stock_id'] . "\n";
            echo "Quantity: " . $row['quantity'] . "\n";
            echo "Reason: " . $row['reason'] . "\n";
            echo "Status: " . $row['status'] . "\n";
            echo "Returned By: " . $row['returned_by'] . "\n";
            echo "Created At: " . $row['created_at'] . "\n";
        } else {
            die("Error: Could not verify inserted data: " . $conn->error);
        }
    } else {
        die("Error inserting test record: " . $stmt->error);
    }
} else {
    die("Error preparing insert statement: " . $conn->error);
}

$conn->close();
?>
