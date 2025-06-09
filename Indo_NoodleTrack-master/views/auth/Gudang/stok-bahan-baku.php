<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gudang') {
    header('Location: ../../login.php');
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$user_id = $_SESSION['user_id'];
$conn = getDBConnection();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Stok Bahan Baku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet"/>
</head>
<body class="bg-white font-poppins">
<div class="flex min-h-screen max-h-screen overflow-hidden">
    <aside class="w-64 bg-[#4A9AB7] text-white flex flex-col py-10 px-6 rounded-tr-3xl rounded-br-3xl">
        <div class="flex flex-col items-center mb-12">
            <img src="/Indo_NoodleTrack-master/assets/images/logo.jpg" alt="Logo" class="w-16 h-16 mb-2 rounded-full">
            <span class="text-xl font-bold">indo<br>noodle<br>track.</span>
        </div>
        <nav class="flex flex-col gap-4 text-sm font-semibold">
            <a href="./dashboardgudang.php" class="flex items-center gap-2"><i class="fas fa-home"></i> Dashboard</a>
            <a href="./penerimaanpermintaanmasuk.php" class="flex items-center gap-2"><i class="fas fa-file-invoice"></i> Permintaan Masuk</a>
            <a href="./returmasuk.php" class="flex items-center gap-2"><i class="fas fa-sync-alt"></i> Retur Masuk</a>
            <a href="./monitoringgudang.php" class="flex items-center gap-2"><i class="fas fa-cube"></i> Monitoring</a>
            <a href="./stok-bahan-baku.php" class="flex items-center gap-2 active"><i class="fas fa-box"></i> Stok</a>
            <a href="../../auth/login.php" class="flex items-center gap-2 mt-4"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col bg-white rounded-tl-3xl rounded-bl-3xl overflow-hidden">
        <div class="flex-1 overflow-y-auto p-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Success/Error Messages -->
                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                        <strong class="font-bold">Berhasil!</strong>
                        <span class="block sm:inline">Bahan baku berhasil dihapus.</span>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">Terjadi kesalahan saat menghapus bahan baku.</span>
                    </div>
                <?php endif; ?>

                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Bahan Baku Utama</h2>
                    <a href="./tambah-bahan-baku.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i>Tambah Bahan Baku
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM stocks ORDER BY nama");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <div class="bg-white rounded-lg shadow-md p-4 relative group">
                        <div class="absolute inset-0 bg-gradient-to-br from-transparent to-gray-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <img src="<?php echo $row['gambar'] ? '../../public/' . $row['gambar'] : 'https://cdn-icons-png.flaticon.com/512/2909/2909767.png'; ?>" 
                             alt="<?php echo htmlspecialchars($row['nama']); ?>" 
                             class="w-32 h-32 mx-auto mb-4 rounded-lg object-cover">
                        <h3 class="text-lg font-semibold text-center mb-2"><?php echo htmlspecialchars($row['nama']); ?></h3>
                        <p class="text-gray-600 text-center text-sm">Stok: <?php echo htmlspecialchars($row['stok']); ?> <?php echo htmlspecialchars($row['satuan']); ?></p>
                        <?php if ($row['tanggal_expired']): ?>
                        <p class="text-gray-500 text-center text-xs">Exp: <?php echo htmlspecialchars($row['tanggal_expired']); ?></p>
                        <?php endif; ?>

                        <div class="absolute top-2 right-2 z-20">
                            <div class="relative inline-block text-left">
                                <button onclick="toggleDropdown(event, <?php echo $row['id']; ?>)" class="text-gray-600 hover:text-black focus:outline-none">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div id="dropdown-<?php echo $row['id']; ?>" class="dropdown-menu hidden origin-top-right absolute right-0 mt-2 w-32 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-30">
                                     <a href="edit-bahan-baku.php?id=<?php echo $row['id']; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                         <i class="fas fa-edit mr-2"></i> Edit
                                     </a>
                                     <form method="POST" action="delete-bahan-baku.php" class="w-full">
                                         <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                         <button type="submit" class="w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center" onclick="return confirm('Apakah Anda yakin ingin menghapus bahan baku ini?');">
                                             <i class="fas fa-trash mr-2"></i> Hapus
                                         </button>
                                     </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h3 class="text-lg font-semibold mb-4">Konfirmasi Hapus</h3>
        <p id="deleteMessage" class="mb-4"></p>
        <div class="flex justify-end space-x-2">
            <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
                <i class="fas fa-times mr-2"></i>Batal
            </button>
            <button id="deleteConfirm" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                <i class="fas fa-trash mr-2"></i>Hapus
            </button>
        </div>
    </div>
</div>

<script>
    function toggleDropdown(event, id) {
        event.stopPropagation();
        document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        const dropdown = document.getElementById('dropdown-' + id);
        dropdown.classList.toggle('hidden');
    }

    document.addEventListener('click', function () {
        document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
    });

    function confirmDelete(id, nama) {
        document.getElementById('deleteMessage').textContent = 'Apakah Anda yakin ingin menghapus bahan baku: ' + nama + '?';
        document.getElementById('deleteConfirm').onclick = function() {
            window.location.href = '?action=delete&id=' + id;
        };
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>
</body>
</html>
