<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

$conn = getDBConnection();

// Disable autocommit
$conn->autocommit(FALSE);

try {
    // Insert a return record
    $sql = "INSERT INTO returns (stock_id, quantity, reason, status, returned_by, approved_by, created_at) 
            VALUES (1, 10, 'Test retur', 'pending', 3, NULL, NOW())";
    
    if ($conn->query($sql) === TRUE) {
        echo "Record inserted successfully\n";
        echo "Last insert ID: " . $conn->insert_id . "\n";
        
        // Commit transaction
        $conn->commit();
        echo "Transaction committed\n";
    } else {
        echo "Error: " . $sql . "\n" . $conn->error . "\n";
        // Rollback in case there is any error
        $conn->rollback();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    // Rollback in case there is any error
    $conn->rollback();
}

$conn->close();
?>
