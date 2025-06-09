<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/base_url.php';

// Check if user is logged in and has gudang role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gudang') {
    header('Location: ' . getBaseUrl() . 'views/auth/login.php');
    exit();
}

$conn = getDBConnection();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Validate input
$errors = [];

// Get form data
$nama = trim($_POST['nama_bahanbaku'] ?? '');
$jenis = trim($_POST['jenis_bahanbaku'] ?? '');
$stok = floatval($_POST['stok_bahanbaku'] ?? 0);
$tanggal_expired = $_POST['tanggal_expired'] ?? null;
$deskripsi = trim($_POST['deskripsi'] ?? '');
$satuan = $_POST['satuan'] ?? 'kg';
$minimal_stok = floatval($_POST['minimal_stok'] ?? 0);

// Validate required fields
if (empty($nama)) {
    $errors[] = "Nama bahan baku wajib diisi";
}
if (empty($jenis)) {
    $errors[] = "Jenis bahan baku wajib dipilih";
}
if (empty($satuan)) {
    $errors[] = "Satuan wajib dipilih";
}

// Validate numbers
if ($stok < 0) {
    $errors[] = "Stok tidak boleh negatif";
}
if ($minimal_stok < 0) {
    $errors[] = "Minimal stok tidak boleh negatif";
}

// Generate kode
$kode = uniqid('BB_', true);

// Handle image upload
$gambar = null;
if (isset($_FILES['gambar'])) {
    if ($_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
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
}

// If there are errors, redirect back with error messages
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_data'] = [
        'nama_bahanbaku' => $nama,
        'jenis_bahanbaku' => $jenis,
        'stok_bahanbaku' => $stok,
        'tanggal_expired' => $tanggal_expired,
        'deskripsi' => $deskripsi,
        'satuan' => $satuan,
        'minimal_stok' => $minimal_stok
    ];
    header('Location: ' . getBaseUrl() . 'views/auth/Gudang/tambah-bahan-baku.php');
    exit();
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
    $_SESSION['success'] = "Bahan baku berhasil ditambahkan";
    header('Location: ' . getBaseUrl() . 'views/auth/Gudang/stok-bahan-baku.php');
    exit();
} else {
    // Redirect back with error message
    $_SESSION['error'] = "Gagal menambahkan bahan baku: " . $stmt->error;
    header('Location: ' . getBaseUrl() . 'views/auth/Gudang/tambah-bahan-baku.php');
    exit();
}

$conn->close();
