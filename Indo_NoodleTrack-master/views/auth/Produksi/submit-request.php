<?php
session_start();
require_once '../../../config/database.php';

// Check if user is logged in and has produksi role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'produksi') {
    header('Location: ../../login.php');
    exit();
}

// Get database connection
$conn = getDBConnection();

// Check if selected_items is set
if (!isset($_POST['selected_items']) || empty($_POST['selected_items'])) {
    header('Location: keranjang.php?error=1');
    exit();
}

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Get current timestamp
    $created_at = date('Y-m-d H:i:s');
    
    // Insert into requests table
    $stmt = $conn->prepare("INSERT INTO requests (requested_by, status, created_at) VALUES (?, 'pending', ?)");
    $stmt->bind_param("is", $_SESSION['user_id'], $created_at);
    if (!$stmt->execute()) {
        throw new Exception("Error inserting request: " . $conn->error);
    }

    // Get the request ID
    $request_id = $conn->insert_id;

    // Store all cart items for later use
    $cart_items = [];

    // Process each selected item
    foreach ($_POST['selected_items'] as $cart_id) {
        // Get cart item details
        $cart_stmt = $conn->prepare("SELECT c.*, s.* 
                                    FROM cart c 
                                    JOIN stocks s ON c.bahan_id = s.id 
                                    WHERE c.id = ? AND c.user_id = ?");
        $cart_stmt->bind_param("ii", $cart_id, $_SESSION['user_id']);
        $cart_stmt->execute();
        $cart_result = $cart_stmt->get_result();
        
        if ($cart_result->num_rows > 0) {
            $cart_item = $cart_result->fetch_assoc();
            $cart_items[] = $cart_item;
            
            // Insert into request_items table
            $item_stmt = $conn->prepare("INSERT INTO request_items (request_id, bahan_id, quantity, status, created_at) 
                                       VALUES (?, ?, ?, 'pending', ?)");
            $item_stmt->bind_param("iddd", $request_id, $cart_item['bahan_id'], $cart_item['quantity'], $created_at);
            if (!$item_stmt->execute()) {
                throw new Exception("Error inserting request item: " . $conn->error);
            }
            
            // Delete from cart after successful insertion
            $delete_stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $delete_stmt->bind_param("ii", $cart_id, $_SESSION['user_id']);
            if (!$delete_stmt->execute()) {
                throw new Exception("Error deleting from cart: " . $conn->error);
            }
        }
    }
    
    // Log activity for production
    $activity_stmt = $conn->prepare("INSERT INTO activity_logs (user_id, activity_type, description) 
                                   VALUES (?, 'request_submitted', ?)");
    $description = "Submitted request #" . $request_id . " with items: " . 
                   implode(", ", array_map(function($item) {
                       return htmlspecialchars($item['nama']) . " (" . $item['quantity'] . " " . $item['satuan'] . ")";
                   }, $cart_items));
    $activity_stmt->bind_param("is", $_SESSION['user_id'], $description);
    if (!$activity_stmt->execute()) {
        throw new Exception("Error logging activity: " . $conn->error);
    }
    
    // Commit transaction
    $conn->commit();
    
    // Redirect back to produksi dashboard with success message
    header('Location: ../Produksi/dashboardproduksi.php?success=1&message=Permintaan%20berhasil%20diajukan');
    exit();
    
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn)) {
        $conn->rollback();
    }
    error_log("Error in submit-request.php: " . $e->getMessage());
    header('Location: keranjang.php?error=1&error_msg=' . urlencode($e->getMessage()));
    exit();
}
