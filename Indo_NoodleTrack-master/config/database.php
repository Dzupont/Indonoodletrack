<?php
// Load environment variables
$dotenv = __DIR__ . '/../.env';
if (file_exists($dotenv)) {
    $lines = file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Database configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'indonoodle_track');

// Function to get database connection
function getDBConnection() {
    static $conn = null;
    
    try {
        // Try multiple connection attempts with different configurations
        $attempts = [
            ['host' => DB_HOST, 'port' => 3306],
            ['host' => '127.0.0.1', 'port' => 3306],
            ['host' => DB_HOST, 'port' => 3307],
            ['host' => '127.0.0.1', 'port' => 3307]
        ];

        foreach ($attempts as $attempt) {
            try {
                $conn = new mysqli(
                    $attempt['host'],
                    DB_USER,
                    DB_PASS,
                    DB_NAME,
                    $attempt['port']
                );
                
                if ($conn->connect_error) {
                    throw new Exception("Connection failed: " . $conn->connect_error);
                }
                
                $conn->set_charset("utf8");
                return $conn;
            } catch (Exception $e) {
                error_log("Attempt failed (" . $attempt['host'] . ":" . $attempt['port'] . "): " . $e->getMessage());
                continue;
            }
        }

        throw new Exception("All connection attempts failed");
        
    } catch (Exception $e) {
        error_log("Database connection error: " . $e->getMessage());
        throw $e;
    }
}

// Create tables if not exists
try {
    $conn = getDBConnection();
    
    // Users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        no_tlpn VARCHAR(20) NOT NULL,
        sandi VARCHAR(255) NOT NULL,
        role ENUM('gudang', 'manager', 'produksi') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    if (!$conn->query($sql)) {
        throw new Exception("Error creating users table: " . $conn->error);
    }

    // Activity logs table
    $sql = "CREATE TABLE IF NOT EXISTS activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        activity_type VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    if (!$conn->query($sql)) {
        throw new Exception("Error creating activity_logs table: " . $conn->error);
    }

    // Stocks table
    $sql = "CREATE TABLE IF NOT EXISTS stocks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        kode VARCHAR(20) NOT NULL UNIQUE,
        nama VARCHAR(100) NOT NULL,
        jenis VARCHAR(50) NOT NULL,
        satuan VARCHAR(20) NOT NULL,
        stok DECIMAL(10,2) NOT NULL DEFAULT 0,
        tanggal_expired DATE NULL,
        deskripsi TEXT,
        gambar VARCHAR(255),
        minimal_stok DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    if (!$conn->query($sql)) {
        throw new Exception("Error creating stocks table: " . $conn->error);
    }

    // Requests table
    $sql = "CREATE TABLE IF NOT EXISTS requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        material_id INT,
        quantity DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        requested_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (material_id) REFERENCES raw_materials(id) ON DELETE CASCADE,
        FOREIGN KEY (requested_by) REFERENCES users(id) ON DELETE CASCADE
    )";
    if (!$conn->query($sql)) {
        throw new Exception("Error creating requests table: " . $conn->error);
    }

    // Returns table
    $sql = "CREATE TABLE IF NOT EXISTS returns (
        id INT AUTO_INCREMENT PRIMARY KEY,
        material_id INT,
        quantity DECIMAL(10,2) NOT NULL,
        reason TEXT,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        returned_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (material_id) REFERENCES raw_materials(id) ON DELETE CASCADE,
        FOREIGN KEY (returned_by) REFERENCES users(id) ON DELETE CASCADE
    )";
    if (!$conn->query($sql)) {
        throw new Exception("Error creating returns table: " . $conn->error);
    }

    // Cart table
    $sql = "CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        bahan_id INT NOT NULL,
        quantity DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (bahan_id) REFERENCES stocks(id) ON DELETE CASCADE,
        UNIQUE KEY unique_cart_item (user_id, bahan_id)
    )";
    if (!$conn->query($sql)) {
        throw new Exception("Error creating returns table: " . $conn->error);
    }

} catch (Exception $e) {
    error_log("Database initialization error: " . $e->getMessage());
    throw $e;
}