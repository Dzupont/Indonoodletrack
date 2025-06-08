<?php
session_start();

// Check if user is logged in and has gudang role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gudang') {
    header('Location: ../../login.php');
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$user_id = $_SESSION['user_id'];
$conn = getDBConnection();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch username
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Stok Bahan Baku</title>
    <script src="https://cdn.tailwindcss.com">
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: "Poppins", sans-serif;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f6fcfd;
        }
        .sidebar {
            width: 220px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            background-color: #3e9cb4;
            padding: 30px 20px;
            color: white;
        }
        .sidebar h4 {
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 30px;
        }
        .sidebar .nav-link {
            color: white;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding-left: 10px;
        }
        .content {
            margin-left: 240px;
            padding: 40px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        .tabs button {
            background: #e4f3f9;
            border: none;
            padding: 8px 16px;
            margin-right: 10px;
            border-radius: 8px;
            color: #333;
            font-weight: 500;
        }
        .tabs button.active {
            background-color: #3e9cb4;
            color: white;
        }
        .product-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
            transition: 0.2s ease;
        }
        .product-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .product-card img {
            height: 90px;
            margin-bottom: 10px;
        }
        .product-name {
            font-weight: bold;
            color: #1b6f7a;
        }
    </style>
</head>
<body class="bg-white">
    <div class="flex min-h-screen max-h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#4A9AB7] text-white flex flex-col py-10 px-6 rounded-tr-3xl rounded-br-3xl">
    <div class="flex flex-col items-center mb-12">
        <img src="/Indo_NoodleTrack-master/assets/images/logo.jpg" alt="Logo" class="w-16 h-16 mb-2 rounded-full">
        <span class="text-xl font-bold">indo<br>noodle<br>track.</span>
    </div>
    <nav class="flex flex-col gap-4 text-sm font-semibold">
        <a href="./dashboardgudang.php" class="flex items-center gap-2">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="./penerimaanpermintaanmasuk.php" class="flex items-center gap-2">
            <i class="fas fa-file-invoice"></i> Permintaan Masuk
        </a>
        <a href="./returmasuk.php" class="flex items-center gap-2">
            <i class="fas fa-sync-alt"></i> Retur Masuk
        </a>
        <a href="./monitoringgudang.php" class="flex items-center gap-2">
            <i class="fas fa-cube"></i> Monitoring
        </a>
        <a href="./stok-bahan-baku.php" class="flex items-center gap-2 active">
            <i class="fas fa-box"></i> Stok
        </a>
        <a href="../../auth/login.php" class="flex items-center gap-2 mt-4">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</aside>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col bg-white rounded-tl-3xl rounded-bl-3xl overflow-hidden">
            <div class="flex-1 overflow-y-auto p-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Bahan Baku Utama</h2>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                            <i class="fas fa-plus mr-2"></i>Tambah Bahan Baku
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <?php for ($i = 0; $i < 9; $i++): ?>
                            <div class="bg-white rounded-lg shadow-md p-4">
                                <img src="https://cdn-icons-png.flaticon.com/512/2909/2909767.png" alt="Tepung" class="w-24 h-24 mx-auto mb-4">
                                <h3 class="text-lg font-semibold text-center mb-2">Tepung Terigu</h3>
                                <p class="text-gray-600 text-center text-sm">Stok: 1000 kg</p>
                                <div class="mt-4">
                                    <button class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </button>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
