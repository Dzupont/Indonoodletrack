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

// Check if required parameters are set
if (!isset($_POST['bahan_id']) || !isset($_POST['quantity'])) {
    header('Location: dashboardproduksi.php?error=1');
    exit();
}

try {
    $bahan_id = $_POST['bahan_id'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id'];
    
    // Check if cart item already exists
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND bahan_id = ?");
    $stmt->bind_param("ii", $user_id, $bahan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing cart item
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;
        
        $stmt = $conn->prepare("UPDATE cart SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("di", $new_quantity, $row['id']);
        if (!$stmt->execute()) {
            throw new Exception("Error updating cart item: " . $conn->error);
        }
    } else {
        // Insert new cart item
        $stmt = $conn->prepare("INSERT INTO cart (user_id, bahan_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $user_id, $bahan_id, $quantity);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting cart item: " . $conn->error);
        }
    }
    
    // Get the ID of the last inserted/updated cart item
    $cart_id = $result->num_rows > 0 ? $row['id'] : $conn->insert_id;
    
    // Get cart item details
    $stmt = $conn->prepare("SELECT s.*, c.quantity as cart_quantity 
                           FROM stocks s 
                           JOIN cart c ON c.bahan_id = s.id 
                           WHERE c.id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $cart_item = $result->fetch_assoc();
        
        // Redirect to keranjang.php with success message
        header('Location: keranjang.php?success=1&cart_item=' . json_encode($cart_item));
        exit();
    } else {
        throw new Exception("Cart item not found");
    }
} catch (Exception $e) {
    error_log("Error in add-to-cart.php: " . $e->getMessage());
    header('Location: dashboardproduksi.php?error=1&error_msg=' . urlencode($e->getMessage()));
    exit();
}
