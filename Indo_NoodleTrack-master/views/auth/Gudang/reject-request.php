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
    
    // Update request items status
    $stmt = $conn->prepare("UPDATE request_items SET status = 'rejected', updated_at = CURRENT_TIMESTAMP WHERE request_id = ?");
    $stmt->bind_param("i", $request_id);
    if (!$stmt->execute()) throw new Exception($conn->error);
    
    // Update request status
    $stmt = $conn->prepare("UPDATE requests SET status = 'rejected', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
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
