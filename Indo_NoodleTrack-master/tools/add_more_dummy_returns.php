<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

$conn = getDBConnection();

// Stock IDs yang valid
$stock_ids = [1, 2, 3, 4, 5]; // Tepung Terigu, Tepung Tapioka, Air, Garam, Telur

// User IDs dengan role produksi
$user_ids = [3, 6, 7, 19, 21]; // lolo, handayani, tri, ceca, produksi1

// Possible reasons for returns
$reasons = [
    'Kualitas bahan tidak sesuai standar',
    'Bahan rusak saat pengiriman',
    'Jumlah bahan tidak sesuai dengan pesanan',
    'Bahan kadaluarsa',
    'Kemasan bahan rusak'
];

// Add multiple dummy returns
for ($i = 0; $i < 5; $i++) {
    // Pilih stock dan user secara acak
    $stock_id = $stock_ids[array_rand($stock_ids)];
    $returned_by = $user_ids[array_rand($user_ids)];
    
    // Get stock name
    $stock_sql = "SELECT nama FROM stocks WHERE id = ?";
    $stock_stmt = $conn->prepare($stock_sql);
    $stock_stmt->bind_param("i", $stock_id);
    $stock_stmt->execute();
    $stock = $stock_stmt->get_result()->fetch_assoc();

    // Generate random data
    $quantity = rand(1, 50);
    $reason = $reasons[array_rand($reasons)];
    $status = $i < 3 ? 'pending' : 'approved'; // First 3 are pending
    $approved_by = $status === 'approved' ? 1 : null; // Gudang user ID
    $created_at = date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'));

    // Insert return
    $sql = "INSERT INTO returns (stock_id, quantity, reason, status, returned_by, approved_by, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("idssiii", $stock_id, $quantity, $reason, $status, $returned_by, $approved_by, $created_at);
        
        if ($stmt->execute()) {
            echo "Return berhasil ditambahkan:\n";
            echo "- Stock: " . $stock['nama'] . "\n";
            echo "- Quantity: " . $quantity . "\n";
            echo "- Status: " . $status . "\n";
            echo "- Created At: " . $created_at . "\n\n";
        } else {
            echo "Error: " . $stmt->error . "\n";
        }
    } else {
        echo "Error preparing statement: " . $conn->error . "\n";
    }
}

$conn->close();
?>
