
<?php
session_start();

// Check if user is logged in and has produksi role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'produksi') {
    header('Location: ../../login.php');
    exit();
}

require_once '../../../config/database.php';

// Get database connection
$conn = getDBConnection();

// Get status counts
$statusCounts = [
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0
];

// Query to get status counts
$sql = "SELECT status, COUNT(*) as count 
        FROM requests 
        WHERE requested_by = ? 
        GROUP BY status";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $statusCounts[$row['status']] = $row['count'];
}
$stmt->close();

// Get recent requests
$sql = "SELECT r.*, m.name as bahan_baku, m.unit 
        FROM requests r 
        LEFT JOIN raw_materials m ON r.material_id = m.id 
        WHERE r.requested_by = ? 
        ORDER BY r.created_at DESC 
        LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$recentRequests = $stmt->get_result();
$stmt->close();

// Get recent returns
$sql = "SELECT r.*, s.nama as bahan_baku, s.satuan 
        FROM returns r 
        LEFT JOIN stocks s ON r.stock_id = s.id 
        WHERE r.returned_by = ? 
        ORDER BY r.created_at DESC 
        LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$recentReturns = $stmt->get_result();
$stmt->close();

// Get user data
$user_id = $_SESSION['user_id'];
$conn = getDBConnection();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Reset connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Bahan Baku - IndoNoodle Track</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
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
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f8fb;
            margin: 0;
        }
        .sidebar {
            width: 230px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #4a9bb1;
            color: white;
            padding: 25px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar h4 {
            font-weight: bold;
            font-size: 1.4rem;
            margin-bottom: 2rem;
        }
        .nav-link {
            color: white;
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 10px;
            transition: 0.3s;
            display: flex;
            align-items: center;
        }
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }
        .nav-link i {
            margin-right: 10px;
        }
        .content {
            margin-left: 250px;
            padding: 30px;
        }
        .tabs {
            margin-bottom: 30px;
        }
        .tabs button {
            border: none;
            background-color: #e3f2f9;
            margin-right: 10px;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            color: #4a9bb1;
        }
        .tabs button.active {
            background-color: #4a9bb1;
            color: white;
        }
        .product-card {
            background-color: white;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .product-card:hover {
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        .product-card img {
            height: 100px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        .product-name {
            font-weight: bold;
            color: #4a9bb1;
        }
        .overlay {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #2e94a6;
            color: white;
            padding: 30px 50px;
            border-radius: 15px;
            text-align: center;
            display: none;
            z-index: 9999;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div>
            <h4>indo noodle track.</h4>
            <a class="nav-link" href="dashboardproduksi.php"><i class="fas fa-home"></i> Dashboard</a>
            <a class="nav-link" href="permintaanmasuk.php"><i class="fas fa-shopping-cart"></i> Permintaan Bahan Baku</a>
            <a class="nav-link" href="returbahanbaku.php"><i class="fas fa-undo"></i> Retur Bahan Baku</a>
            <a class="nav-link" href="monitor.php"><i class="fas fa-chart-line"></i> Monitoring</a>
            <a class="nav-link" href="riwayat.php"><i class="fas fa-history"></i> Riwayat</a>
            <a class="nav-link" href="../../../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="content">
        <?php if (isset($_GET['success']) && $_GET['success'] === '1' && isset($_GET['message'])): ?>
            <div class="success-message show">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="color:#4a9bb1;">Dashboard</h2>
            <div class="d-flex align-items-center">
                <a href="keranjang.php" class="me-3 text-[#4a9bb1] hover:text-[#2e94a6]">
                    <i class="fas fa-shopping-cart text-2xl"></i>
                </a>
                <div class="me-3 text-end">
                    <strong>Divisi Produksi</strong><br>
                    User Id : <?php echo htmlspecialchars($_SESSION['user_id']); ?>
                </div>
                <img src="https://via.placeholder.com/40" class="rounded-circle" alt="User Image">
            </div>
        </div>
        <div class="tabs" id="categoryTabs">
            <button class="active" data-category="all">Semua</button>
            <button data-category="bahan_utama">Bahan Baku Utama</button>
            <button data-category="bahan_tambahan">Bahan Tambahan</button>
            <button data-category="bumbu">Bumbu & Perisa</button>
            <button data-category="kemasan">Perlengkapan Kemasan</button>
            <button data-category="penolong">Bahan Penolong Lain</button>
        </div>
        
        <div class="row" id="productsContainer">
            <?php
            // Query to get all stocks with their categories
            $sql = "SELECT s.*, 
                    CASE 
                        WHEN s.jenis = 'bahan_utama' THEN 'Bahan Baku Utama'
                        WHEN s.jenis = 'bahan_tambahan' THEN 'Bahan Tambahan'
                        WHEN s.jenis = 'bumbu' THEN 'Bumbu & Perisa'
                        WHEN s.jenis = 'kemasan' THEN 'Perlengkapan Kemasan'
                        ELSE 'Bahan Penolong Lain'
                    END as category_name,
                    s.gambar as image_path
                    FROM stocks s
                    ORDER BY s.jenis, s.nama";
            
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imagePath = $row['image_path'] ? '../../public/images/bahan-baku/' . $row['image_path'] : 'https://cdn-icons-png.flaticon.com/512/2909/2909767.png';
                    ?>
                    <div class="col-md-3 mb-4" data-category="<?php echo htmlspecialchars($row['jenis']); ?>">
                        <div class="product-card">
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($row['nama']); ?>">
                            <div class="product-name"><?php echo htmlspecialchars($row['nama']); ?></div>
                            <p><?php echo htmlspecialchars($row['satuan']); ?></p>
                            <p>Stok: <?php echo htmlspecialchars($row['stok']); ?></p>
                            <?php if ($row['tanggal_expired']): ?>
                            <p class="text-sm text-gray-500">Exp: <?php echo htmlspecialchars($row['tanggal_expired']); ?></p>
                            <?php endif; ?>
                            <form action="add-to-cart.php" method="POST" class="add-to-cart-form">
                                <input type="hidden" name="bahan_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <input type="hidden" name="bahan_nama" value="<?php echo htmlspecialchars($row['nama']); ?>">
                                <input type="hidden" name="bahan_satuan" value="<?php echo htmlspecialchars($row['satuan']); ?>">
                                <div class="flex items-center gap-2">
                                    <input type="number" name="quantity" value="1" min="1" class="form-control w-24" required>
                                    <button type="submit" class="btn btn-sm btn-primary">Tambah</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-12"><p class="text-center text-gray-500">Tidak ada bahan baku yang tersedia.</p></div>';
            }
            ?>
        </div>
    </div>
    <div class="overlay" id="confirmationOverlay">
        <i class="fas fa-check-circle fa-2x mb-2"></i>
        <p>Produk Telah Ditambahkan Ke Keranjang</p>
    </div>
    <script>
        // Category filtering
        document.querySelectorAll('#categoryTabs button').forEach(button => {
            button.addEventListener('click', function() {
                const category = this.dataset.category;
                document.querySelectorAll('#categoryTabs button').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                const products = document.querySelectorAll('#productsContainer > div');
                products.forEach(product => {
                    if (category === 'all' || product.dataset.category === category) {
                        product.style.display = 'block';
                    } else {
                        product.style.display = 'none';
                    }
                });
            });
        });

        // Add to cart form handling
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Submit the form normally
                form.submit();
            });
        });
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const overlay = document.getElementById('confirmationOverlay');
                        overlay.innerHTML = `
                            <i class="fas fa-check-circle fa-2x mb-2 text-green-500"></i>
                            <p class="text-green-700">Barang berhasil ditambahkan ke keranjang!</p>
                        `;
                        overlay.style.display = 'block';
                        setTimeout(() => {
                            overlay.style.display = 'none';
                        }, 1500);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menambahkan ke keranjang');
                });
            });
        });
    </script>
</body>
</html>
