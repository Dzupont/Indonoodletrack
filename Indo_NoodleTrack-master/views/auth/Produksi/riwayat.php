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

// Get returns history
$sql = "SELECT * FROM returns WHERE returned_by = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$returns_history = [];
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 240px;
            height: 100vh;
            background-color: #4a9bb1;
            color: white;
            padding: 30px 20px;
        }
        .sidebar h4 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 40px;
        }
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            margin-bottom: 18px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .sidebar a:hover, .sidebar a.active {
            color: #e0f7fa;
            font-weight: bold;
        }
        .content {
            margin-left: 240px;
            padding: 40px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h2 {
            color: #3397b9;
            font-size: 24px;
        }
        .profile {
            display: flex;
            align-items: center;
        }
        .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-left: 15px;
        }
        .card {
            margin-top: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            padding: 14px 12px;
            text-align: left;
        }
        table thead {
            background-color: #4a9bb1;
            color: white;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-block;
        }
        .bg-proses {
            background-color: #e1f0f5;
            color: #117a8b;
        }
        .bg-ditolak {
            background-color: #fde2e2;
            color: #a33a3a;
        }
        .icon-button {
            color: #6c757d;
            text-decoration: none;
            margin-right: 10px;
        }
        .icon-button:hover {
            color: #343a40;
        }
        .text-end {
            text-align: end;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4>indo noodle track.</h4>
        <a href="dashboardproduksi.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="permintaanmasuk.php"><i class="fas fa-inbox"></i> Permintaan Masuk</a>
        <a href="returbahanbaku.php"><i class="fas fa-undo"></i> Retur Bahan Baku</a>
        <a href="monitor.php"><i class="fas fa-eye"></i> Monitoring</a>
        <a href="riwayat.php" class="active"><i class="fas fa-history"></i> Riwayat</a>
        <a href="../../../views/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="content">
        <div class="header">
            <h2>Riwayat Aktivitas Produksi</h2>
            <div class="profile">
                <div class="text-end">
                    <div><strong>Divisi Produksi</strong></div>
                    <div>User id : <?= htmlspecialchars($_SESSION['user_id']) ?></div>
                </div>
                <img src="https://via.placeholder.com/40" alt="User Profile">
            </div>
        </div>
        <div class="card">
            <h4 style="color: #3397b9; margin-bottom: 20px;">Riwayat Aktivitas Produksi</h4>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal Aktivitas</th>
                        <th>Nama Aktivitas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($returns_history)): ?>
                        <?php foreach ($returns_history as $return): ?>
                            <tr>
                                <td><?= date('d F Y', strtotime($return['created_at'])) ?></td>
                                <td><?= htmlspecialchars($return['reason']) ?></td>
                                <td>
                                    <?php if ($return['status'] == 'Diproses'): ?>
                                        <span class="status-badge bg-proses">Diproses</span>
                                    <?php elseif ($return['status'] == 'Ditolak'): ?>
                                        <span class="status-badge bg-ditolak">Ditolak</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="#" class="icon-button"><i class="fas fa-copy"></i></a>
                                    <a href="#" class="icon-button"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">Tidak ada riwayat ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
