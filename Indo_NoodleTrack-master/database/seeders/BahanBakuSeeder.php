<?php

require_once __DIR__ . '/../../config/database.php';

function generateKode($jenis, $conn) {
    $prefix = strtoupper(substr($jenis, 0, 2));
    $search_pattern = $prefix . '%';
    
    $query = "SELECT MAX(CAST(SUBSTRING(kode, 3) AS UNSIGNED)) as last_number 
              FROM stocks 
              WHERE kode LIKE '" . $conn->real_escape_string($search_pattern) . "'";
    
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $next_number = ($row['last_number'] ?? 0) + 1;
    return sprintf("%s%03d", $prefix, $next_number);
}

function insertBahanBaku($conn, $data) {
    $query = "
        INSERT INTO stocks 
        (kode, nama, jenis, satuan, stok, tanggal_expired, deskripsi, gambar, minimal_stok, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssdssi", 
        $data['kode'],
        $data['nama'],
        $data['jenis'],
        $data['satuan'],
        $data['stok'],
        $data['tanggal_expired'],
        $data['deskripsi'],
        $data['gambar'],
        $data['minimal_stok']
    );
    
    return $stmt->execute();
}

// Connect to database
$conn = getDBConnection();

// Dummy data for bahan baku
$dummyData = [
    [
        'nama' => 'Tepung Terigu',
        'jenis' => 'Tepung Terigu',
        'satuan' => 'kg',
        'stok' => 1500,
        'tanggal_expired' => '2025-06-30',
        'deskripsi' => 'Tepung terigu berkualitas tinggi untuk produksi mie',
        'gambar' => 'images/bahan-baku/te_001.jpg',
        'minimal_stok' => 100
    ],
    [
        'nama' => 'Tepung Tapioka',
        'jenis' => 'Tepung Tapioka',
        'satuan' => 'kg',
        'stok' => 800,
        'tanggal_expired' => '2025-07-31',
        'deskripsi' => 'Tepung tapioka untuk tekstur mie',
        'gambar' => 'images/bahan-baku/tp_001.jpg',
        'minimal_stok' => 50
    ],
    [
        'nama' => 'Air',
        'jenis' => 'Air',
        'satuan' => 'liter',
        'stok' => 10000,
        'deskripsi' => 'Air bersih untuk proses produksi',
        'gambar' => 'images/bahan-baku/ai_001.jpg',
        'minimal_stok' => 1000
    ],
    [
        'nama' => 'Garam',
        'jenis' => 'Garam',
        'satuan' => 'kg',
        'stok' => 300,
        'tanggal_expired' => '2026-06-30',
        'deskripsi' => 'Garam untuk rasa mie',
        'gambar' => 'images/bahan-baku/ga_001.jpg',
        'minimal_stok' => 20
    ],
    [
        'nama' => 'Telur',
        'jenis' => 'Telur',
        'satuan' => 'kg',
        'stok' => 200,
        'tanggal_expired' => '2025-06-15',
        'deskripsi' => 'Telur segar untuk variasi mie',
        'gambar' => 'images/bahan-baku/tl_001.jpg',
        'minimal_stok' => 50
    ],
    [
        'nama' => 'Minyak Nabati',
        'jenis' => 'Minyak Nabati',
        'satuan' => 'liter',
        'stok' => 500,
        'tanggal_expired' => '2025-12-31',
        'deskripsi' => 'Minyak nabati untuk proses produksi',
        'gambar' => 'images/bahan-baku/mn_001.jpg',
        'minimal_stok' => 100
    ],
    [
        'nama' => 'Pewarna Makanan',
        'jenis' => 'Pewarna Makanan',
        'satuan' => 'ml',
        'stok' => 5000,
        'tanggal_expired' => '2025-11-30',
        'deskripsi' => 'Pewarna makanan untuk variasi warna mie',
        'gambar' => 'images/bahan-baku/pm_001.jpg',
        'minimal_stok' => 500
    ],
    [
        'nama' => 'Pengawet',
        'jenis' => 'Pengawet',
        'satuan' => 'kg',
        'stok' => 100,
        'tanggal_expired' => '2025-12-31',
        'deskripsi' => 'Pengawet untuk menjaga kualitas mie',
        'gambar' => 'images/bahan-baku/pg_001.jpg',
        'minimal_stok' => 10
    ],
    [
        'nama' => 'Bumbu Penyedap',
        'jenis' => 'Bumbu Penyedap',
        'satuan' => 'kg',
        'stok' => 200,
        'tanggal_expired' => '2025-09-30',
        'deskripsi' => 'Bumbu penyedap untuk rasa mie',
        'gambar' => 'images/bahan-baku/bp_001.jpg',
        'minimal_stok' => 20
    ],
    [
        'nama' => 'Kemasan Plastik',
        'jenis' => 'Kemasan Plastik',
        'satuan' => 'pcs',
        'stok' => 5000,
        'deskripsi' => 'Kemasan plastik untuk mie instan',
        'gambar' => 'images/bahan-baku/kp_001.jpg',
        'minimal_stok' => 1000
    ],
    [
        'nama' => 'Label / Stiker',
        'jenis' => 'Label / Stiker',
        'satuan' => 'pcs',
        'stok' => 10000,
        'deskripsi' => 'Label dan stiker untuk kemasan mie instan',
        'gambar' => 'images/bahan-baku/ls_001.jpg',
        'minimal_stok' => 2000
    ],
    [
        'nama' => 'Box Karton',
        'jenis' => 'Box Karton',
        'satuan' => 'pcs',
        'stok' => 2000,
        'deskripsi' => 'Box karton untuk pengemasan mie instan',
        'gambar' => 'images/bahan-baku/bk_001.jpg',
        'minimal_stok' => 500
    ]
];

// Insert dummy data
foreach ($dummyData as $data) {
    $data['kode'] = generateKode($data['jenis'], $conn);
    if (!insertBahanBaku($conn, $data)) {
        echo "Failed to insert: " . $data['nama'] . "\n";
    } else {
        echo "Successfully inserted: " . $data['nama'] . "\n";
    }
}

$conn->close();

echo "\nDummy data seeding completed!";
