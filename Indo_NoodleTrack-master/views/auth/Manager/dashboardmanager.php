<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#0095c8] text-white p-6 rounded-tr-3xl rounded-br-3xl">
            <div class="text-center mb-10">
                <img src="assets/images/logo.jpg" alt="Logo" class="w-16 h-16 mx-auto rounded-full mb-2">
                <div class="text-xl font-bold leading-tight">indo<br>noodle<br>track.</div>
            </div>
            <nav class="flex flex-col gap-4">
                <a href="dashboardmanager.php" class="px-4 py-2 font-semibold bg-white text-[#0095c8] rounded hover:bg-gray-100">Dashboard</a>

                <!-- Tombol redirect ke fiturlaporan.php -->
                <form action="fiturlaporan.php" method="get">
                    <button type="submit" class="w-full text-left px-4 py-2 mt-2 font-semibold bg-white text-[#0095c8] rounded hover:bg-gray-100 transition">
                        Laporan
                    </button>
                </form>

                <!-- Filter -->
                <div class="mt-6">
                    <p class="text-sm font-semibold ml-4 mb-1">Filter</p>
                    <div class="ml-4 space-y-2">
                        <input type="text" placeholder="Bulan..." class="w-full px-2 py-1 rounded text-black">
                        <input type="text" placeholder="Tahun..." class="w-full px-2 py-1 rounded text-black">
                        <button class="w-full mt-2 bg-white text-[#0095c8] font-bold px-3 py-1 rounded">Cari</button>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-y-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Dashboard</h1>
                <div class="flex items-center gap-4">
                    <a href="logout.php" class="text-sm font-medium text-red-600 hover:underline">Logout</a>
                    <div class="flex items-center">
                        <img src="assets/images/profile.jpg" alt="Profile" class="w-10 h-10 rounded-full mr-2">
                        <div>
                            <p class="font-semibold">Manager</p>
                            <p class="text-xs text-gray-500">User ID: 02081999</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Month & Year -->
            <div class="flex items-center space-x-4 mb-4">
                <button class="bg-[#a4d4de] text-white font-semibold px-4 py-1 rounded">APRIL</button>
                <button class="bg-[#a4d4de] text-white font-semibold px-4 py-1 rounded">2025</button>
            </div>

            <!-- Summary Boxes -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white shadow rounded p-6 text-center">
                    <p class="text-lg font-semibold text-gray-600">Total Aktivitas</p>
                    <p class="text-4xl font-bold mt-2">24</p>
                </div>
                <div class="bg-white shadow rounded p-6 text-center">
                    <p class="text-lg font-semibold text-gray-600">Permintaan Bahan Baku</p>
                    <p class="text-4xl font-bold mt-2">20</p>
                </div>
                <div class="bg-white shadow rounded p-6 text-center">
                    <p class="text-lg font-semibold text-gray-600">Return</p>
                    <p class="text-4xl font-bold mt-2">4</p>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white shadow rounded p-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Aktivitas Berhasil</h2>
                    <div class="text-sm text-gray-600">Show <strong>All Column</strong></div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300">
                        <thead>
                            <tr class="bg-[#0095c8] text-white">
                                <th class="py-2 px-4 border">Order ID</th>
                                <th class="py-2 px-4 border">Tanggal</th>
                                <th class="py-2 px-4 border">Aktivitas</th>
                                <th class="py-2 px-4 border">Total Barang</th>
                                <th class="py-2 px-4 border">Return</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center hover:bg-gray-50">
                                <td class="py-2 px-4 border">001</td>
                                <td class="py-2 px-4 border">01/04/2025</td>
                                <td class="py-2 px-4 border">Pemesanan</td>
                                <td class="py-2 px-4 border">100</td>
                                <td class="py-2 px-4 border">No</td>
                            </tr>
                            <tr class="text-center hover:bg-gray-50">
                                <td class="py-2 px-4 border">002</td>
                                <td class="py-2 px-4 border">02/04/2025</td>
                                <td class="py-2 px-4 border">Pengiriman</td>
                                <td class="py-2 px-4 border">50</td>
                                <td class="py-2 px-4 border">Yes</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Placeholder -->
                <div class="mt-4 flex justify-end text-sm text-gray-500">
                    <span> &lt; 1 2 3 ... 10 &gt; </span>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
