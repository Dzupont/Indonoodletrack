<?php
// Disable session handling for AJAX requests
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    session_start();
    require_once '../../../config/database.php';
    
    // Check login
    if (!isset($_SESSION['user_id'])) {
        echo "<div class='text-red-600'>Anda harus login terlebih dahulu</div>";
        exit();
    }
    
    // Check role
    if ($_SESSION['role'] !== 'produksi') {
        echo "<div class='text-red-600'>Anda tidak memiliki akses ke halaman ini</div>";
        exit();
    }
}

// Get return ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Get return details
    $sql = "SELECT r.*, m.name as material_name, u.name as returned_by_name
            FROM returns r
            LEFT JOIN raw_materials m ON r.material_id = m.id
            LEFT JOIN users u ON r.returned_by = u.id
            WHERE r.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo "<div class='space-y-4'>";
        echo "<div class='text-sm'>";
        echo "<p><strong>Material:</strong> " . htmlspecialchars($row['material_name']) . "</p>";
        echo "<p><strong>Jumlah:</strong> " . htmlspecialchars($row['quantity']) . "</p>";
        echo "<p><strong>Alasan:</strong> " . htmlspecialchars($row['reason']) . "</p>";
        echo "<p><strong>Dikembalikan Oleh:</strong> " . htmlspecialchars($row['returned_by_name']) . "</p>";
        echo "<p><strong>Tanggal:</strong> " . date('d/m/Y H:i', strtotime($row['created_at'])) . "</p>";
        
        if ($row['approved_by']) {
            echo "<p><strong>Status:</strong> <span class='text-green-600'>Disetujui</span></p>";
        } else {
            echo "<p><strong>Status:</strong> <span class='text-yellow-600'>Menunggu</span></p>";
        }
        echo "</div>";
        echo "</div>";
    } else {
        echo "<div class='text-red-600'>Retur tidak ditemukan</div>";
    }
} else {
    echo "<div class='text-red-600'>ID retur tidak valid</div>";
}
?>
