<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gudang') {
    header('Location: ../../login.php');
    exit();
}

require_once __DIR__ . '/../config/database.php';
$conn = getDBConnection();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get POST data
$id = $_POST['id'] ?? null;
$nama = $_POST['nama'] ?? '';
$jenis = $_POST['jenis'] ?? '';
$stok = $_POST['stok'] ?? '';
$satuan = $_POST['satuan'] ?? '';
$minimal_stok = $_POST['minimal_stok'] ?? '';
$tanggal_expired = $_POST['tanggal_expired'] ?? null;
$deskripsi = $_POST['deskripsi'] ?? '';
$gambar = $_FILES['gambar'] ?? null;

// Validate inputs
$errors = [];
if (empty($nama)) {
    $errors[] = "Nama bahan baku tidak boleh kosong";
}
if (empty($jenis)) {
    $errors[] = "Jenis bahan baku tidak boleh kosong";
}
if (empty($stok)) {
    $errors[] = "Stok tidak boleh kosong";
}
if (empty($satuan)) {
    $errors[] = "Satuan tidak boleh kosong";
}
if (empty($minimal_stok)) {
    $errors[] = "Minimal stok tidak boleh kosong";
}

if (empty($errors)) {
    try {
        // Start transaction
        $conn->begin_transaction();

        // Get current data
        $stmt = $conn->prepare("SELECT gambar FROM stocks WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_data = $result->fetch_assoc();

        $gambar_path = $current_data['gambar'];
        
        // Handle image upload
        if ($gambar && $gambar['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../public/images/bahan-baku/';
            $file_extension = strtolower(pathinfo($gambar['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($file_extension, $allowed_extensions)) {
                $errors[] = "Format file tidak diizinkan. Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.";
            } else {
                $new_filename = uniqid() . '.' . $file_extension;
                $target_path = $upload_dir . $new_filename;

                if (move_uploaded_file($gambar['tmp_name'], $target_path)) {
                    // Delete old image if exists
                    if ($current_data['gambar'] && file_exists($upload_dir . $current_data['gambar'])) {
                        unlink($upload_dir . $current_data['gambar']);
                    }
                    $gambar_path = $new_filename;
                } else {
                    $errors[] = "Gagal mengunggah gambar";
                }
            }
        }

        if (empty($errors)) {
            // Update bahan baku
            $stmt = $conn->prepare("
                UPDATE stocks 
                SET 
                    nama = ?, 
                    jenis = ?, 
                    stok = ?, 
                    satuan = ?, 
                    minimal_stok = ?, 
                    tanggal_expired = ?, 
                    deskripsi = ?, 
                    gambar = ?
                WHERE id = ?
            ");

            $stmt->bind_param("ssdsssssi", 
                $nama, 
                $jenis, 
                $stok, 
                $satuan, 
                $minimal_stok, 
                $tanggal_expired, 
                $deskripsi, 
                $gambar_path, 
                $id
            );

            if ($stmt->execute()) {
                // Log activity
                $activity_desc = "Mengubah bahan baku: {$nama}";
                $log_query = "INSERT INTO activity_logs (user_id, activity_type, description) VALUES (?, 'update', ?)";
                $stmt_log = $conn->prepare($log_query);
                $stmt_log->bind_param("is", $_SESSION['user_id'], $activity_desc);
                $stmt_log->execute();

                // Commit transaction
                $conn->commit();

                header('Location: ../views/auth/Gudang/stok-bahan-baku.php?success=1');
                exit();
            } else {
                $errors[] = "Gagal mengupdate bahan baku: " . $conn->error;
            }
        }

        // Rollback transaction if any error occurs
        if (isset($conn)) {
            $conn->rollback();
        }

    } catch (Exception $e) {
        error_log("Error updating bahan baku: " . $e->getMessage());
        $errors[] = "Terjadi kesalahan saat mengupdate data";
    }
}

// Redirect with errors
header('Location: ../views/auth/Gudang/edit-bahan-baku.php?id=' . $id . '&error=1');
exit();
