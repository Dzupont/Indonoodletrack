<?php
$rootPath = dirname(dirname(__FILE__));
require_once $rootPath . '/config/database.php';

// Get database connection
$conn = getDBConnection();

// Check if php_errors.log exists
$php_log_path = 'php_errors.log';
if (file_exists($php_log_path)) {
    echo "Reading PHP error log:\n";
    $lines = file($php_log_path);
    foreach ($lines as $line) {
        echo $line;
    }
} else {
    echo "PHP error log not found\n";
}

// Check database tables
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result) {
    echo "\nDatabase tables:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Tables_in_indonoodle_track'] . "\n";
    }
} else {
    echo "\nError checking tables: " . $conn->error . "\n";
}

// Check returns table structure
$sql = "DESCRIBE returns";
$result = $conn->query($sql);

if ($result) {
    echo "\nReturns table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " " . $row['Type'] . "\n";
    }
} else {
    echo "\nError checking returns table: " . $conn->error . "\n";
}

$conn->close();
?>
