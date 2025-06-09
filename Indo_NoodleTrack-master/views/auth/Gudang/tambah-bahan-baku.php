<?php
// Contoh penyimpanan input dan error (simulasi saja)
$errors = [];
$old = $_POST ?? [];

// Jika ada submit (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi dasar
    if (empty($_POST['nama_bahanbaku'])) {
        $errors[] = "Nama Bahan Baku wajib diisi.";
    }
    if (empty($_POST['jenis_bahanbaku'])) {
        $errors[] = "Jenis Bahan Baku wajib dipilih.";
    }

    // Lakukan penyimpanan ke database di sini...
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Bahan Baku</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 2rem;
        }

        .form-container {
            background-color: #ffffff;
            max-width: 800px;
            margin: auto;
            padding: 2rem 3rem;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .form-header {
            color: #1b6f7a;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 2rem;
        }

        .form-label {
            display: block;
            color: #1e293b;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-input,
        .form-select,
        .form-textarea {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.75rem;
            width: 100%;
            margin-bottom: 1rem;
            box-sizing: border-box;
        }

        .form-button {
            background-color: #2e94a6;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            cursor: pointer;
        }

        .form-button:hover {
            background-color: #267c8c;
        }

        .form-button-secondary {
            background-color: white;
            border: 1px solid #2e94a6;
            color: #2e94a6;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            margin-right: 1rem;
            text-decoration: none;
        }

        .form-button-secondary:hover {
            background-color: #f0fafa;
        }

        .error-message {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .flex {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 class="form-header">Tambah Bahan Baku Baru</h2>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/IndoNoodleTrack/Indo_NoodleTrack-master/controllers/store-bahan-baku.php" method="POST" enctype="multipart/form-data">
            <!-- Nama Bahan Baku -->
            <label class="form-label" for="nama_bahanbaku">Nama Bahan Baku <span style="color:red">*</span></label>
            <input class="form-input" type="text" name="nama_bahanbaku" id="nama_bahanbaku" required value="<?= htmlspecialchars($old['nama_bahanbaku'] ?? '') ?>">

            <!-- Jenis Bahan Baku -->
            <label class="form-label" for="jenis_bahanbaku">Jenis Bahan Baku <span style="color:red">*</span></label>
            <select class="form-select" name="jenis_bahanbaku" id="jenis_bahanbaku" required>
    <option value="">Pilih Jenis</option>
    <?php
    $jenisOptions = [
        'Tepung Terigu',
        'Tepung Tapioka',
        'Air',
        'Garam',
        'Telur',
        'Minyak Nabati',
        'Pewarna Makanan',
        'Pengawet',
        'Bumbu Penyedap',
        'Kemasan Plastik',
        'Label / Stiker',
        'Box Karton'
    ];
    foreach ($jenisOptions as $jenis) {
        $selected = ($old['jenis_bahanbaku'] ?? '') === $jenis ? 'selected' : '';
        echo "<option value=\"$jenis\" $selected>$jenis</option>";
    }
    ?>
</select>


            <!-- Jumlah Stok -->
            <label class="form-label" for="stok_bahanbaku">Jumlah Stok</label>
            <input class="form-input" type="number" name="stok_bahanbaku" id="stok_bahanbaku" min="0" value="<?= htmlspecialchars($old['stok_bahanbaku'] ?? '0') ?>">

            <!-- Satuan -->
            <label class="form-label" for="satuan">Satuan</label>
            <select class="form-select" name="satuan" id="satuan" required>
                <option value="">Pilih Satuan</option>
                <option value="kg" <?php echo ($old['satuan'] ?? '') === 'kg' ? 'selected' : ''; ?>>Kilogram (kg)</option>
                <option value="liter" <?php echo ($old['satuan'] ?? '') === 'liter' ? 'selected' : ''; ?>>Liter (L)</option>
                <option value="pcs" <?php echo ($old['satuan'] ?? '') === 'pcs' ? 'selected' : ''; ?>>Pieces (pcs)</option>
            </select>

            <!-- Minimal Stok -->
            <label class="form-label" for="minimal_stok">Minimal Stok</label>
            <input class="form-input" type="number" name="minimal_stok" id="minimal_stok" min="0" value="<?php echo htmlspecialchars($old['minimal_stok'] ?? '0'); ?>">
            <p style="font-size: 0.8rem; color: #6b7280;">Stok minimum yang harus tersedia</p>



            <!-- Tanggal Expired -->
            <label class="form-label" for="tanggal_expired">Tanggal Expired</label>
            <input class="form-input" type="date" name="tanggal_expired" id="tanggal_expired" value="<?= htmlspecialchars($old['tanggal_expired'] ?? '') ?>">

            <!-- Gambar -->
            <label class="form-label" for="gambar">Gambar</label>
            <input class="form-input" type="file" name="gambar" id="gambar" accept="image/*">
            <p style="font-size: 0.8rem; color: #6b7280;">Jika tidak diisi, gambar default akan digunakan sesuai kategori</p>

            <!-- Deskripsi -->
            <label class="form-label" for="deskripsi">Deskripsi</label>
            <textarea class="form-textarea" name="deskripsi" id="deskripsi" rows="4"><?php echo htmlspecialchars($old['deskripsi'] ?? ''); ?></textarea>
            <p style="font-size: 0.8rem; color: #6b7280;">Deskripsi singkat tentang bahan baku</p>

            <!-- Buttons -->
            <div class="flex" style="margin-top: 2rem;">
                <a href="../Gudang/stok-bahan-baku.php" class="form-button-secondary">Batal</a>
                <button type="submit" class="form-button">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>
