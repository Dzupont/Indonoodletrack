<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

$conn = getDBConnection();

// Check raw data in returns table
$sql = "SELECT * FROM returns";
$result = $conn->query($sql);

if ($result) {
    echo "Total returns: " . $result->num_rows . "\n\n";
    
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . "\n";
        echo "Stock ID: " . $row['stock_id'] . "\n";
        echo "Quantity: " . $row['quantity'] . "\n";
        echo "Reason: " . $row['reason'] . "\n";
        echo "Status: " . $row['status'] . "\n";
        echo "Returned By: " . $row['returned_by'] . "\n";
        echo "Approved By: " . $row['approved_by'] . "\n";
        echo "Created At: " . $row['created_at'] . "\n\n";
    }
} else {
    echo "Error querying returns: " . $conn->error . "\n";
}

$conn->close();
?>
