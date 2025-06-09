<?php
// Set relative path
$relative_path = '../../../';
require_once $relative_path . 'config/session.php';
require_once $relative_path . 'config/database.php';
require_once $relative_path . 'config/base_url.php';

// Set base URL
$base_url = getBaseUrl();

// Check session and redirect if not valid
if (!requireLogin(false) || getCurrentUserRole() !== 'produksi') {
    header('Location: ' . $base_url . 'views/auth/login.php');
    exit();
}

// Get database connection
$conn = getDBConnection();

// Get user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) {
    $user = ['username' => 'User']; // Default value if user not found
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
        // Update material stock
        $update_stock_sql = "UPDATE raw_materials SET stock = stock + ? WHERE id = ?";
        $stock_stmt = $conn->prepare($update_stock_sql);
        $stock_stmt->bind_param("di", $quantity, $material_id);
        $stock_stmt->execute();
        
        $_SESSION['success'] = "Retur berhasil ditambahkan dan stok diperbarui";
        header('Location: ' . $base_url . 'views/auth/Produksi/returbahanbaku.php');
        exit();
    } else {
        $_SESSION['error'] = "Gagal menambahkan retur";
    }
}

// Fetch returns
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

$materials_sql = "SELECT id, name FROM raw_materials ORDER BY name";
$materials_result = $conn->query($materials_sql);
$materials = [];
while ($row = $materials_result->fetch_assoc()) {
    $materials[] = $row;
}
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
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f8fb;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #4a9bb1;
            color: white;
            padding: 20px;
        }
        .sidebar h4 {
            font-weight: bold;
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }
        .sidebar .nav-link {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 5px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .main-content {
            margin-left: 270px;
            padding: 30px;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .top-bar .title {
            color: #4a9bb1;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .profile-group {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .profile-group .profile-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .profile-group .profile-info h6 {
            margin: 0;
            font-size: 0.9rem;
        }
        .profile-group .profile-info p {
            margin: 0;
            font-size: 0.8rem;
            color: #666;
        }
        .profile-group img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .table-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .table {
            width: 100%;
            margin-bottom: 0;
        }
        .table th {
            background-color: #f8f9fa;
            border: none;
        }
        .table td {
            border: none;
            padding: 15px;
        }
        .table tr:hover {
            background-color: #f8f9fa;
        }
        .btn-primary {
            background-color: #4a9bb1;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #3a7c95;
        }
        .modal-content {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: none;
        }
        .modal-title {
            color: #4a9bb1;
        }
        .modal-footer {
            border-top: none;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: none;
        }
        .success-message.show {
            display: block;
        }
        .error-message {
            background-color: #f8d7da;
            color: #842029;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: none;
        }
        .error-message.show {
            display: block;
        }
    </style>
</head>
<body class="flex">

<div class="d-flex">
    <div class="sidebar">
        <div>
            <h4>indo noodle track.</h4>
            <a class="nav-link" href="<?php echo url('views/auth/Produksi/dashboardproduksi.php'); ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a class="nav-link" href="<?php echo url('views/auth/Produksi/permintaanmasuk.php'); ?>">
                <i class="fas fa-shopping-cart"></i> Permintaan Bahan Baku
            </a>
            <a class="nav-link active" href="<?php echo url('views/auth/Produksi/returbahanbaku.php'); ?>">
                <i class="fas fa-undo"></i> Retur Bahan Baku
            </a>
            <a class="nav-link" href="<?php echo url('views/auth/Produksi/monitor.php'); ?>">
                <i class="fas fa-chart-line"></i> Monitoring
            </a>
            <a class="nav-link" href="<?php echo url('views/auth/Produksi/riwayat.php'); ?>">
                <i class="fas fa-history"></i> Riwayat
            </a>
            <a class="nav-link" href="<?php echo url('views/auth/logout.php'); ?>">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="title">Retur Bahan Baku</div>
            <div class="profile-group">
                <div class="profile-info">
                    <h6><?php echo htmlspecialchars($user['username']); ?></h6>
                    <p>Divisi Produksi</p>
                </div>
                <img src="https://via.placeholder.com/40" alt="Profile">
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message show">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message show">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 style="color:#4a9bb1;">Retur Bahan Baku</h2>
                <button class="btn-primary" data-bs-toggle="modal" data-bs-target="#addReturnModal">
                    <i class="fas fa-plus me-2"></i> Tambah Retur
                </button>
            </div>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message show">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <div class="table-container">

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
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                document.getElementById('returnDetails').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('returnDetails').innerHTML = '<div class="text-red-600">Error: ' + error.message + '</div>';
            });
    });
</script>
</body>
</html>
