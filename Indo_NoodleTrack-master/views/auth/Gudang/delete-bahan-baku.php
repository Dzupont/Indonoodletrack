<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gudang') {
    header('Location: ../../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./stok-bahan-baku.php?error=1');
    exit();
}

$id = $_POST['id'] ?? null;
if (!$id) {
    header('Location: ./stok-bahan-baku.php?error=1');
    exit();
}

try {
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }

    // Start transaction
    $conn->begin_transaction();

    // Get bahan baku data
    $stmt = $conn->prepare("SELECT kode, nama, gambar FROM stocks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bahan_baku = $result->fetch_assoc();

    if (!$bahan_baku) {
        throw new Exception("Bahan baku tidak ditemukan");
    }

    // Delete from stocks table
    $stmt = $conn->prepare("DELETE FROM stocks WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Gagal menghapus bahan baku: " . $conn->error);
    }

    // Delete image if exists
    if ($bahan_baku['gambar']) {
        $upload_dir = __DIR__ . '/../../../public/images/bahan-baku/';
        $image_path = $upload_dir . basename($bahan_baku['gambar']);
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // Log activity
    $user_id = $_SESSION['user_id'];
    $activity = "Menghapus bahan baku: {$bahan_baku['nama']} ({$bahan_baku['kode']})";
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, activity, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $user_id, $activity);
    
    if (!$stmt->execute()) {
        throw new Exception("Gagal mencatat aktivitas: " . $conn->error);
    }

    // Commit transaction
    $conn->commit();
    
    header('Location: ./stok-bahan-baku.php?success=1');
    exit();

} catch (Exception $e) {
    // Rollback transaction if error occurs
    if (isset($conn)) {
        $conn->rollback();
    }
    
    $errorDetails = [
        'Error' => $e->getMessage(),
        'File' => $e->getFile(),
        'Line' => $e->getLine(),
        'Trace' => $e->getTraceAsString(),
        'Database Connection' => isset($conn) ? 'Connected' : 'Not Connected',
        'Bahan Baku ID' => $id
    ];
    error_log("Error deleting bahan baku: " . json_encode($errorDetails));
    header('Location: ./stok-bahan-baku.php?error=1');
    exit();
}
