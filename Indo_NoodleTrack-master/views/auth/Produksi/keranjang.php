<?php
session_start();
require_once '../../../config/database.php';

// Check login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'produksi') {
    header('Location: ../../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Permintaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
            text-decoration: none;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .nav-link i {
            margin-right: 10px;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#4A9AB7] text-white flex flex-col py-10 px-6 rounded-tr-3xl rounded-br-3xl">
        <div class="flex flex-col justify-between h-full">
            <div>
                <img src="/Indo_NoodleTrack-master/assets/images/logo.jpg" alt="Logo" class="w-16 h-16 mb-2 rounded-full">
                <span class="text-xl font-bold">indo<br>noodle<br>track.</span>
            </div>
            <nav class="flex flex-col gap-4 text-sm font-semibold mt-6">
                <a href="dashboardproduksi.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
                <a href="permintaanmasuk.php" class="nav-link"><i class="fas fa-inbox"></i> Permintaan Bahan Baku</a>
                <a href="returbahanbaku.php" class="nav-link"><i class="fas fa-undo"></i> Retur Bahan Baku</a>
                <a href="monitor.php" class="nav-link"><i class="fas fa-eye"></i> Monitoring</a>
                <a href="riwayat.php" class="nav-link"><i class="fas fa-history"></i> Riwayat</a>
            </nav>
            <a href="../../../views/auth/logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col bg-white rounded-tl-3xl rounded-bl-3xl overflow-hidden">
        <div class="flex justify-between items-center p-8 border-b border-gray-100">
            <h2 class="text-2xl font-bold text-[#4a9bb1]">Keranjang Permintaan</h2>
            <div class="flex items-center space-x-4">
                <div class="text-right text-sm">
                    <strong>Divisi Produksi</strong><br>
                    User Id : <?php echo htmlspecialchars($_SESSION['user_id']); ?>
                </div>
                <img src="https://via.placeholder.com/40" class="rounded-full" alt="User Image">
            </div>
        </div>

        <!-- Table -->
        <div class="p-8">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item</th>
                            <th>Kuantitas</th>
                            <th>Ketersediaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Get database connection
                        $conn = getDBConnection();
                        
                        // Get cart items for current user
                        $stmt = $conn->prepare("SELECT c.*, s.nama, s.stok, s.satuan 
                                               FROM cart c 
                                               JOIN stocks s ON c.bahan_id = s.id 
                                               WHERE c.user_id = ?");
                        $stmt->bind_param("i", $_SESSION['user_id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows === 0) {
                            echo '<tr><td colspan="5" class="text-center py-6 text-gray-400">Keranjang Kosong</td></tr>';
                        } else {
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr class="border-b border-gray-100">';
                                echo '<td class="py-3 px-6">' . htmlspecialchars($row['id']) . '</td>';
                                echo '<td class="py-3 px-6">' . htmlspecialchars($row['nama']) . '</td>';
                                echo '<td class="py-3 px-6">' . htmlspecialchars($row['quantity']) . ' ' . htmlspecialchars($row['satuan']) . '</td>';
                                echo '<td class="py-3 px-6">' . htmlspecialchars($row['stok']) . ' ' . htmlspecialchars($row['satuan']) . '</td>';
                                echo '<td class="py-3 px-6">';
                                echo '<form action="remove-from-cart.php" method="POST" class="inline">';
                                echo '<input type="hidden" name="cart_id" value="' . htmlspecialchars($row['id']) . '">';
                                echo '<button type="submit" class="text-red-500 hover:text-red-700">🗑</button>';
                                echo '</form>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Button -->
            <div class="mt-6 flex justify-end">
                <button id="checkoutBtn" class="bg-gray-400 text-white px-6 py-2 rounded-full hover:bg-gray-500 transition">Ajukan</button>
            </div>
        </div>
    </main>
</div>

<!-- Notification -->
<div id="pageNotification" class="hidden fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-[#00BCD4] text-white p-6 rounded-lg text-center z-50 w-[260px] shadow-md">
    <div class="flex justify-center mb-3">
        <div class="w-10 h-10 rounded-full border-2 border-white flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="white">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
    </div>
    <p class="font-medium text-base">Permintaan Berhasil Diajukan</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cart = JSON.parse(localStorage.getItem('indoNoodleCart')) || [];
    const tbody = document.getElementById('cartItemsList');
    tbody.innerHTML = '';

    if (cart.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-6 text-gray-400">Keranjang Permintaan Kosong</td>
            </tr>`;
    } else {
        cart.forEach((item, index) => {
            const row = document.createElement('tr');
            row.className = 'border-b border-gray-100';
            row.innerHTML = `
                <td class="py-3 px-6">${item.id || '-'}</td>
                <td class="py-3 px-6">${item.nama || '-'}</td>
                <td class="py-3 px-6">${item.jumlah || 0} Kg</td>
                <td class="py-3 px-6">${item.stok || 0} Kg</td>
                <td class="py-3 px-6">
                    <button onclick="removeItem(${index})" class="text-red-500 hover:text-red-700">
                        🗑
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    document.getElementById('checkoutBtn').addEventListener('click', () => {
        document.getElementById('pageNotification').classList.remove('hidden');
        setTimeout(() => {
            document.getElementById('pageNotification').classList.add('hidden');
        }, 2000);
    });
});

function removeItem(index) {
    const cart = JSON.parse(localStorage.getItem('indoNoodleCart')) || [];
    cart.splice(index, 1);
    localStorage.setItem('indoNoodleCart', JSON.stringify(cart));
    location.reload();
}
</script>

</body>
</html>
