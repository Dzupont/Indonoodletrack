<?php
session_start();
require_once '../../../config/database.php';

// Check if user is logged in and has produksi role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'produksi') {
    header('Location: ../../login.php');
    exit();
}

// Check if cart_id is set
if (!isset($_POST['cart_id'])) {
    header('Location: keranjang.php?error=1');
    exit();
}

// Get database connection
$conn = getDBConnection();

try {
    $conn->begin_transaction();
    
    // Delete from cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $_POST['cart_id'], $_SESSION['user_id']);
    
    if (!$stmt->execute()) throw new Exception("Gagal menghapus item dari keranjang: " . $conn->error);
    
    $conn->commit();
    
    // Redirect back to the same page with success message
    $referrer = $_SERVER['HTTP_REFERER'] ?? 'keranjang.php';
    header("Location: $referrer?success=1");
    exit();
} catch (Exception $e) {
    if (isset($conn)) $conn->rollback();
    error_log("Error removing from cart: " . $e->getMessage());
    
    // Redirect back to the same page with error message
    $referrer = $_SERVER['HTTP_REFERER'] ?? 'keranjang.php';
    header("Location: $referrer?error=1");
    exit();
}
