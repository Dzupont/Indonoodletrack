<?php
// Contoh data laporan (biasanya dari database)
$laporan = [
    ['tanggal' => '24', 'hari' => 'Kamis', 'bulan' => 'April', 'tahun' => '2025', 'total_barang' => 30, 'return' => 'Tidak ada', 'status' => 'Berhasil'],
    ['tanggal' => '25', 'hari' => 'Jumat', 'bulan' => 'April', 'tahun' => '2025', 'total_barang' => 25, 'return' => '1 barang', 'status' => 'Gagal'],
    // Tambah data lain sesuai kebutuhan...
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>Laporan 2</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    body { font-family: "Poppins", sans-serif; }
  </style>
</head>
<body class="bg-[#F5F9FA] min-h-screen flex">
  <!-- Sidebar -->
  <aside class="bg-[#0D8B9E] w-64 min-h-screen rounded-tr-[40px] rounded-br-[40px] flex flex-col px-6 py-8 text-white relative">
    <!-- Konten sidebar -->
  </aside>

  <!-- Main content -->
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
      <button class="bg-[#3DB1C9] text-white text-[14px] font-semibold px-6 py-2 cursor-default" disabled>APRIL</button>
      <button class="bg-[#B7D7E3] text-[#3DB1C9] text-[14px] font-semibold px-6 py-2 cursor-default" disabled>2025</button>
    </div>

    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($laporan as $item): ?>
      <article class="bg-[#0D8B9E] rounded-md shadow-md text-white w-full max-w-xs select-none">
        <header class="flex">
          <div class="bg-[#3DB1C9] w-14 h-14 flex flex-col items-center justify-center rounded-tl-md rounded-bl-md">
            <span class="font-extrabold text-[20px] leading-[20px]"><?php echo $item['tanggal']; ?></span>
          </div>
          <div class="px-3 py-2 flex flex-col justify-center text-[10px] font-semibold leading-tight">
            <span><?php echo $item['hari']; ?></span>
            <span><?php echo $item['bulan'] . ' ' . $item['tahun']; ?></span>
          </div>
        </header>
        <div class="px-3 py-2 text-[10px] font-semibold leading-tight">
          <div class="flex justify-between">
            <span>Total Barang</span>
            <span><?php echo $item['total_barang']; ?></span>
          </div>
          <div class="flex justify-between">
            <span>Return</span>
            <span><?php echo $item['return']; ?></span>
          </div>
          <div class="flex justify-between">
            <span>Status</span>
            <span><?php echo $item['status']; ?></span>
          </div>
        </div>
        <footer class="px-3 py-1 border-t border-[#3DB1C9] text-[10px] font-semibold flex justify-between items-center cursor-pointer hover:underline">
          <span>Klik untuk melihat Laporan</span>
          <i class="fas fa-chevron-right"></i>
        </footer>
      </article>
      <?php endforeach; ?>
    </section>