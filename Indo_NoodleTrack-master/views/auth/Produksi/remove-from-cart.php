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

// Check if cart_id is set
if (!isset($_POST['cart_id'])) {
    header('Location: keranjang.php?error=1');
    exit();
}

try {
    $cart_id = $_POST['cart_id'];
    
    // Delete cart item
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $_SESSION['user_id']);
    
    if (!$stmt->execute()) {
        throw new Exception("Error deleting cart item: " . $conn->error);
    }
    
    // Redirect back to keranjang.php with success message
    header('Location: keranjang.php?success=1');
    exit();
} catch (Exception $e) {
    error_log("Error in remove-from-cart.php: " . $e->getMessage());
    header('Location: keranjang.php?error=1&error_msg=' . urlencode($e->getMessage()));
    exit();
}
