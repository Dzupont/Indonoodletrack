<?php
// penerimaanpermintaanmasuk.php
session_start();
// Cek login pengguna (contoh sederhana)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Permintaan Masuk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .active {
            background-color: white;
            color: #4A9AB7;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-gray-100">
<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-[#4A9AB7] text-white flex flex-col py-10 px-6 rounded-tr-3xl rounded-br-3xl">
        <div class="flex flex-col items-center mb-12">
            <img src="assets/images/logo.jpg" alt="Logo" class="w-16 h-16 mb-2 rounded-full">
            <span class="text-xl font-bold">indo<br>noodle<br>track.</span>
        </div>
        <nav class="flex flex-col gap-4 text-sm font-semibold">
            <a href="dashboardgudang.php" class="flex items-center gap-2"><i class="fas fa-home"></i> Dashboard</a>
            <a href="penerimaanpermintaanmasuk.php" class="flex items-center gap-2 active"><i class="fas fa-file-invoice"></i> Permintaan Masuk</a>
            <a href="returmasuk.php" class="flex items-center gap-2"><i class="fas fa-sync-alt"></i> Retur Masuk</a>
            <a href="monitoringgudang.php" class="flex items-center gap-2"><i class="fas fa-cube"></i> Monitoring</a>
            <a href="stok-bahan-baku.php" class="flex items-center gap-2"><i class="fas fa-box"></i> Stok</a>
            <a href="../../auth/login.php" class="flex items-center gap-2 mt-4"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-10">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-[#4A9AB7]">Permintaan Masuk</h1>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <h4 class="font-semibold">Divisi Gudang</h4>
                    <span class="text-xs text-gray-500">User ID : 02081999</span>
                </div>
                <img src="assets/images/user.jpg" alt="User" class="w-10 h-10 rounded-full">
            </div>
        </div>

        <!-- Search -->
        <div class="mb-4">
            <input type="text" placeholder="Search" class="px-4 py-2 border rounded-md w-64">
        </div>

        <!-- Table -->
        <div class="overflow-auto rounded-lg shadow bg-white">
            <table class="min-w-full text-sm text-left text-gray-700">
                <thead class="bg-[#4A9AB7] text-white">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Kode Bahan</th>
                        <th class="px-4 py-3">Nama Bahan</th>
                        <th class="px-4 py-3">Stok</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="px-4 py-3">1</td>
                        <td class="px-4 py-3">BB001</td>
                        <td class="px-4 py-3">Tepung Terigu</td>
                        <td class="px-4 py-3">1.000 KG</td>
                        <td class="px-4 py-3 text-yellow-600">Menunggu persetujuan</td>
                        <td class="px-4 py-3 flex gap-2">
                            <button class="bg-green-500 text-white px-3 py-1 rounded">Terima</button>
                            <button class="bg-red-500 text-white px-3 py-1 rounded">Tolak</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3">2</td>
                        <td class="px-4 py-3">BB003</td>
                        <td class="px-4 py-3">Telur Ayam</td>
                        <td class="px-4 py-3">300 butir</td>
                        <td class="px-4 py-3 text-yellow-600">Menunggu persetujuan</td>
                        <td class="px-4 py-3 flex gap-2">
                            <button class="bg-green-500 text-white px-3 py-1 rounded">Terima</button>
                            <button class="bg-red-500 text-white px-3 py-1 rounded">Tolak</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex justify-center gap-2">
            <button class="px-3 py-1 bg-gray-200 rounded" disabled>Previous</button>
            <button class="px-3 py-1 bg-[#4A9AB7] text-white rounded">1</button>
            <button class="px-3 py-1 bg-gray-200 rounded">2</button>
            <button class="px-3 py-1 bg-gray-200 rounded">Next</button>
        </div>
    </main>
</div>
</body>
</html>
