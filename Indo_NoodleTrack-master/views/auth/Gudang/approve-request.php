<?php
session_start();
require_once '../../../config/database.php';

// Check login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gudang') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get database connection
$conn = getDBConnection();

// Get request ID
$request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : 0;

if ($request_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request ID']);
    exit();
}

try {
    $conn->begin_transaction();
    
    // Get request items and their details
    $stmt = $conn->prepare("SELECT ri.*, s.* 
                           FROM request_items ri 
                           JOIN stocks s ON ri.bahan_id = s.id 
                           WHERE ri.request_id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Update stocks and request items
    foreach ($items as $item) {
        // Check if stock is available
        if ($item['current_stock'] < $item['quantity']) {
            throw new Exception("Stok tidak cukup untuk bahan: " . $item['nama']);
        }
        
        // Update stock
        $stmt = $conn->prepare("UPDATE stocks SET stok = stok - ? WHERE id = ?");
        $stmt->bind_param("di", $item['quantity'], $item['bahan_id']);
        if (!$stmt->execute()) throw new Exception($conn->error);
        
        // Update request item status
        $stmt = $conn->prepare("UPDATE request_items SET status = 'approved', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("i", $item['id']);
        if (!$stmt->execute()) throw new Exception($conn->error);
    }
    
    // Update request status
    $stmt = $conn->prepare("UPDATE requests SET status = 'approved', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    if (!$stmt->execute()) throw new Exception($conn->error);
    
    $conn->commit();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'redirect' => $_SERVER['HTTP_REFERER'] ?? 'penerimaanpermintaanmasuk.php'
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'redirect' => $_SERVER['HTTP_REFERER'] ?? 'penerimaanpermintaanmasuk.php'
    ]);
}
?>
