<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';
require_once $rootPath . '/config/session.php';

// Simulate login as a produksi user
$_SESSION['user_id'] = 3; // ID user lolo
$_SESSION['role'] = 'produksi';

// Get database connection
$conn = getDBConnection();

// Check if autocommit is enabled
echo "Autocommit: " . ($conn->autocommit(true) ? "enabled" : "disabled") . "\n\n";

// Simulate the same query as returbahanbaku.php
$sql = "SELECT r.*, s.nama as stock_name, u.username as returned_by_name, u2.username as approved_by_name
        FROM returns r
        LEFT JOIN stocks s ON r.stock_id = s.id 
        LEFT JOIN users u ON r.returned_by = u.id 
        LEFT JOIN users u2 ON r.approved_by = u2.id
        ORDER BY r.created_at DESC";

$result = $conn->query($sql);

if ($result) {
    echo "Query executed successfully\n";
    echo "Number of returns: " . $result->num_rows . "\n\n";
    
    while ($row = $result->fetch_assoc()) {
        echo "Return ID: " . $row['id'] . "\n";
        echo "Stock ID: " . $row['stock_id'] . "\n";
        echo "Stock Name: " . $row['stock_name'] . "\n";
        echo "Quantity: " . $row['quantity'] . "\n";
        echo "Reason: " . $row['reason'] . "\n";
        echo "Status: " . $row['status'] . "\n";
        echo "Returned By: " . $row['returned_by_name'] . "\n";
        echo "Approved By: " . ($row['approved_by_name'] ?? 'Not approved') . "\n";
        echo "Created At: " . $row['created_at'] . "\n\n";
    }
} else {
    echo "Error executing query: " . $conn->error . "\n";
}

$conn->close();
?>
