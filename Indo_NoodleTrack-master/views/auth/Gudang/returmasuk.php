<?php
// Get absolute path to project root
$rootPath = dirname(dirname(dirname(dirname(__FILE__))));
// Load database configuration
require_once $rootPath . '/config/database.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit();
}

// Get database connection
$conn = getDBConnection();

// Check if connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $material_id = $_POST['material_id'];
    $quantity = $_POST['quantity'];
    $reason = $_POST['reason'];
    $returned_by = $_SESSION['user_id'];
    $approved_by = null; // Will be set when approved
    $created_at = date('Y-m-d H:i:s');

    // Insert return record
    $sql = "INSERT INTO returns (material_id, quantity, reason, returned_by, approved_by, created_at) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$material_id, $quantity, $reason, $returned_by, $approved_by, $created_at])) {
        $_SESSION['success'] = "Retur berhasil ditambahkan";
        header('Location: returmasuk.php');
        exit();
    } else {
        $_SESSION['error'] = "Gagal menambahkan retur";
    }
}

// Fetch all returns
$sql = "SELECT r.*, m.name as material_name, u.username as returned_by_name 
        FROM returns r 
        LEFT JOIN raw_materials m ON r.material_id = m.id 
        LEFT JOIN users u ON r.returned_by = u.id 
        ORDER BY r.created_at DESC";
$result = $conn->query($sql);
$returns = array();
while ($row = $result->fetch_assoc()) {
    $returns[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retur Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
        }
    </style>
</head>
<body class="flex">

<aside class="bg-[#4A9AB7] w-60 min-h-screen py-10 px-6 text-white rounded-tr-3xl rounded-br-3xl">
    <div class="flex items-center justify-center mb-10">
        <img src="/Indo_NoodleTrack-master/assets/images/logo.jpg" alt="Logo" class="h-12 w-12" />
    </div>
    <nav class="flex flex-col space-y-6 text-base font-semibold">
        <a href="./dashboardgudang.php" class="flex items-center space-x-3 hover:text-gray-200">
            <i class="fas fa-home"></i><span>Dashboard</span>
        </a>
        <a href="./penerimaanpermintaanmasuk.php" class="flex items-center space-x-3 hover:text-gray-200">
            <i class="fas fa-file-invoice"></i><span>Permintaan Masuk</span>
        </a>
        <a href="./returmasuk.php" class="flex items-center space-x-3 text-[#FFE484]">
            <i class="fas fa-sync-alt"></i><span>Retur Masuk</span>
        </a>
        <a href="./monitoringgudang.php" class="flex items-center space-x-3 hover:text-gray-200">
            <i class="fas fa-cube"></i><span>Monitoring</span>
        </a>
        <a href="./stok-bahan-baku.php" class="flex items-center space-x-3 hover:text-gray-200">
            <i class="fas fa-box"></i><span>Stok Bahan Baku</span>
        </a>
        <a href="../../views/auth/logout.php" class="flex items-center space-x-3 hover:text-gray-200">
            <i class="fas fa-sign-out-alt"></i><span>Logout</span>
        </a>
    </nav>
</aside>

<main class="flex-1 bg-white px-10 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-[#4A9AB7]">Retur Masuk</h1>
        <button class="bg-[#4A9AB7] text-white px-4 py-2 rounded-lg shadow-md hover:bg-[#3a7c95]" data-bs-toggle="modal" data-bs-target="#addReturnModal">
            + Tambah Retur
        </button>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="bg-[#F4FBFF] p-6 rounded-lg shadow">
        <table class="w-full text-sm text-left text-gray-700">
            <thead class="text-xs text-gray-700 uppercase bg-[#D9F2FF]">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Material</th>
                    <th class="px-4 py-3">Jumlah</th>
                    <th class="px-4 py-3">Alasan</th>
                    <th class="px-4 py-3">Dikembalikan Oleh</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                <?php foreach ($returns as $index => $retur): ?>
                <tr class="border-b">
                    <td class="px-4 py-3"><?php echo $index + 1; ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($retur['material_name']); ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($retur['quantity']); ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($retur['reason']); ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($retur['returned_by_name']); ?></td>
                    <td class="px-4 py-3"><?php echo date('d/m/Y H:i', strtotime($retur['created_at'])); ?></td>
                    <td class="px-4 py-3">
                        <?php if ($retur['approved_by']): ?>
                            <span class="text-green-600 font-medium">Disetujui</span>
                        <?php else: ?>
                            <span class="text-yellow-600 font-medium">Menunggu</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <button class="text-blue-600 hover:underline" data-bs-toggle="modal" data-bs-target="#viewReturnModal" data-id="<?php echo $retur['id']; ?>">
                            Lihat
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Add Return Modal -->
<div class="modal fade" id="addReturnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Retur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="material_id" class="form-label">Material</label>
                        <select class="form-control" id="material_id" name="material_id" required>
                            <option value="">Pilih Material</option>
                            <?php
                            $sql = "SELECT id, nama FROM raw_materials ORDER BY nama";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['id'] . "'>" . $row['nama'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Alasan Retur</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Return Modal -->
<div class="modal fade" id="viewReturnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Retur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="returnDetails"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // View Return Details
    var viewModal = new bootstrap.Modal(document.getElementById('viewReturnModal'));
    var viewModalElement = document.getElementById('viewReturnModal');
    
    viewModalElement.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        
        fetch('get_return_details.php?id=' + id)
            .then(response => response.text())
            .then(html => {
                document.getElementById('returnDetails').innerHTML = html;
            });
    });
</script>
</body>
</html>
