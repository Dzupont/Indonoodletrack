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
$request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($request_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request ID']);
    exit();
}

try {
    // Get request details
    $stmt = $conn->prepare("SELECT r.*, u.username as user_name FROM requests r JOIN users u ON r.requested_by = u.id WHERE r.id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Request not found");
    }
    
    $request = $result->fetch_assoc();
    
    // Get request items
    $stmt = $conn->prepare("SELECT s.*, ri.quantity as requested_quantity, ri.status as item_status 
                           FROM request_items ri 
                           JOIN stocks s ON ri.bahan_id = s.id 
                           WHERE ri.request_id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'user_name' => $request['user_name'],
        'created_at' => date('d/m/Y H:i', strtotime($request['created_at'])),
        'items' => $items
    ]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
