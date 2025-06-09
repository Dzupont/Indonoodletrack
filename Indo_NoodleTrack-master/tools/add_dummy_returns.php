<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

$conn = getDBConnection();

// Use valid stock ID (1-19)
$stock_id = 1; // Tepung Terigu
$stock_sql = "SELECT nama FROM stocks WHERE id = ?";
$stock_stmt = $conn->prepare($stock_sql);
$stock_stmt->bind_param("i", $stock_id);
$stock_stmt->execute();
$stock = $stock_stmt->get_result()->fetch_assoc();

// Use valid user ID (produksi)
$returned_by = 3; // ID user produksi lolo

// Add a simple return
$quantity = 10;
$reason = "Kualitas bahan tidak sesuai standar";
$status = "pending";
$approved_by = null;
$created_at = date('Y-m-d H:i:s');

// Insert return
$sql = "INSERT INTO returns (stock_id, quantity, reason, status, returned_by, approved_by, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("idssiii", $stock_id, $quantity, $reason, $status, $returned_by, $approved_by, $created_at);
    
    if ($stmt->execute()) {
        echo "Return berhasil ditambahkan untuk stock: " . $stock['nama'] . "\n";
        echo "Last insert ID: " . $conn->insert_id . "\n";
    } else {
        echo "Error: " . $stmt->error . "\n";
    }
} else {
    echo "Error preparing statement: " . $conn->error . "\n";
}

$conn->close();
?>
