<?php
require_once __DIR__ . '/database.php';

$conn = getDBConnection();

try {
    // Add approved_at column
    $sql = "ALTER TABLE requests 
            ADD COLUMN approved_at TIMESTAMP NULL,
            ADD COLUMN rejected_at TIMESTAMP NULL";
    
    if (!$conn->query($sql)) {
        throw new Exception("Error adding columns: " . $conn->error);
    }
    
    echo "Columns added successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
