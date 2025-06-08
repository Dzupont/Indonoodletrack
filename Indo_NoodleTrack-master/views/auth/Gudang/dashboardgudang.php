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
    .nav-link {
      @apply flex items-center gap-3 px-4 py-2 hover:bg-white/20 rounded-lg transition duration-150;
    }
    .nav-link.active {
      @apply bg-white/20;
    }
  </style>
</head>
<body class="bg-[#f9fcfd]">
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#4A9AB7] text-white flex flex-col py-10 px-6 rounded-tr-3xl rounded-br-3xl">
      <div class="flex flex-col items-center mb-12">
        <img src="/Indo_NoodleTrack-master/assets/images/logo.jpg" alt="Logo" class="w-16 h-16 mb-2 rounded-full">
        <span class="text-xl font-bold">indo<br>noodle<br>track.</span>
      </div>
      <nav class="flex flex-col gap-4 text-sm font-semibold">
        <a href="./dashboardgudang.php" class="flex items-center gap-2 active">
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
        <a href="./stok-bahan-baku.php" class="flex items-center gap-2">
          <i class="fas fa-box"></i> Stok
        </a>
        <a href="../../auth/login.php" class="flex items-center gap-2 mt-4">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </nav>
    </aside>

    <!-- Content -->
    <div class="flex-1 p-10">
      <div class="flex justify-between items-start mb-10">
        <h1 class="text-3xl font-bold text-[#388ca6]">Dashboard</h1>
        <div class="text-end">
          <p class="font-semibold">Divisi Gudang</p>
          <p class="text-sm text-gray-600">User id: 0023899</p>
          <img src="https://via.placeholder.com/40" class="rounded-full mt-2" alt="User" />
        </div>
      </div>

      <!-- Dashboard Cards -->
      <div class="grid grid-cols-1 gap-8 max-w-3xl">
        <!-- Permintaan -->
        <div class="bg-[#3E90B6] p-6 rounded-xl text-white shadow">
          <h2 class="text-lg font-semibold mb-4">Permintaan Bahan Baku</h2>
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-white text-[#3E90B6] p-4 rounded-lg flex flex-col items-center justify-center">
              <i class="fas fa-user-check text-2xl mb-2 text-green-500"></i>
              <p class="font-medium">Di Setujui</p>
              <p class="text-xl font-bold">0</p>
            </div>
            <div class="bg-white text-[#3E90B6] p-4 rounded-lg flex flex-col items-center justify-center">
              <i class="fas fa-user-times text-2xl mb-2 text-red-500"></i>
              <p class="font-medium">Di Tolak</p>
              <p class="text-xl font-bold">0</p>
            </div>
          </div>
        </div>

        <!-- Retur -->
        <div class="bg-[#3E90B6] p-6 rounded-xl text-white shadow">
          <h2 class="text-lg font-semibold mb-4">Retur Bahan Baku</h2>
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-white text-[#3E90B6] p-4 rounded-lg flex flex-col items-center justify-center">
              <i class="fas fa-user-check text-2xl mb-2 text-green-500"></i>
              <p class="font-medium">Di Setujui</p>
              <p class="text-xl font-bold">0</p>
            </div>
            <div class="bg-white text-[#3E90B6] p-4 rounded-lg flex flex-col items-center justify-center">
              <i class="fas fa-user-times text-2xl mb-2 text-red-500"></i>
              <p class="font-medium">Di Tolak</p>
              <p class="text-xl font-bold">0</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
