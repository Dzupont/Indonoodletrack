<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/session.php';
require_once __DIR__ . '/../../../config/base_url.php';
require_once __DIR__ . '/../../../config/logging.php';

// Log session status
logSessionStatus();

// Check session and role
if (!requireLogin(false) || getCurrentUserRole() !== 'produksi') {
    header('Location: ' . getBaseUrl() . 'views/auth/login.php');
    exit();
}

// Get database connection
$conn = getDBConnection();

// Log database connection status
logDatabaseStatus($conn);

// Get stocks for dropdown
$stocks_sql = "SELECT id, nama, satuan FROM stocks ORDER BY nama";
$stocks_result = $conn->query($stocks_sql);
$stocks = [];
while ($row = $stocks_result->fetch_assoc()) {
    $stocks[] = $row;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Log session data
    error_log("Session data:");
    error_log("user_id: " . $_SESSION['user_id']);
    error_log("role: " . $_SESSION['role']);

    // Get form data
    $stock_id = $_POST['stock_id'];
    $quantity = $_POST['quantity'];
    $reason = $_POST['reason'];
    $returned_by = $_SESSION['user_id'];
    $approved_by = null;
    $created_at = date('Y-m-d H:i:s');

    // Log form data
    logFormData($stock_id, $quantity, $reason);

    // Check autocommit status
    $autocommit_status = $conn->autocommit(true);
    logAutocommitStatus($conn);

    // Start transaction
    $conn->begin_transaction();
    logTransactionStart();

    // Insert return record
    $sql = "INSERT INTO returns (stock_id, quantity, reason, returned_by, approved_by, created_at) 
            VALUES (?, ?, ?, ?, ?, ?)";
    error_log("Executing SQL: " . $sql);

    // Prepare statement with explicit parameter types
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        logStatementPrepared();
        
        // Bind parameters
        // i: integer (stock_id, returned_by, approved_by)
        // d: decimal (quantity)
        // s: string (reason)
        // s: string (created_at - will be converted to timestamp)
        $stmt->bind_param("idssis", $stock_id, $quantity, $reason, $returned_by, $approved_by, $created_at);
        if ($stmt->execute()) {
            logStatementExecuted();
            
            // Get the last insert ID
            $last_id = $conn->insert_id;
            logInsertID($last_id);

            // Debug: Check if data was inserted
            $check_sql = "SELECT * FROM returns WHERE id = ?";
            $check_stmt = $conn->prepare($check_sql);
            if ($check_stmt) {
                $check_stmt->bind_param("i", $last_id);
                $check_stmt->execute();
                $result = $check_stmt->get_result();
                if ($result) {
                    $row = $result->fetch_assoc();
                    if ($row) {
                        logDataVerification($row);
                    } else {
                        error_log("Failed to verify inserted data");
                    }
                } else {
                    error_log("Error in check query: " . $check_stmt->error);
                }
            } else {
                error_log("Error preparing check statement: " . $conn->error);
            }

            // Update stock in stocks table
            $update_stock_sql = "UPDATE stocks SET stok = stok + ? WHERE id = ?";
            error_log("Executing stock update: " . $update_stock_sql);

            $stock_stmt = $conn->prepare($update_stock_sql);
            if ($stock_stmt) {
                error_log("Stock update statement prepared successfully");
                
                if ($stock_stmt->bind_param("di", $quantity, $stock_id)) {
                    logParametersBound();
                    
                    if ($stock_stmt->execute()) {
                        error_log("Stock update executed successfully");
                        
                        // Verify stock update
                        $check_stock_sql = "SELECT stok FROM stocks WHERE id = ?";
                        $check_stock_stmt = $conn->prepare($check_stock_sql);
                        $check_stock_stmt->bind_param("i", $stock_id);
                        $check_stock_stmt->execute();
                        $result = $check_stock_stmt->get_result();
                        
                        if ($result && $result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            error_log("Stock verified successfully:");
                            error_log("Stock ID: " . $stock_id);
                            error_log("Current Stock: " . $row['stok']);
                        } else {
                            error_log("Failed to verify stock update");
                        }
                        
                        $conn->commit();
                        error_log("Stock update transaction committed");
                        
                        $_SESSION['success'] = "Retur berhasil ditambahkan dan stok diperbarui";
                        header('Location: ' . getBaseUrl() . 'views/auth/Produksi/returbahanbaku.php');
                        exit();
                    } else {
                        logErrorAndRollback($stock_stmt->error);
                    }
                } else {
                    logErrorAndRollback($stock_stmt->error);
                }
            } else {
                logPrepareErrorAndRollback($conn->error);
            }
        } else {
            logErrorAndRollback($stmt->error);
        }
    } else {
        logPrepareErrorAndRollback($conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Retur Bahan Baku - Produksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
            margin: 0;
        }
        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #5d99ae;
            padding: 30px 20px;
            color: white;
        }
        .sidebar h4 {
            font-weight: bold;
            margin-bottom: 40px;
            font-size: 20px;
        }
        .sidebar .nav-link {
            color: white;
            font-size: 14px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            padding: 8px 10px;
        }
        .main-content {
            margin-left: 260px;
            padding: 40px;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            max-width: 600px;
        }
        .form-container label {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .form-container select,
        .form-container input,
        .form-container textarea {
            font-size: 14px;
            border-radius: 8px;
        }
        .form-container .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 20px;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .btn-primary {
            background-color: #5d99ae;
            border: none;
        }
        .btn-primary:hover {
            background-color: #4a7c91;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
        }
    </style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stockSelect = document.getElementById('stock_id');
    const namaInput = document.getElementById('nama');
    
    stockSelect.addEventListener('change', function() {
        const selectedOption = stockSelect.options[stockSelect.selectedIndex];
        const nama = selectedOption.text.split(' (')[0];
        namaInput.value = nama;
    });
});
</script>
</head>
<body>
    <div class="sidebar">
        <h4>indo noodle track.</h4>
        <a class="nav-link" href="<?php echo getBaseUrl(); ?>views/auth/Produksi/dashboardproduksi.php"><i class="fas fa-home"></i> Dashboard</a>
        <a class="nav-link" href="<?php echo getBaseUrl(); ?>views/auth/Produksi/permintaanmasuk.php"><i class="fas fa-shopping-cart"></i> Permintaan Bahan Baku</a>
        <a class="nav-link active" href="<?php echo getBaseUrl(); ?>views/auth/Produksi/returbahanbaku.php"><i class="fas fa-undo"></i> Retur Bahan Baku</a>
        <a class="nav-link" href="<?php echo getBaseUrl(); ?>views/auth/Produksi/monitor.php"><i class="fas fa-chart-line"></i> Monitoring</a>
        <a class="nav-link" href="<?php echo getBaseUrl(); ?>views/auth/Produksi/riwayat.php"><i class="fas fa-history"></i> Riwayat</a>
        <a class="nav-link" href="<?php echo getBaseUrl(); ?>views/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="main-content">
        <h3 class="mb-4 fw-bold text-secondary">Tambah Retur Bahan Baku</h3>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST">
                <div class="mb-3">
                    <label for="stock_id">Bahan Baku</label>
                    <select name="stock_id" id="stock_id" class="form-select" required>
                        <option value="">Pilih Bahan Baku</option>
                        <?php foreach ($stocks as $stock): ?>
                            <option value="<?php echo $stock['id']; ?>"><?php echo $stock['nama'] . " ({$stock['satuan']})"; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="nama">Nama Bahan Baku</label>
                    <input type="text" name="nama" id="nama" class="form-control" required readonly>
                </div>

                <div class="mb-3">
                    <label for="quantity">Jumlah</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label for="reason">Alasan Retur</label>
                    <textarea name="reason" id="reason" class="form-control" rows="3" required></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?php echo getBaseUrl(); ?>views/auth/Produksi/returbahanbaku.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>