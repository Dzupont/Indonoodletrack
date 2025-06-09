<?php
session_start();
require_once __DIR__ . '/../config/database.php';

class StockAdjustmentController
{
    public function adjustStock($id, $type, $quantity)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gudang') {
            header('Location: ../login.php');
            exit();
        }

        $conn = getDBConnection();
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Get current stock
        $stmt = $conn->prepare("SELECT stok FROM stocks WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            header('Location: ../views/auth/Gudang/stok-bahan-baku.php?error=1');
            exit();
        }

        $current_stock = $row['stok'];
        $new_stock = 0;

        // Calculate new stock
        if ($type === 'add') {
            $new_stock = $current_stock + $quantity;
        } elseif ($type === 'subtract') {
            $new_stock = $current_stock - $quantity;
            if ($new_stock < 0) {
                header('Location: ../views/auth/Gudang/stok-bahan-baku.php?error=2');
                exit();
            }
        }

        // Update stock
        $stmt = $conn->prepare("UPDATE stocks SET stok = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("di", $new_stock, $id);

        if ($stmt->execute()) {
            // Log activity
            $activity_desc = sprintf(
                "%s stok bahan baku: %s (%s) sebanyak %.2f %s",
                $type === 'add' ? 'Menambah' : 'Mengurangi',
                $row['nama'],
                $row['kode'],
                $quantity,
                $row['satuan']
            );
            
            $log_query = "INSERT INTO activity_logs (user_id, activity_type, description) VALUES (?, 'stock_adjustment', ?)";
            $stmt_log = $conn->prepare($log_query);
            $stmt_log->bind_param("is", $_SESSION['user_id'], $activity_desc);
            $stmt_log->execute();
            
            header('Location: ../views/auth/Gudang/stok-bahan-baku.php?success=1');
            exit();
        } else {
            header('Location: ../views/auth/Gudang/stok-bahan-baku.php?error=1');
            exit();
        }
    }
}
