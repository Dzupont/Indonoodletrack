<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5; /* Warna latar belakang */
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #0095c8; /* Warna sidebar */
            color: white;
            padding: 20px;
            position: fixed;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            margin: 0 0 20px 0;
            font-size: 24px;
            text-align: center; /* Memusatkan logo */
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #007bb5; /* Warna saat hover */
        }

        .content {
            margin-left: 250px; /* Menggeser konten ke kanan */
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: white;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            margin: 0;
            font-size: 28px; /* Ukuran font judul */
        }

        .dashboard-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px 0;
        }

        .summary-item {
            text-align: center;
            flex: 1;
        }

        .summary-item h2 {
            margin: 0;
            font-size: 36px;
        }

        .summary-item p {
            margin: 5px 0;
            font-weight: bold;
        }

        .activity-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .activity-table th, .activity-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .activity-table th {
            background-color: #0095c8;
            color: white;
        }

        .activity-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .activity-table tr:hover {
            background-color: #f1f1f1;
        }

        .profile {
            display: flex;
            align-items: center;
        }

        .profile img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Indo Noodle Track</h2>
        <a href="#">Dashboard</a>
        <a href="#">Laporan</a>
        <a href="#">Bahan Baku</a>
        <a href="#">Stok</a>
        <a href="#">Keluar</a>
    </div>

    <div class="content">
        <div class="header">
            <h1>Dashboard Manager</h1>
            <div class="profile">
                <a href="../../../views/auth/logout.php" class="logout-btn">Logout</a>
                <img src="profile.jpg" alt="Profile Picture"> <!-- Ganti dengan foto profil yang sesuai -->
                <span>Manager</span>
            </div>
        </div>

        <div class="dashboard-summary">
            <div class="summary-item">
                <h2>24</h2>
                <p>Total Aktivitas</p>
            </div>
            <div class="summary-item">
                <h2>20</h2>
                <p>Permintaan Bahan Baku</p>
            </div>
            <div class="summary-item">
                <h2>4</h2>
                <p>Return</p>
            </div>
        </div>

        <h2>Aktivitas Berhasil</h2>
        <table class="activity-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Tanggal</th>
                    <th>Aktivitas</th>
                    <th>Total Barang</th>
                    <th>Return</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>001</td>
                    <td>01/04/2025</td>
                    <td>Pemesanan</td>
                    <td>100</td>
                    <td>No</td>
                </tr>
                <tr>
                    <td>002</td>
                    <td>02/04/2025</td>
                    <td>Pengiriman</td>
                    <td>50</td>
                    <td>Yes</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
