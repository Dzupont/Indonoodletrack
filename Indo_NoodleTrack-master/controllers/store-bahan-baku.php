<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Check if user is logged in and has gudang role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gudang') {
    header('Location: ../login.php');
    exit();
}

$conn = getDBConnection();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get form data
$nama = $_POST['nama_bahanbaku'] ?? '';
$jenis = $_POST['jenis_bahanbaku'] ?? '';
$stok = $_POST['stok_bahanbaku'] ?? 0;
$tanggal_expired = $_POST['tanggal_expired'] ?? null;
$deskripsi = $_POST['deskripsi'] ?? '';
$satuan = $_POST['satuan'] ?? 'kg';
$minimal_stok = 0; // Default minimal stock

// Generate kode
$kode = uniqid('BB_', true);

// Handle image upload
$gambar = null;
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/../../public/images/bahan-baku/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_extension = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($file_extension, $allowed_extensions)) {
        $new_filename = $kode . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
            $gambar = 'images/bahan-baku/' . $new_filename;
        }
    }
}

// Insert into database
$query = "INSERT INTO stocks (kode, nama, jenis, stok, tanggal_expired, deskripsi, gambar, satuan, minimal_stok, created_at, updated_at) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

// Prepare and execute query
$stmt = $conn->prepare($query);
$stmt->bind_param("ssdsssdsd", $kode, $nama, $jenis, $stok, $tanggal_expired, $deskripsi, $gambar, $satuan, $minimal_stok);

if ($stmt->execute()) {
    // Log activity
    $activity_desc = "Menambah bahan baku: $nama ($kode)";
    $log_query = "
        INSERT INTO activity_logs (user_id, activity_type, description)
        VALUES ('$_SESSION[user_id]', 'create', '$activity_desc')
    ";
    $conn->query($log_query);
    
    // Redirect to stok-bahan-baku with success message
    header('Location: /IndoNoodleTrack/Indo_NoodleTrack-master/views/auth/Gudang/stok-bahan-baku.php?success=1');
    exit();
} else {
    // Redirect back with error message
    header('Location: /IndoNoodleTrack/Indo_NoodleTrack-master/views/auth/Gudang/tambah-bahan-baku.php?error=1');
    exit();
}

$conn->close();
