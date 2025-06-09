<?php
require_once __DIR__ . '/database.php';

$conn = getDBConnection();

// Tambahkan kolom stock jika belum ada
$sql = "ALTER TABLE raw_materials ADD COLUMN IF NOT EXISTS stock DECIMAL(10,2) NOT NULL DEFAULT 0";
if (!$conn->query($sql)) {
    die("Error adding stock column: " . $conn->error);
}

// Insert data bahan baku
$materials = [
    ['name' => 'Tepung Terigu', 'unit' => 'kg', 'stock' => 0],
    ['name' => 'Tepung Tapioka', 'unit' => 'kg', 'stock' => 0],
    ['name' => 'Air', 'unit' => 'liter', 'stock' => 0],
    ['name' => 'Garam', 'unit' => 'kg', 'stock' => 0],
    ['name' => 'Telur', 'unit' => 'pcs', 'stock' => 0],
    ['name' => 'Minyak Nabati', 'unit' => 'liter', 'stock' => 0],
    ['name' => 'Pewarna Makanan', 'unit' => 'ml', 'stock' => 0],
    ['name' => 'Pengawet', 'unit' => 'ml', 'stock' => 0],
    ['name' => 'Bumbu Penyedap', 'unit' => 'kg', 'stock' => 0],
    ['name' => 'Kemasan Plastik', 'unit' => 'pcs', 'stock' => 0],
    ['name' => 'Label / Stiker', 'unit' => 'pcs', 'stock' => 0],
    ['name' => 'Box Karton', 'unit' => 'pcs', 'stock' => 0]
];

// Insert atau update data
foreach ($materials as $material) {
    $name = $material['name'];
    $unit = $material['unit'];
    $stock = $material['stock'];
    
    $sql = "INSERT INTO raw_materials (name, unit, stock, minimum_stock) 
            VALUES ('$name', '$unit', $stock, 0)
            ON DUPLICATE KEY UPDATE 
            name = VALUES(name),
            unit = VALUES(unit),
            stock = VALUES(stock),
            minimum_stock = VALUES(minimum_stock)";
            
    if (!$conn->query($sql)) {
        die("Error inserting/updating data: " . $conn->error);
    }
}

echo "Data bahan baku berhasil diinisialisasi!";
