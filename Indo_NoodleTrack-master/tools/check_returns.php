<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

$conn = getDBConnection();

// Check returns table
$sql = "SELECT r.*, s.nama as stock_name, u1.username as returned_by_name, u2.username as approved_by_name 
        FROM returns r 
        LEFT JOIN stocks s ON r.stock_id = s.id 
        LEFT JOIN users u1 ON r.returned_by = u1.id 
        LEFT JOIN users u2 ON r.approved_by = u2.id 
        ORDER BY r.created_at DESC";
$result = $conn->query($sql);

if ($result) {
    echo "Total returns: " . $result->num_rows . "\n\n";
    
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . "\n";
        echo "Stock: " . $row['stock_name'] . "\n";
        echo "Quantity: " . $row['quantity'] . "\n";
        echo "Reason: " . $row['reason'] . "\n";
        echo "Status: " . $row['status'] . "\n";
        echo "Returned By: " . $row['returned_by_name'] . "\n";
        echo "Approved By: " . ($row['approved_by_name'] ?? 'Not approved') . "\n";
        echo "Created At: " . $row['created_at'] . "\n\n";
    }
} else {
    echo "Error querying returns: " . $conn->error . "\n";
}

$conn->close();
?>
