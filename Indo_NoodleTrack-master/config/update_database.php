<?php
require_once __DIR__ . '/database.php';

$conn = getDBConnection();

// Drop tabel raw_materials yang lama
$sql = "DROP TABLE IF EXISTS raw_materials";
if (!$conn->query($sql)) {
    die("Error dropping table: " . $conn->error);
}

// Buat ulang tabel dengan struktur baru
$sql = "CREATE TABLE raw_materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    jenis VARCHAR(50) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    stock DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (!$conn->query($sql)) {
    die("Error creating table: " . $conn->error);
}

// Masukkan data bahan baku
$materials = [
    ['name' => 'Tepung Terigu', 'jenis' => 'Tepung', 'unit' => 'kg', 'stock' => 0],
    ['name' => 'Tepung Tapioka', 'jenis' => 'Tepung', 'unit' => 'kg', 'stock' => 0],
    ['name' => 'Air', 'jenis' => 'Lainnya', 'unit' => 'liter', 'stock' => 0],
    ['name' => 'Garam', 'jenis' => 'Bumbu', 'unit' => 'kg', 'stock' => 0],
    ['name' => 'Telur', 'jenis' => 'Bahan Pokok', 'unit' => 'pcs', 'stock' => 0],
    ['name' => 'Minyak Nabati', 'jenis' => 'Minyak', 'unit' => 'liter', 'stock' => 0],
    ['name' => 'Pewarna Makanan', 'jenis' => 'Bahan Tambahan', 'unit' => 'ml', 'stock' => 0],
    ['name' => 'Pengawet', 'jenis' => 'Bahan Tambahan', 'unit' => 'ml', 'stock' => 0],
    ['name' => 'Bumbu Penyedap', 'jenis' => 'Bumbu', 'unit' => 'kg', 'stock' => 0],
    ['name' => 'Kemasan Plastik', 'jenis' => 'Kemasan', 'unit' => 'pcs', 'stock' => 0],
    ['name' => 'Label / Stiker', 'jenis' => 'Kemasan', 'unit' => 'pcs', 'stock' => 0],
    ['name' => 'Box Karton', 'jenis' => 'Kemasan', 'unit' => 'pcs', 'stock' => 0]
];

// Masukkan data ke dalam tabel
foreach ($materials as $material) {
    $sql = "INSERT INTO raw_materials (name, jenis, unit, stock) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssd", $material['name'], $material['jenis'], $material['unit'], $material['stock']);
    if (!$stmt->execute()) {
        die("Error inserting data: " . $conn->error);
    }
}

echo "Database berhasil diperbarui!";
