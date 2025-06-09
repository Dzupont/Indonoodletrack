<?php
session_start();
require_once '../../../config/database.php';
require_once '../../../config/session.php';

// Check if user is logged in and has proper role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gudang') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $return_id = $_POST['return_id'] ?? null;
    $rejection_reason = $_POST['rejection_reason'] ?? '';
    
    if (!$return_id) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Return ID is required']);
        exit;
    }

    if (empty($rejection_reason)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Alasan penolakan harus diisi']);
        exit;
    }

    $conn = getDBConnection();
    
    // Update return status
    $sql = "UPDATE returns SET 
            status = 'rejected',
            rejected_by = ?,
            rejected_at = NOW(),
            rejection_reason = ?
            WHERE id = ? AND status IN ('pending', 'rejected')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $_SESSION['user_id'], $rejection_reason, $return_id);
    
    if ($stmt->execute()) {
        // Get quantity of rejected return
        $sql = "SELECT quantity FROM returns WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $return_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $return_data = $result->fetch_assoc();

        // Update rejected returns in dashboard
        $sql = "UPDATE dashboard SET retur_bahan_baku_rejected = retur_bahan_baku_rejected + ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $return_data['quantity']);
        $stmt->execute();

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Retur berhasil ditolak']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Gagal menolak retur: ' . $conn->error]);
    }
    exit;
}

header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Invalid request method']);
