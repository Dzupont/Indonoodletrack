<?php
session_start();
require_once '../../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $material_id = $_POST['material_id'];
    $quantity = $_POST['quantity'];
    $reason = $_POST['reason'];
    $returned_by = $_SESSION['user_id'];
    $approved_by = null; // Will be set when approved
    $created_at = date('Y-m-d H:i:s');

    // Insert return record
    $sql = "INSERT INTO returns (material_id, quantity, reason, returned_by, approved_by, created_at) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$material_id, $quantity, $reason, $returned_by, $approved_by, $created_at])) {
        $_SESSION['success'] = "Retur berhasil ditambahkan";
        header('Location: returmasuk.php');
        exit();
    } else {
        $_SESSION['error'] = "Gagal menambahkan retur";
    }
}

// Fetch all returns
$conn = getDBConnection();
$sql = "SELECT r.*, m.nama as material_name, u.username as returned_by_name 
        FROM returns r 
        LEFT JOIN raw_materials m ON r.material_id = m.id 
        LEFT JOIN users u ON r.returned_by = u.id 
        ORDER BY r.created_at DESC";
$result = $conn->query($sql);
$returns = array();
while ($row = $result->fetch_assoc()) {
    $returns[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retur Masuk - Gudang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Retur Masuk</h2>
            <div>
                <a href="index.php" class="btn btn-secondary me-2"><i class="bi bi-arrow-left"></i> Kembali</a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReturnModal">
                    <i class="bi bi-plus"></i> Tambah Retur
                </button>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- List of Returns -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Daftar Retur</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Material</th>
                                <th>Jumlah</th>
                                <th>Alasan</th>
                                <th>Dikembalikan Oleh</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($returns as $index => $retur): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($retur['material_name']); ?></td>
                                <td><?php echo htmlspecialchars($retur['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($retur['reason']); ?></td>
                                <td><?php echo htmlspecialchars($retur['returned_by_name']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($retur['created_at'])); ?></td>
                                <td>
                                    <?php if ($retur['approved_by']): ?>
                                        <span class="badge bg-success">Disetujui</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Menunggu Persetujuan</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewReturnModal" 
                                            data-id="<?php echo $retur['id']; ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Return Modal -->
    <div class="modal fade" id="addReturnModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Retur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="material_id" class="form-label">Material</label>
                            <select class="form-control" id="material_id" name="material_id" required>
                                <option value="">Pilih Material</option>
                                <?php
                                $sql = "SELECT id, nama FROM raw_materials ORDER BY nama";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['nama'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Alasan Retur</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Return Modal -->
    <div class="modal fade" id="viewReturnModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Retur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="returnDetails"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // View Return Details
        var viewModal = new bootstrap.Modal(document.getElementById('viewReturnModal'));
        var viewModalElement = document.getElementById('viewReturnModal');
        
        viewModalElement.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            
            fetch('get_return_details.php?id=' + id)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('returnDetails').innerHTML = html;
                });
        });
    </script>
</body>
</html>
