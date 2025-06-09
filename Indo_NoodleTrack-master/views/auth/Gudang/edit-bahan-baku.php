
<?php
// Simulasi data, karena ini bukan Laravel
$bahanBaku = (object)[
    'id_bahanbaku' => 1,
    'nama_bahanbaku' => 'Carboxymethyl Cellulose',
    'jenis_bahanbaku' => 'Pengental',
    'stok_bahanbaku' => 350,
    'satuan' => 'gram',
    'harga' => 15000,
    'tanggal_expired' => '2025-12-31',
    'kode' => 'BB001',
    'gambar' => 'cmc.png',
    'deskripsi' => 'Bahan dasar utama untuk membuat mie, berperan dalam membentuk struktur dan tekstur.'
];
$jenisOptions = ['Pengental', 'Pengawet', 'Pewarna'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Bahan Baku</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 py-10">
    <div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-blue-700 mb-6">Edit Bahan Baku</h2>
        
        <form action="update_bahanbaku.php?id=<?= $bahanBaku->id_bahanbaku ?>" method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bahan Baku</label>
                    <input type="text" name="nama_bahanbaku" value="<?= $bahanBaku->nama_bahanbaku ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Bahan Baku</label>
                    <select name="jenis_bahanbaku" required class="w-full px-4 py-2 border border-gray-300 rounded-md">
                        <option value="">Pilih Jenis</option>
                        <?php foreach ($jenisOptions as $jenis): ?>
                            <option value="<?= $jenis ?>" <?= $bahanBaku->jenis_bahanbaku === $jenis ? 'selected' : '' ?>><?= $jenis ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                    <input type="number" name="stok_bahanbaku" value="<?= $bahanBaku['stok_bahanbaku'] ?>" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                    <input type="text" name="satuan" value="<?= $bahanBaku['satuan'] ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">Rp</span>
                        </div>
                        <input type="number" name="harga" value="<?= $bahanBaku->harga ?>" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Expired</label>
                    <input type="date" name="tanggal_expired" value="<?= $bahanBaku['tanggal_expired'] ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode</label>
                    <input type="text" value="<?= $bahanBaku['kode'] ?>" class="w-full px-4 py-2 border border-gray-300 bg-gray-100 rounded-md" readonly>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar</label>
                    <div class="flex items-center space-x-4">
                        <div class="w-24 h-24 bg-gray-100 rounded-md overflow-hidden">
                            <img src="item-images/<?= $bahanBaku['gambar'] ?>" alt="<?= $bahanBaku['nama_bahanbaku'] ?>" class="w-full h-full object-contain">
                            <img src="item-images/<?= $bahanBaku->gambar ?>" alt="<?= $bahanBaku->nama_bahanbaku ?>" class="w-full h-full object-contain">
                        </div>
                        <input type="file" name="gambar" class="flex-1 px-4 py-2 border border-gray-300 rounded-md" accept="image/*">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-md"><?= $bahanBaku->deskripsi ?></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8">
                <a href="./stok-bahan-baku.php" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>
