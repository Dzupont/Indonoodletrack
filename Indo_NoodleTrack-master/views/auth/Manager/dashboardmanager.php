<?php
// dashboardmanager.php
session_start();
require_once '../../../config/session.php';
require_once '../../../config/database.php';
require_once '../../../config/base_url.php';

// Cek login dan role
if (!requireLogin(false) || getCurrentUserRole() !== 'manager') {
    header("Location: " . getBaseUrl() . "views/auth/login.php");
    exit();
}

// Get database connection
$conn = getDBConnection();

// Fetch real-time statistics
$total_returns = $conn->query("SELECT COUNT(*) as total FROM returns WHERE status IN ('pending', 'approved')")->fetch_assoc()['total'];
$total_requests = $conn->query("SELECT COUNT(*) as total FROM requests WHERE status IN ('pending', 'approved')")->fetch_assoc()['total'];

// Fetch recent activities from both requests and returns
$recent_activities = $conn->query("
    SELECT 
        'request' as activity_type,
        r.id,
        CONCAT('Permintaan bahan baku ', m.name, ' sebanyak ', r.quantity, ' ', m.unit) as description,
        r.created_at,
        u.username as user_name
    FROM requests r
    LEFT JOIN raw_materials m ON m.id = r.material_id
    LEFT JOIN users u ON u.id = r.requested_by
    WHERE r.status IN ('pending', 'approved')
    UNION ALL
    SELECT 
        'return' as activity_type,
        r.id,
        CONCAT('Retur bahan baku ', s.nama, ' sebanyak ', r.quantity, ' ', s.satuan) as description,
        r.created_at,
        u.username as user_name
    FROM returns r
    LEFT JOIN stocks s ON s.id = r.stock_id
    LEFT JOIN users u ON u.id = r.returned_by
    WHERE r.status IN ('pending', 'approved')
    ORDER BY created_at DESC
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);


