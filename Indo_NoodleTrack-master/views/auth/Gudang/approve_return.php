<?php
session_start();
require_once '../../../config/database.php';
require_once '../../../config/session.php';
require_once '../../../config/base_url.php';

// Check if user is logged in and has proper role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gudang') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Unauthorized',
        'redirect' => getBaseUrl() . 'views/auth/login.php'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $return_id = $_POST['return_id'] ?? null;
    
    if (!$return_id) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'message' => 'ID retur diperlukan',
            'redirect' => getBaseUrl() . 'views/auth/Gudang/returmasuk.php'
        ]);
        exit;
    }

    $conn = getDBConnection();
    
    try {
        $conn->begin_transaction();
        
        // Get return details
        $sql = "SELECT r.*, s.* FROM returns r 
                JOIN stocks s ON r.material_id = s.id 
                WHERE r.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $return_id);
        $stmt->execute();
        $return_data = $stmt->get_result()->fetch_assoc();
        
        if (!$return_data || $return_data['status'] !== 'pending') {
            throw new Exception("Retur tidak dalam status pending");
        }

        // Update return status
        $sql = "UPDATE returns SET 
                status = 'approved',
                approved_by = ?,
                approved_at = NOW()
                WHERE id = ? AND status = 'pending'";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $_SESSION['user_id'], $return_id);
        if (!$stmt->execute()) {
            throw new Exception("Gagal mengupdate status retur: " . $conn->error);
        }

        // Update stock
        $sql = "UPDATE stocks SET stok = stok + ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $return_data['quantity'], $return_data['material_id']);
        if (!$stmt->execute()) {
            throw new Exception("Gagal mengupdate stok: " . $conn->error);
        }

        // Update total retur in dashboard
        $sql = "UPDATE dashboard SET retur_bahan_baku = retur_bahan_baku + ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("d", $return_data['quantity']);
        if (!$stmt->execute()) {
            throw new Exception("Gagal mengupdate dashboard: " . $conn->error);
        }

        $conn->commit();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Retur berhasil disetujui',
            'redirect' => getBaseUrl() . 'views/auth/Gudang/returmasuk.php'
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'redirect' => getBaseUrl() . 'views/auth/Gudang/returmasuk.php'
        ]);
    }
    exit;
}

header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'message' => 'Metode request tidak valid',
    'redirect' => getBaseUrl() . 'views/auth/Gudang/returmasuk.php'
]);
