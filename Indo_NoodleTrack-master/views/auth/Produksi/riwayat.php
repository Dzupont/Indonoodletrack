<?php
session_start();

// Check if user is logged in and has produksi role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'produksi') {
    header('Location: ../../login.php');
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM returns WHERE returned_by = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$returns_history = array();
while ($row = $result->fetch_assoc()) {
    $returns_history[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Aktivitas Produksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Segoe UI', sans-serif;
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
        .content {
            margin-left: 270px;
            padding: 30px;
        }
        .dashboard-card {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .table thead {
            background-color: #4a9bb1;
            color: white;
        }
        .profile-info {
            position: absolute;
            top: 20px;
            right: 30px;
            display: flex;
            align-items: center;
        }
        .profile-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.9em;
        }
        .bg-proses { background-color: #d1ecf1; color: #0c5460; }
        .bg-ditolak { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="sidebar">
            <h4>indo noodle track.</h4>
            <a class="nav-link" href="dashboardproduksi.php">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a class="nav-link" href="permintaanmasuk.php">
                <i class="fas fa-inbox"></i>
                <span>Pengajuan Bahan Baku</span>
            </a>
            <a class="nav-link" href="returbahanbaku.php">
                <i class="fas fa-undo"></i>
                <span>Retur Bahan Baku</span>
            </a>
            <a class="nav-link" href="monitor.php">
                <i class="fas fa-eye"></i>
                <span>Monitoring</span>
            </a>
            <a class="nav-link active" href="riwayat.php">
                <i class="fas fa-history"></i>
                <span>Riwayat</span>
            </a>
            <a class="nav-link" href="../../../views/auth/logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
        <div class="content">
            <div class="profile-info">
                <img src="https://via.placeholder.com/40" alt="Profile">
                <div>
                    <strong>Divisi Produksi</strong><br>
                    <small>User id : <?php echo $_SESSION['user_id']; ?></small>
                </div>
            </div>
            <div class="dashboard-card">
                <h4 class="text-primary fw-bold">Riwayat Aktivitas Produksi</h4>
                <table class="table mt-4">
                    <thead>
                        <tr>
                            <th>Tanggal Aktivitas</th>
                            <th>Nama Aktivitas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($returns_history as $return): ?>
                            <tr>
                                <td><?php echo date('d F Y', strtotime($return['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($return['reason']); ?></td>
                                <td>
                                    <?php if ($return['status'] == 'Diproses'): ?>
                                        <span class="status-badge bg-proses">Diproses</span>
                                    <?php elseif ($return['status'] == 'Ditolak'): ?>
                                        <span class="status-badge bg-ditolak">Ditolak</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="#" class="text-secondary me-2"><i class="fas fa-copy"></i></a>
                                    <a href="#" class="text-secondary"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($returns_history)): ?>
                            <tr><td colspan="4" class="text-center">Tidak ada riwayat ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
