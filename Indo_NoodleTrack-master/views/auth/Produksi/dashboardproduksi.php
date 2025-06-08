<?php
session_start();

// Check if user is logged in and has produksi role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'produksi') {
    header('Location: ../../login.php');
    exit();
}

// Include database connection
require_once __DIR__ . '/../../../config/database.php';

// Get user data
$user_id = $_SESSION['user_id'];
$conn = getDBConnection();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Reset connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Bahan Baku - IndoNoodle Track</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        .product-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            background-color: white;
            text-align: center;
            transition: box-shadow 0.3s;
        }
        .product-card:hover {
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
        }
        .product-card img {
            max-width: 100%;
            height: auto;
        }
        .product-name {
            font-weight: bold;
            margin-top: 10px;
        }
        .overlay {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #2e94a6;
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            display: none;
        }
        .tabs {
            margin-bottom: 20px;
        }
        .tabs button {
            border: none;
            background-color: #e3f2f9;
            margin-right: 10px;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
        }
        .tabs button.active {
            background-color: #2e94a6;
            color: white;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4>indo noodle track.</h4>
        <a class="nav-link" href="dashboardproduksi.php"><i class="fas fa-home me-2"></i> Dashboard</a>
        <a class="nav-link" href="permintaanmasuk.php"><i class="fas fa-inbox me-2"></i> Permintaan Bahan Baku</a>
        <a class="nav-link" href="returbahanbaku.php"><i class="fas fa-undo me-2"></i> Retur Bahan Baku</a>
        <a class="nav-link" href="monitor.php"><i class="fas fa-eye me-2"></i> Monitoring</a>
        <a class="nav-link" href="riwayat.php"><i class="fas fa-history me-2"></i> Riwayat</a>
        <a class="nav-link" href="../../../views/auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </div>
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Permintaan Bahan Baku</h2>
            <div class="d-flex align-items-center">
                <div class="me-3 text-end">
                    <strong>Divisi Gudang</strong><br>
                    User Id : 02018999
                </div>
                <img src="https://via.placeholder.com/40" class="rounded-circle" alt="User Image">
            </div>
        </div>
        <div class="tabs">
            <button class="active">Bahan Baku Utama</button>
            <button>Bahan Tambahan</button>
            <button>Bumbu & Perisa</button>
            <button>Perlengkapan Kemasan</button>
            <button>Bahan Penolong Lain</button>
        </div>
        <div class="row">
            <?php for ($i = 0; $i < 8; $i++): ?>
                <div class="col-md-3 mb-4">
                    <div class="product-card">
                        <img src="https://cdn-icons-png.flaticon.com/512/2909/2909767.png" alt="Tepung">
                        <div class="product-name">Tepung Terigu Protein Tinggi</div>
                        <p>500 gr</p>
                        <p>Stok: 100</p>
                        <button class="btn btn-primary btn-sm">Tambah</button>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
    <div class="overlay" id="confirmationOverlay">
        <i class="fas fa-check-circle fa-2x mb-2"></i>
        <p>Produk Telah Ditambahkan Ke Keranjang</p>
    </div>
    <script>
        const buttons = document.querySelectorAll('.btn-primary');
        const overlay = document.getElementById('confirmationOverlay');

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                overlay.style.display = 'block';
                setTimeout(() => {
                    overlay.style.display = 'none';
                }, 1500);
            });
        });
    </script>
</body>
</html>