?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: "Poppins", sans-serif; background-color: #F6F6F6; }
        .sidebar { background-color: #3C9BA2; }
        .active-link { background-color: white; color: #3C9BA2; border-radius: 12px; }
    </style>
</head>
<body class="text-[#333]">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 sidebar text-white p-6 rounded-tr-[30px] rounded-br-[30px]">
        <div class="text-center mb-12">
            <div class="text-2xl font-bold leading-6">ind0<br>noodle<br>track.</div>
        </div>
        <nav class="flex flex-col gap-4">
            <a href="dashboardmanager.php" class="active-link px-4 py-2 font-semibold text-center">Dashboard</a>

            <!-- Tombol Laporan -->
            <a href="fiturlaporan.php" class="w-full bg-white text-[#3C9BA2] px-4 py-2 text-center font-semibold rounded-lg hover:bg-[#d8f2f2] transition duration-300">
                Laporan
            </a>

            <!-- Filter -->
            <div class="px-2 mt-6">
                <input type="text" placeholder="Bulan..." class="w-full mb-2 px-3 py-2 rounded-md text-black text-sm">
                <input type="text" placeholder="Tahun..." class="w-full mb-2 px-3 py-2 rounded-md text-black text-sm">
                <button class="w-full bg-white text-[#3C9BA2] font-semibold px-3 py-2 rounded-md hover:bg-[#d8f2f2] transition">Cari</button>
            </div>

            <!-- Logout -->
            <a href="../../auth/login.php" class="mt-10 w-full bg-white text-[#3C9BA2] px-4 py-2 text-center font-semibold rounded-lg hover:bg-[#d8f2f2] transition duration-300">
                Keluar
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 px-10 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold tracking-wide">DASHBOARD</h1>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="font-semibold">Manager</p>
                    <p class="text-xs text-gray-500">User Id : 02081999</p>
                </div>
                <img src="assets/images/user.jpg" alt="User" class="w-10 h-10 rounded-full">
            </div>
        </div>

        <!-- Filter Bulan -->
        <div class="flex items-center gap-3 mb-6">
            <div class="bg-[#AEDFE3] text-[#3C9BA2] px-4 py-1 rounded-full font-semibold">APRIL</div>
            <div class="bg-[#AEDFE3] text-[#3C9BA2] px-4 py-1 rounded-full font-semibold">2025</div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-3 gap-6 mb-8">
            <div class="bg-white border border-[#3C9BA2] p-6 rounded-xl text-center shadow-md hover:shadow-lg transition-shadow duration-300 cursor-pointer">
                <p class="text-sm font-medium text-[#3C9BA2] mb-1">Total Retur</p>
                <p class="text-5xl font-bold text-[#111]"><?php echo $total_returns; ?></p>
                <div class="mt-2">
                    <span class="text-sm text-red-500">+2%</span>
                    <span class="text-xs text-gray-500">from last month</span>
                </div>
            </div>
            <div class="bg-white border border-[#3C9BA2] p-6 rounded-xl text-center shadow-md hover:shadow-lg transition-shadow duration-300 cursor-pointer">
                <p class="text-sm font-medium text-[#3C9BA2] mb-1">Permintaan Bahan Baku</p>
                <p class="text-5xl font-bold text-[#111]"><?php echo $total_requests; ?></p>
                <div class="mt-2">
                    <span class="text-sm text-green-500">+15%</span>
                    <span class="text-xs text-gray-500">from last month</span>
                </div>
            </div>
            <div class="bg-white border border-[#3C9BA2] p-6 rounded-xl text-center shadow-md hover:shadow-lg transition-shadow duration-300 cursor-pointer">
                <p class="text-sm font-medium text-[#3C9BA2] mb-1">Total Aktivitas</p>
                <p class="text-5xl font-bold text-[#111]"><?php echo $total_returns + $total_requests; ?></p>
                <div class="mt-2">
                    <span class="text-sm text-yellow-500">+5%</span>
                    <span class="text-xs text-gray-500">from last month</span>
                </div>
            </div>
            <div class="bg-white border border-[#3C9BA2] p-6 rounded-xl text-center shadow-md hover:shadow-lg transition-shadow duration-300 cursor-pointer">
                <p class="text-sm font-medium text-[#3C9BA2] mb-1">Return</p>
                <p class="text-3xl font-bold text-[#3C9BA2]"><?php echo $total_returns; ?></p>
                <div class="mt-2">
                    <span class="text-sm text-red-500">+2%</span>
                    <span class="text-xs text-gray-500">from last month</span>
                </div>
            </div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="bg-white rounded-xl shadow-md">
            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="font-semibold text-base text-[#3C9BA2]">Aktivitas Terbaru</h2>
                <div class="flex items-center gap-2 text-sm">
                    <label class="text-[#3C9BA2]">Filter:</label>
                    <select class="border border-[#3C9BA2] rounded px-2 py-1 text-sm bg-white">
                        <option value="all">Semua Aktivitas</option>
                        <option value="requests">Permintaan Bahan Baku</option>
                        <option value="returns">Retur</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[#AEDFE3] text-[#3C9BA2]">
                            <th class="px-4 py-3 text-left">Jenis Aktivitas</th>
                            <th class="px-4 py-3 text-left">Deskripsi</th>
                            <th class="px-4 py-3 text-left">Waktu</th>
                            <th class="px-4 py-3 text-left">Pengguna</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_activities as $activity): ?>
                        <tr class="border-b hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs <?php echo $activity['activity_type'] === 'request' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $activity['activity_type'] === 'request' ? 'Permintaan' : 'Retur'; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($activity['description']); ?></td>
                            <td class="px-4 py-3"><?php echo date('d M Y H:i', strtotime($activity['created_at'])); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($activity['user_name']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grafik Aktivitas -->
        <div class="bg-white rounded-xl shadow-md mt-8">
            <div class="p-4 border-b">
                <h2 class="font-semibold text-base text-[#3C9BA2]">Grafik Aktivitas</h2>
            </div>
            <div class="p-4">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <!-- Script Grafik -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Data untuk grafik
            const activityData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [
                    {
                        label: 'Total Aktivitas',
                        data: [12, 19, 3, 5, 2, 3],
                        borderColor: '#3C9BA2',
                        tension: 0.1
                    }
                ]
            };

            // Konfigurasi grafik
            const config = {
                type: 'line',
                data: activityData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Aktivitas Bulanan'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                },
            };

            // Membuat grafik
            const ctx = document.getElementById('activityChart').getContext('2d');
            new Chart(ctx, config);
        </script>
                        <option>Aktivitas</option>
                        <option>Return</option>
                    </select>
                </div>
            </div>
            <table class="w-full text-sm text-left">
                <thead class="bg-[#3C9BA2] text-white">
                    <tr>
                        <th class="px-4 py-3">Order ID</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Aktivitas</th>
                        <th class="px-4 py-3">Total Barang</th>
                        <th class="px-4 py-3">Return</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b hover:bg-[#F0F8FA]">
                        <td class="px-4 py-3">001</td>
                        <td class="px-4 py-3">01/04/2025</td>
                        <td class="px-4 py-3">Pemesanan</td>
                        <td class="px-4 py-3">100</td>
                        <td class="px-4 py-3">No</td>
                    </tr>
                    <tr class="border-b hover:bg-[#F0F8FA]">
                        <td class="px-4 py-3">002</td>
                        <td class="px-4 py-3">02/04/2025</td>
                        <td class="px-4 py-3">Pengiriman</td>
                        <td class="px-4 py-3">50</td>
                        <td class="px-4 py-3">Yes</td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="p-4 flex justify-end items-center gap-2 text-sm">
                <button class="text-gray-500 px-2 py-1"> &lt; </button>
                <button class="bg-[#3C9BA2] text-white px-3 py-1 rounded">1</button>
                <button class="px-3 py-1 bg-gray-200 rounded">2</button>
                <span class="px-2">...</span>
                <button class="px-3 py-1 bg-gray-200 rounded">10</button>
                <button class="text-gray-500 px-2 py-1"> &gt; </button>
            </div>
        </div>

    </main>
</div>
</body>
</html>
