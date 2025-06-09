<?php
session_start();
require_once '../../../config/database.php';

// Check if user is logged in and has produksi role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'produksi') {
    header('Location: ../../login.php');
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$conn = getDBConnection();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get status message if any
$status = isset($_GET['status']) ? $_GET['status'] : '';
$message = '';
if ($status === 'success') {
    $message = '<div class="alert alert-success">Berhasil memperbarui status permintaan!</div>';
} elseif ($status === 'error') {
    $message = '<div class="alert alert-danger">Terjadi kesalahan saat memperbarui status permintaan!</div>';
}

// Get user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get incoming requests
$sql = "SELECT r.*, m.name as nama_bahan, m.unit as unit, u.username as requested_by 
        FROM requests r 
        LEFT JOIN raw_materials m ON r.material_id = m.id 
        LEFT JOIN users u ON r.requested_by = u.id 
        WHERE r.status != 'rejected' 
        ORDER BY r.created_at DESC";
$result = $conn->query($sql);
$permintaan = array();
while ($row = $result->fetch_assoc()) {
    $permintaan[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Bahan Baku - Produksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f8fb;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #4a9bb1;
            color: white;
            padding: 20px;
        }
        .sidebar h4 {
            font-weight: bold;
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }
        .sidebar .nav-link {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 5px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .main-content {
            margin-left: 270px;
            padding: 30px;
        }
        .profile {
            text-align: right;
        }
        .badge-warning {
            background-color: #ffc107;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-danger {
            background-color: #dc3545;
        }
        .table thead {
            background-color: #f8f9fa;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(74, 155, 177, 0.1);
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="sidebar">
            <div>
                <h4>indo noodle track.</h4>
                <a href="dashboardproduksi.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
                <a href="permintaanmasuk.php" class="nav-link active"><i class="fas fa-inbox"></i> Permintaan Bahan Baku</a>
                <a href="returbahanbaku.php" class="nav-link"><i class="fas fa-undo"></i> Retur Bahan Baku</a>
                <a href="monitor.php" class="nav-link"><i class="fas fa-eye"></i> Monitoring</a>
                <a href="riwayat.php" class="nav-link"><i class="fas fa-history"></i> Riwayat</a>
            </div>
            <a href="../../../views/auth/logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="main-content w-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <a href="keranjang.php" class="me-3 text-[#4a9bb1] hover:text-[#2e94a6]">
                        <i class="fas fa-shopping-cart text-2xl"></i>
                    </a>
                    <div class="me-3 text-end">
                        <strong>Divisi Produksi</strong><br>
                        User Id : <?php echo htmlspecialchars($_SESSION['user_id']); ?>
                    </div>
                    <img src="https://via.placeholder.com/40" class="rounded-circle" alt="User Image">
                </div>
                <h2>Permintaan Bahan Baku</h2>
            </div>

            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                Halaman ini menampilkan daftar permintaan bahan baku yang masuk untuk produksi.
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Permintaan</th>
                            <th>Tanggal</th>
                            <th>Bahan Baku</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Direquest Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($permintaan as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['id']); ?></td>
                            <td><?= date('d F Y H:i', strtotime($p['created_at'])); ?></td>
                            <td><?= htmlspecialchars($p['nama_bahan']); ?></td>
                            <td><?= htmlspecialchars($p['quantity']) . ' ' . htmlspecialchars($p['unit']); ?></td>
                            <td>
                                <?php
                                $status_class = '';
                                switch ($p['status']) {
                                    case 'pending':
                                        $status_class = 'badge-warning';
                                        break;
                                    case 'approved':
                                        $status_class = 'badge-success';
                                        break;
                                    case 'rejected':
                                        $status_class = 'badge-danger';
                                        break;
                                }
                                ?>
                                <span class="badge <?= $status_class; ?>"><?= ucfirst($p['status']); ?></span>
                            </td>
                            <td><?= htmlspecialchars($p['requested_by']); ?></td>
                            <td>
                                <?php if ($p['status'] === 'pending'): ?>
                                    <button class="btn btn-sm btn-success" onclick="approveRequest(<?= $p['id']; ?>)">
                                        <i class="fas fa-check"></i> Setujui
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="rejectRequest(<?= $p['id']; ?>)">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                <?php endif; ?>
                                <a href="monitoring.php?id=<?= $p['id']; ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Monitoring
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Reject -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Alasan Penolakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rejectForm">
                        <input type="hidden" id="requestId" name="request_id">
                        <div class="mb-3">
                            <label for="reason" class="form-label">Alasan Penolakan</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="submitReject()">Tolak</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function approveRequest(id) {
            if (confirm('Apakah Anda yakin ingin menyetujui permintaan ini?')) {
                window.location.href = 'permintaanmasuk.php?action=approve&id=' + id;
            }
        }

        function rejectRequest(id) {
            $('#requestId').val(id);
            $('#rejectModal').modal('show');
        }

        function submitReject() {
            const id = $('#requestId').val();
            const reason = $('#reason').val();
            
            if (reason.trim() === '') {
                alert('Silakan isi alasan penolakan');
                return;
            }

            $.ajax({
                url: 'permintaanmasuk.php?action=reject&id=' + id,
                type: 'POST',
                data: { rejection_reason: reason },
                success: function(response) {
                    window.location.href = 'permintaanmasuk.php';
                }
            });
        }
    </script>
</body>
</html>
