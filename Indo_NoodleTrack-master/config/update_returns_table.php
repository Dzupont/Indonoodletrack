<?php
// Get database connection
require_once __DIR__ . '/database.php';
$conn = getDBConnection();

// Check if returns table exists and has the correct structure
$sql = "SHOW COLUMNS FROM returns";
$result = $conn->query($sql);
$columns = [];
while ($row = $result->fetch_assoc()) {
    $columns[] = $row['Field'];
}

// Add status column if it doesn't exist
if (!in_array('status', $columns)) {
    $sql = "ALTER TABLE returns ADD COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'";
    if ($conn->query($sql)) {
        echo "Status column added to returns table\n";
    } else {
        echo "Error adding status column: " . $conn->error . "\n";
    }
} else {
    echo "Status column already exists in returns table\n";
}

// Update existing records to have status 'pending'
$sql = "UPDATE returns SET status = 'pending' WHERE status IS NULL";
if ($conn->query($sql)) {
    echo "Updated existing records with pending status\n";
} else {
    echo "Error updating records: " . $conn->error . "\n";
}

$conn->close();
