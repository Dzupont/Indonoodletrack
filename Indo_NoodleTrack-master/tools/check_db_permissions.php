<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

$conn = getDBConnection();

// Check database permissions
$sql = "SHOW GRANTS FOR CURRENT_USER()";
$result = $conn->query($sql);

echo "Database permissions:\n";
while ($row = $result->fetch_assoc()) {
    echo $row['Grants for root@localhost'] . "\n";
}
echo "\n";

// Test basic operations
$sql = "SELECT COUNT(*) as count FROM returns";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Number of returns: " . $row['count'] . "\n";
} else {
    echo "Error checking returns count: " . $conn->error . "\n";
}

// Test insert operation
$sql = "INSERT INTO returns (stock_id, quantity, reason, status, returned_by, approved_by, created_at) 
        VALUES (1, 10, 'Test retur', 'pending', 3, NULL, NOW())";

if ($conn->query($sql) === TRUE) {
    echo "Test insert successful\n";
    echo "Last insert ID: " . $conn->insert_id . "\n";
} else {
    echo "Error inserting test record: " . $conn->error . "\n";
}

// Test select operation
$sql = "SELECT * FROM returns WHERE id = " . $conn->insert_id;
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "Selected data:\n";
    echo "ID: " . $row['id'] . "\n";
    echo "Stock ID: " . $row['stock_id'] . "\n";
    echo "Quantity: " . $row['quantity'] . "\n";
    echo "Reason: " . $row['reason'] . "\n";
    echo "Status: " . $row['status'] . "\n";
    echo "Returned By: " . $row['returned_by'] . "\n";
    echo "Created At: " . $row['created_at'] . "\n";
} else {
    echo "Error selecting data: " . $conn->error . "\n";
}

$conn->close();
?>
