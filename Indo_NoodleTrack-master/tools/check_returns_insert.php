<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

$conn = getDBConnection();

// Get the last insert ID
$last_id = $conn->insert_id;

// Check if there are any recent returns
$sql = "SELECT * FROM returns ORDER BY created_at DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "Last return record:\n";
    echo "ID: " . $row['id'] . "\n";
    echo "Stock ID: " . $row['stock_id'] . "\n";
    echo "Quantity: " . $row['quantity'] . "\n";
    echo "Reason: " . $row['reason'] . "\n";
    echo "Returned By: " . $row['returned_by'] . "\n";
    echo "Approved By: " . ($row['approved_by'] ?? 'Not approved') . "\n";
    echo "Created At: " . $row['created_at'] . "\n\n";
    
    // Check if stock was updated
    $sql = "SELECT stok FROM stocks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $row['stock_id']);
    $stmt->execute();
    $stock_result = $stmt->get_result();
    
    if ($stock_result && $stock_result->num_rows > 0) {
        $stock_row = $stock_result->fetch_assoc();
        echo "Stock updated:\n";
        echo "Stock ID: " . $row['stock_id'] . "\n";
        echo "Current Stock: " . $stock_row['stok'] . "\n";
    } else {
        echo "Stock not found\n";
    }
} else {
    echo "No recent returns found\n";
}

$conn->close();
?>
