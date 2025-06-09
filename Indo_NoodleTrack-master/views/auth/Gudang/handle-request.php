<?php
session_start();
require_once '../../../config/database.php';

// Check if user is logged in and has gudang role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gudang') {
    header('Location: ../../login.php');
    exit();
}

// Get database connection
$conn = getDBConnection();

// Check required parameters
if (!isset($_POST['request_id']) || !isset($_POST['action'])) {
    header('Location: penerimaanpermintaanmasuk.php?error=1');
    exit();
}

try {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];
    $current_time = date('Y-m-d H:i:s');
    
    // Start transaction
    $conn->begin_transaction();
    
    // Get request details
    $stmt = $conn->prepare("SELECT r.*, s.* 
                           FROM requests r 
                           JOIN stocks s ON r.material_id = s.id 
                           WHERE r.id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Request not found");
    }
    
    $request = $result->fetch_assoc();
    
    // Update request status
    $update_stmt = $conn->prepare("UPDATE requests 
                                  SET status = ?, 
                                      notes = CONCAT(notes, ' - ', ?),
                                      updated_at = ?
                                  WHERE id = ?");
    
    $notes = "Diproses oleh Gudang pada " . date('d/m/Y H:i');
    
    if ($action === 'approve') {
        // Check if stock is available
        if ($request['stok'] < $request['quantity']) {
            throw new Exception("Stok tidak mencukupi");
        }
        
        // Update stock
        $stock_stmt = $conn->prepare("UPDATE stocks 
                                     SET stok = stok - ?, 
                                         updated_at = ?
                                     WHERE id = ?");
        $stock_stmt->bind_param("ids", $request['quantity'], $current_time, $request['material_id']);
        if (!$stock_stmt->execute()) {
            throw new Exception("Error updating stock: " . $conn->error);
        }
        
        // Update request
        $update_stmt->bind_param("sssi", 'approved', $notes, $current_time, $request_id);
        
        // Log activity
        $activity_stmt = $conn->prepare("INSERT INTO activity_logs (user_id, activity_type, description) 
                                       VALUES (?, 'request_approved', ?)");
        $activity_desc = "Menyetujui permintaan bahan baku: " . $request['nama'] . " sebanyak " . $request['quantity'] . " " . $request['satuan'];
        $activity_stmt->bind_param("is", $_SESSION['user_id'], $activity_desc);
        if (!$activity_stmt->execute()) {
            throw new Exception("Error logging activity: " . $conn->error);
        }
    } else if ($action === 'reject') {
        // Update request
        $update_stmt->bind_param("sssi", 'rejected', $notes, $current_time, $request_id);
        
        // Log activity
        $activity_stmt = $conn->prepare("INSERT INTO activity_logs (user_id, activity_type, description) 
                                       VALUES (?, 'request_rejected', ?)");
        $activity_desc = "Menolak permintaan bahan baku: " . $request['nama'] . " sebanyak " . $request['quantity'] . " " . $request['satuan'];
        $activity_stmt->bind_param("is", $_SESSION['user_id'], $activity_desc);
        if (!$activity_stmt->execute()) {
            throw new Exception("Error logging activity: " . $conn->error);
        }
    } else {
        throw new Exception("Invalid action");
    }
    
    if (!$update_stmt->execute()) {
        throw new Exception("Error updating request: " . $conn->error);
    }
    
    // Commit transaction
    $conn->commit();
    
    // Redirect back to penerimaanpermintaanmasuk.php
    header('Location: penerimaanpermintaanmasuk.php?success=1');
    exit();
    
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn)) {
        $conn->rollback();
    }
    error_log("Error in handle-request.php: " . $e->getMessage());
    header('Location: penerimaanpermintaanmasuk.php?error=1&error_msg=' . urlencode($e->getMessage()));
    exit();
}
