<?php
// Load database configuration
require_once __DIR__ . '/database.php';

$conn = getDBConnection();

// Add retur_bahan_baku column if not exists
$sql = "ALTER TABLE dashboard 
        ADD COLUMN IF NOT EXISTS retur_bahan_baku INT DEFAULT 0,
        ADD COLUMN IF NOT EXISTS retur_bahan_baku_rejected INT DEFAULT 0";

if ($conn->query($sql) === TRUE) {
    echo "Columns added successfully\n";
} else {
    echo "Error adding columns: " . $conn->error . "\n";
}

// Update dashboard with current retur quantities
$sql = "UPDATE dashboard 
        SET retur_bahan_baku = (SELECT SUM(quantity) FROM returns WHERE status = 'approved'),
            retur_bahan_baku_rejected = (SELECT SUM(quantity) FROM returns WHERE status = 'rejected')";

if ($conn->query($sql) === TRUE) {
    echo "Dashboard updated successfully\n";
} else {
    echo "Error updating dashboard: " . $conn->error . "\n";
}

$conn->close();
