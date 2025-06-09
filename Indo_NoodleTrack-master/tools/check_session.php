<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/session.php';
require_once $rootPath . '/config/database.php';

// Simulate login as a produksi user
$_SESSION['user_id'] = 3; // ID user lolo
$_SESSION['role'] = 'produksi';

// Check session and user
if (!requireLogin()) {
    echo "Session not set\n";
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$conn = getDBConnection();

// Get user data
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    echo "Session valid:\n";
    echo "User ID: " . $user_id . "\n";
    echo "Username: " . $user['username'] . "\n";
    echo "Role: " . $role . "\n\n";
} else {
    echo "User not found in database\n";
}

// Try to insert a return record
$test_stock_id = 1; // Tepung Terigu
$quantity = 10;
$reason = "Test retur";
$status = "pending";
$approved_by = null;
$created_at = date('Y-m-d H:i:s');

$sql = "INSERT INTO returns (stock_id, quantity, reason, status, returned_by, approved_by, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("idssiii", $test_stock_id, $quantity, $reason, $status, $user_id, $approved_by, $created_at);
    
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
