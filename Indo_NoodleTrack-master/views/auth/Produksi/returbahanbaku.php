<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retur Pengajuan</title>
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
        .content {
            margin-left: 270px;
            padding: 30px;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .status-label {
            background-color: #e2f0ff;
            border-radius: 10px;
            padding: 5px 15px;
            color: #2769a5;
        }
        .btn-process {
            background-color: #3c9cb1;
            border: none;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
        }
        .user-profile {
            text-align: right;
        }
        .pagination {
            justify-content: end;
        }
    </style>
</head>
<body>
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
        <a class="nav-link active" href="returbahanbaku.php">
            <i class="fas fa-undo"></i>
            <span>Retur Bahan Baku</span>
        </a>
        <a class="nav-link" href="monitor.php">
            <i class="fas fa-eye"></i>
            <span>Monitoring</span>
        </a>
        <a class="nav-link" href="riwayat.php">
            <i class="fas fa-history"></i>
            <span>Riwayat</span>
        </a>
        <a class="nav-link" href="../../../views/auth/logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>

    <div class="content">
        <div class="top-bar">
            <h3>Pengajuan Retur</h3>
            <div class="user-profile">
                <strong>Divisi Gudang</strong><br>
                <small>User ID: 0020190</small>
            </div>
        </div>

        <div class="mb-3">
            <input type="text" class="form-control" placeholder="Search" style="width: 250px;">
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Bahan</th>
                        <th>Nama Bahan</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>BB002</td>
                        <td>Minyak Goreng</td>
                        <td>500 liter</td>
                        <td><span class="status-label">Menunggu verifikasi dari gudang</span></td>
                        <td><button class="btn btn-process">Proses Retur</button></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <nav>
            <ul class="pagination">
                <li class="page-item disabled"><span class="page-link">Previous</span></li>
                <li class="page-item active"><span class="page-link">1</span></li>
                <li class="page-item disabled"><span class="page-link">Next</span></li>
            </ul>
        </nav>
    </div>
</body>
</html>
