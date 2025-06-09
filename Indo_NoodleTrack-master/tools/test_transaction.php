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
        $last_id = $conn->insert_id;
        echo "Last insert ID: " . $last_id . "\n";
        
        // Commit transaction
        $conn->commit();
        
        // Select the inserted record
        $sql = "SELECT * FROM returns WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $last_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "Selected data after commit:\n";
            echo "ID: " . $row['id'] . "\n";
            echo "Stock ID: " . $row['stock_id'] . "\n";
            echo "Quantity: " . $row['quantity'] . "\n";
            echo "Reason: " . $row['reason'] . "\n";
            echo "Status: " . $row['status'] . "\n";
            echo "Returned By: " . $row['returned_by'] . "\n";
            echo "Created At: " . $row['created_at'] . "\n\n";
        } else {
            echo "Error: Could not select inserted data after commit\n";
        }
    } else {
        echo "Error inserting test record: " . $conn->error . "\n";
        $conn->rollback();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    $conn->rollback();
}

$conn->close();
?>
