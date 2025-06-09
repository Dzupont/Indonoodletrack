<?php
session_start();
require_once __DIR__ . '/../config/database.php';

class DeleteBahanBakuController
{
    public function delete($id)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gudang') {
            header('Location: ../login.php');
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
                $upload_dir = __DIR__ . '/../../public/images/bahan-baku/';
                $image_path = $upload_dir . basename($bahan_baku['gambar']);
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            // Log activity
            $activity_desc = "Menghapus bahan baku: {$bahan_baku['nama']} ({$bahan_baku['kode']})";
            $log_query = "INSERT INTO activity_logs (user_id, activity_type, description) VALUES (?, 'delete', ?)";
            $stmt_log = $conn->prepare($log_query);
            $stmt_log->bind_param("is", $_SESSION['user_id'], $activity_desc);
            
            if (!$stmt_log->execute()) {
                throw new Exception("Gagal mencatat aktivitas: " . $conn->error);
            }

            // Commit transaction
            $conn->commit();

            header('Location: ../views/auth/Gudang/stok-bahan-baku.php?success=1');
            exit();

        } catch (Exception $e) {
            // Rollback transaction if any error occurs
            if (isset($conn)) {
                $conn->rollback();
            }
            
            error_log("Error deleting bahan baku: " . $e->getMessage());
            header('Location: ../views/auth/Gudang/stok-bahan-baku.php?error=1');
            exit();
        }
    }
}
