<?php
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

// Get selected month and year from form
$bulan = isset($_POST['bulan']) ? $_POST['bulan'] : date('F');
$tahun = isset($_POST['tahun']) ? $_POST['tahun'] : date('Y');

// Generate days array for the selected month
$days = [];
$month = date('m', strtotime($bulan));
$first_day = strtotime("$tahun-$month-01");
$last_day = strtotime("$tahun-$month-" . date('t', $first_day));

// Get all activities for the selected month
$activities = $conn->query("
    SELECT 
        DATE_FORMAT(r.created_at, '%d') as tanggal,
        DAYNAME(r.created_at) as hari,
        DATE_FORMAT(r.created_at, '%M') as bulan,
        DATE_FORMAT(r.created_at, '%Y') as tahun,
        SUM(r.quantity) as total_barang,
        COUNT(r.id) as jumlah_return,
        CASE 
            WHEN COUNT(r.id) > 0 THEN 'Ada Return'
            ELSE 'Tidak ada Return'
        END as status
    FROM returns r
    WHERE DATE_FORMAT(r.created_at, '%Y-%m') = '$tahun-$month'
    GROUP BY DATE_FORMAT(r.created_at, '%Y-%m-%d')
    ORDER BY tanggal
")->fetch_all(MYSQLI_ASSOC);

// Create array with all days of the month
$current_day = $first_day;
$day_number = 1;
while ($current_day <= $last_day && $day_number <= 30) {
    $day = [
        'tanggal' => $day_number,
        'hari' => date('l', $current_day),
        'bulan' => date('F', $current_day),
        'tahun' => date('Y', $current_day),
        'total_barang' => 0,
        'return' => 'Tidak ada',
        'status' => 'Tidak ada aktivitas',
        'jumlah_return' => 0
    ];
    
    // Find matching activity if exists
    $matching_activity = null;
    foreach ($activities as $activity) {
        if ($activity['tanggal'] == $day_number) {
            $matching_activity = $activity;
            break;
        }
    }
    
    if ($matching_activity) {
        $day = array_merge($day, $matching_activity);
    }
    
    $days[] = $day;
    $current_day = strtotime('+1 day', $current_day);
    $day_number++;
}

// Tutup koneksi database
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: "Poppins", sans-serif; background-color: #F6F6F6; }
        .sidebar { background-color: #3C9BA2; }
        .active-link { background-color: white; color: #3C9BA2; border-radius: 12px; }
        
        .laporan-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            margin-bottom: 0.75rem;
            min-height: 150px;
        }
        
        .laporan-card:hover {
            transform: translateY(-5px);
        }
        
        .tanggal-header {
            background: #3C9BA2;
            color: white;
            padding: 0.75rem;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .tanggal-header .tanggal {
            font-size: 24px;
            font-weight: bold;
            background: #3DB1C9;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
        
        .tanggal-header .info {
            display: flex;
            flex-direction: column;
        }
        
        .tanggal-header .info .hari {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .tanggal-header .info .tanggal-bulan {
            font-size: 12px;
            opacity: 0.8;
        }
        
        .laporan-content {
            padding: 0.75rem;
            border-top: 1px solid #eee;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .laporan-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            padding: 0.25rem 0;
        }
        
        .status {
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 8px;
            font-size: 12px;
        }
        
        .status-berhasil {
            background: #E5F7F2;
            color: #0D8B9E;
        }
        
        .status-gagal {
            background: #FEE2E2;
            color: #DC2626;
        }
        
        .status-tidak-ada {
            background: #F3F4F6;
            color: #6B7280;
        }
        
        /* Scrollbar Styling */
        .laporan-section {
            scrollbar-width: thin;
            scrollbar-color: #3C9BA2 #F6F6F6;
        }
        
        .laporan-section::-webkit-scrollbar {
            width: 8px;
        }
        
        .laporan-section::-webkit-scrollbar-track {
            background: #F6F6F6;
            border-radius: 4px;
        }
        
        .laporan-section::-webkit-scrollbar-thumb {
            background-color: #3C9BA2;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-[#f5f5f5] text-[#333]">

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
                <form method="POST" action="" class="space-y-2">
                    <input type="text" name="bulan" value="<?php echo htmlspecialchars($bulan); ?>" placeholder="Bulan..." class="w-full mb-2 px-3 py-2 rounded-md text-black text-sm">
                    <input type="text" name="tahun" value="<?php echo htmlspecialchars($tahun); ?>" placeholder="Tahun..." class="w-full mb-2 px-3 py-2 rounded-md text-black text-sm">
                    <button type="submit" class="w-full bg-white text-[#3C9BA2] font-semibold px-3 py-2 rounded-md hover:bg-[#d8f2f2] transition">Cari</button>
                </form>
            </div>

            <!-- Logout -->
            <a href="../../auth/login.php" class="mt-10 w-full bg-white text-[#3C9BA2] px-4 py-2 text-center font-semibold rounded-lg hover:bg-[#d8f2f2] transition duration-300">
                Keluar
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-10">
        <header class="flex justify-between items-center mb-10">
            <h1 class="text-[#0D8B9E] font-extrabold text-[28px] leading-[28px] select-none">LAPORAN</h1>
            <div class="flex items-center gap-4">
                <img class="w-10 h-10 rounded-full object-cover" src="https://storage.googleapis.com/a1aa/image/487a7b04-effd-4cb3-4b52-3279ab93034c.jpg" alt="Manager" />
                <div class="text-right">
                    <div class="text-[14px] font-semibold text-[#3B2F2F]">Manager</div>
                    <div class="text-[10px] font-light text-[#9CA3AF]">User id : 02081999</div>
                </div>
            </div>
        </header>

        <div class="inline-flex rounded-lg border-4 border-[#3DB1C9] overflow-hidden mb-10 select-none">
            <button class="bg-[#3DB1C9] text-white text-[14px] font-semibold px-6 py-2 cursor-default" disabled><?php echo strtoupper($bulan); ?></button>
            <button class="bg-[#B7D7E3] text-[#3DB1C9] text-[14px] font-semibold px-6 py-2 cursor-default" disabled><?php echo $tahun; ?></button>
        </div>

        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[600px] overflow-y-auto p-2 bg-white rounded-lg shadow-md">
            <?php foreach ($days as $item): ?>
            <div class="laporan-card">
                <div class="tanggal-header">
                    <div class="tanggal"><?php echo $item['tanggal']; ?></div>
                    <div class="info">
                        <div class="hari"><?php echo $item['hari']; ?></div>
                        <div class="tanggal-bulan"><?php echo $item['bulan'] . ' ' . $item['tahun']; ?></div>
                    </div>
                </div>
                <div class="laporan-content">
                    <div class="laporan-row">
                        <span>Total Barang</span>
                        <span><?php echo $item['total_barang']; ?></span>
                    </div>
                    <div class="laporan-row">
                        <span>Return</span>
                        <span><?php echo $item['jumlah_return'] > 0 ? $item['jumlah_return'] . ' barang' : 'Tidak ada'; ?></span>
                    </div>
                    <div class="laporan-row">
                        <span>Status</span>
                        <span class="status <?php echo $item['status'] === 'Ada Return' ? 'status-gagal' : ($item['status'] === 'Berhasil' ? 'status-berhasil' : 'status-tidak-ada'); ?>">
                            <?php echo $item['status']; ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </section>
    </main>
</div>