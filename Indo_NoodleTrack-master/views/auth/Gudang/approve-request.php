<?php
session_start();
require_once '../../../config/database.php';
require_once '../../../config/base_url.php';

// Check login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gudang') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Unauthorized', 
        'redirect' => getBaseUrl() . 'views/auth/login.php'
    ]);
    exit();
}

// Get database connection
$conn = getDBConnection();

// Get request ID
$request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : 0;

if ($request_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request ID', 
        'redirect' => getBaseUrl() . 'views/auth/Gudang/penerimaanpermintaanmasuk.php'
    ]);
    exit();
}

try {
    $conn->begin_transaction();
    
    // Get request details
    $stmt = $conn->prepare("SELECT * FROM requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();
    
    if (!$request || $request['status'] !== 'pending') {
        throw new Exception("Request tidak dalam status pending");
    }
    
    // Get request items
    $stmt = $conn->prepare("SELECT ri.*, s.* 
                           FROM request_items ri 
                           JOIN stocks s ON ri.bahan_id = s.id 
                           WHERE ri.request_id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    if (empty($items)) {
        throw new Exception("Tidak ada item dalam permintaan");
    }
    
    // Update stocks and request items
    foreach ($items as $item) {
        // Check if stock is available
        if ($item['stok'] < $item['quantity']) {
            throw new Exception("Stok tidak cukup untuk bahan: " . $item['nama'] . 
                " (Stok tersedia: " . $item['stok'] . ", Dibutuhkan: " . $item['quantity'] . ")");
        }
        
        // Update stock
        $stmt = $conn->prepare("UPDATE stocks SET stok = stok - ? WHERE id = ?");
        $stmt->bind_param("di", $item['quantity'], $item['bahan_id']);
        if (!$stmt->execute()) throw new Exception("Gagal mengupdate stok: " . $conn->error);
        
        // Update request item status
        $stmt = $conn->prepare("UPDATE request_items SET status = 'approved', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("i", $item['id']);
        if (!$stmt->execute()) throw new Exception("Gagal mengupdate status item: " . $conn->error);
    }
    
    // Update request status
    $stmt = $conn->prepare("UPDATE requests SET status = 'approved', approved_by = ?, approved_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $request_id);
    if (!$stmt->execute()) throw new Exception("Gagal mengupdate status permintaan: " . $conn->error);
    
    $conn->commit();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Permintaan berhasil disetujui',
        'redirect' => getBaseUrl() . 'views/auth/Gudang/penerimaanpermintaanmasuk.php'
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'redirect' => getBaseUrl() . 'views/auth/Gudang/penerimaanpermintaanmasuk.php'
    ]);
}
?>
