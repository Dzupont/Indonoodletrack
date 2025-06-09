<?php
// Get absolute path to project root
$rootPath = dirname(dirname(dirname(dirname(__FILE__))));
// Load database configuration
require_once $rootPath . '/config/database.php';

// Get database connection
$conn = getDBConnection();

// Get total approved and rejected requests
$sql = "SELECT 
            COUNT(CASE WHEN status = 'approved' THEN 1 END) as total_approved,
            COUNT(CASE WHEN status = 'rejected' THEN 1 END) as total_rejected,
            COUNT(*) as total_requests
        FROM requests";
$result = $conn->query($sql);
$request_stats = $result->fetch_assoc();

// Get total approved and rejected returns
$sql = "SELECT 
            SUM(CASE WHEN status = 'approved' THEN quantity ELSE 0 END) as total_approved,
            SUM(CASE WHEN status = 'rejected' THEN quantity ELSE 0 END) as total_rejected
        FROM returns
        WHERE status IN ('approved', 'rejected')";
$result = $conn->query($sql);
$returns_stats = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Gudang</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
  </style>
</head>
<body class="bg-[#f9fcfd]">
  <div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-[#4A9AB7] text-white flex flex-col py-10 px-6 rounded-tr-[40px] rounded-br-[40px]">
      <div class="flex flex-col items-center mb-12">
        <img src="/Indo_NoodleTrack-master/assets/images/logo.jpg" alt="Logo" class="w-16 h-16 mb-2 rounded-full">
        <span class="text-xl font-bold leading-5 text-center">ind0<br>noodle<br>track.</span>
      </div>
      <nav class="flex flex-col gap-4 text-sm font-semibold">
        <a href="./dashboardgudang.php" class="flex items-center gap-2 bg-white text-[#4A9AB7] px-4 py-2 rounded-lg shadow active">
          <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="./penerimaanpermintaanmasuk.php" class="flex items-center gap-2 hover:bg-white/10 px-4 py-2 rounded-lg">
          <i class="fas fa-file-invoice"></i> Permintaan Masuk
        </a>
        <a href="./returmasuk.php" class="flex items-center gap-2 hover:bg-white/10 px-4 py-2 rounded-lg">
          <i class="fas fa-sync-alt"></i> Retur Masuk
        </a>
        <a href="./monitoringgudang.php" class="flex items-center gap-2 hover:bg-white/10 px-4 py-2 rounded-lg">
          <i class="fas fa-cube"></i> Monitoring
        </a>
        <a href="./stok-bahan-baku.php" class="flex items-center gap-2 hover:bg-white/10 px-4 py-2 rounded-lg">
          <i class="fas fa-box"></i> Stok
        </a>
        <a href="../../auth/login.php" class="flex items-center gap-2 mt-6 bg-white text-[#4A9AB7] px-4 py-2 rounded-lg shadow hover:opacity-90">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </nav>
    </aside>

    <!-- Content -->
    <div class="flex-1 px-10 py-8">
<?php
require_once __DIR__ . '/../../../config/session.php';
// Check login and role
checkLoginAndRedirect();
if (getCurrentUserRole() !== 'gudang') {
    header('Location: ' . getBaseUrl() . 'views/auth/login.php');
    exit();
}
?>

      <!-- Header -->
      <div class="flex justify-between items-start mb-10">
        <h1 class="text-3xl font-bold text-[#388ca6]">Dashboard</h1>
        <div class="text-end">
          <p class="font-semibold">Divisi Gudang</p>
          <p class="text-sm text-gray-600">User: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
          <img src="https://via.placeholder.com/40" class="rounded-full mt-2" alt="User" />
        </div>
      </div>

      <!-- Dashboard Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 max-w-4xl">
        <style>
          .dashboard-card {
            transition: transform 0.2s ease-in-out;
          }
          .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          }
          .dashboard-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            line-height: 1;
          }
          .dashboard-card .label {
            font-size: 0.875rem;
            color: #64748b;
          }
        </style>
        <!-- Permintaan -->
        <div class="bg-white border border-[#4A9AB7] p-6 rounded-2xl text-[#4A9AB7] shadow-sm">
          <h2 class="text-lg font-semibold mb-4">Permintaan Bahan Baku</h2>
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-[#EAF6F8] p-6 rounded-lg flex flex-col items-center justify-center dashboard-card">
              <i class="fas fa-check-circle text-3xl mb-4 text-green-600"></i>
              <p class="label">Disetujui</p>
              <p class="number"><?php echo number_format($request_stats['total_approved']); ?></p>
            </div>
            <div class="bg-[#FBEAEA] p-6 rounded-lg flex flex-col items-center justify-center dashboard-card">
              <i class="fas fa-times-circle text-3xl mb-4 text-red-600"></i>
              <p class="label">Ditolak</p>
              <p class="number"><?php echo number_format($request_stats['total_rejected']); ?></p>
            </div>
          </div>
          <div class="mt-4 text-sm text-gray-600">
            <span class="font-medium">Total Permintaan:</span> <?php echo number_format($request_stats['total_requests']); ?>
          </div>
        </div>

        <!-- Retur -->
        <div class="bg-white border border-[#4A9AB7] p-6 rounded-2xl text-[#4A9AB7] shadow-sm">
          <h2 class="text-lg font-semibold mb-4">Retur Bahan Baku</h2>
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-[#EAF6F8] p-4 rounded-lg flex flex-col items-center justify-center">
              <i class="fas fa-check-circle text-2xl mb-2 text-green-600"></i>
              <p class="font-medium">Disetujui</p>
              <p class="text-2xl font-bold">0</p>
            </div>
            <div class="bg-[#FBEAEA] p-4 rounded-lg flex flex-col items-center justify-center">
              <i class="fas fa-times-circle text-2xl mb-2 text-red-600"></i>
              <p class="font-medium">Ditolak</p>
              <p class="text-2xl font-bold">0</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</body>
</html>