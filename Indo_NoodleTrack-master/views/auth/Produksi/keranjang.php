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

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        th {
            background-color: #4a9bb1;
            color: white;
            padding: 12px;
            text-align: left;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        td {
            background-color: white;
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.95rem;
        }

        tr td:first-child {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        tr td:last-child {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#4A9AB7] text-white flex flex-col py-10 px-6 rounded-tr-3xl rounded-br-3xl">
        <div class="flex flex-col justify-between h-full">
            <div>
                <img src="/Indo_NoodleTrack-master/assets/images/logo.jpg" alt="Logo" class="w-16 h-16 mb-2 rounded-full">
                <span class="text-xl font-bold leading-5">indo<br>noodle<br>track.</span>
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
            <form id="requestForm" action="submit-request.php" method="POST">
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll" onclick="toggleAllCheckboxes(this)"></th>
                            <th>ID</th>
                            <th>Item</th>
                            <th>Kuantitas</th>
                            <th>Ketersediaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $conn = getDBConnection();
                        $stmt = $conn->prepare("SELECT c.*, s.nama, s.stok, s.satuan FROM cart c JOIN stocks s ON c.bahan_id = s.id WHERE c.user_id = ?");
                        $stmt->bind_param("i", $_SESSION['user_id']);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            echo '<tr><td colspan="6" class="text-center text-gray-400 py-8">Keranjang Kosong</td></tr>';
                        } else {
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td><input type="checkbox" name="selected_items[]" value="' . htmlspecialchars($row['id']) . '"></td>';
                                echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['nama']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['quantity']) . ' ' . htmlspecialchars($row['satuan']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['stok']) . ' ' . htmlspecialchars($row['satuan']) . '</td>';
                                echo '<td>';
                                echo '<button type="button" class="text-red-500 hover:text-red-700" title="Hapus dari keranjang" onclick="deleteItem(' . htmlspecialchars($row['id']) . ')">🗑</button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <div class="flex justify-end mt-6">
                    <button type="submit" form="requestForm" class="bg-[#4a9bb1] text-white px-8 py-2 rounded-full hover:bg-[#2e94a6] transition">Ajukan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notification -->
    <div id="notification" class="hidden fixed top-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg">
        <p id="notificationText" class="font-medium text-base"></p>
    </div>

    <?php
    if (isset($_GET['added']) && $_GET['added'] === '1') {
        echo '<script>
            showNotification("Barang berhasil ditambahkan ke keranjang");
        </script>';
    }
    
    if (isset($_GET['success']) && $_GET['success'] === '1') {
        echo '<script>
            showNotification("Item berhasil dihapus dari keranjang");
        </script>';
    }
    ?>

    <script>
        function showNotification(message) {
            const notification = document.getElementById('notification');
            const notificationText = document.getElementById('notificationText');
            notificationText.textContent = message;
            notification.classList.remove('hidden');
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 2000);
        }

        function deleteItem(cartId) {
            if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                showNotification('Menghapus item...');
                
                // Submit the form using JavaScript
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'remove-from-cart.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'cart_id';
                input.value = cartId;
                
                form.appendChild(input);
                document.body.appendChild(form);
                
                setTimeout(() => {
                    form.submit();
                }, 500);
            }
        }

        function toggleAllCheckboxes(source) {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="selected_items[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = source.checked;
            });
        }

        // Handle form submission
        document.getElementById('requestForm')?.addEventListener('submit', function(e) {
            const selectedCheckboxes = document.querySelectorAll('input[type="checkbox"][name="selected_items[]"]:checked');
            if (selectedCheckboxes.length === 0) {
                alert('Silakan pilih setidaknya satu item untuk diajukan!');
                e.preventDefault();
            }
        });

        // Show notification when page loads
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success') && urlParams.get('success') === '1') {
                const notification = document.getElementById('notification');
                notification.classList.remove('hidden');
                setTimeout(() => {
                    notification.classList.add('hidden');
                }, 2000);
            }
        });
    </script>
</body>
</html>
            </form>
        </div>
    </main>
</div>

    <!-- Notification -->
    <div id="notification" class="hidden fixed top-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg">
        <p class="font-medium text-base">Item berhasil dihapus dari keranjang!</p>
    </div>

    <?php
    if (isset($_GET['success']) && $_GET['success'] === '1') {
        echo '<script>
            const notification = document.getElementById("notification");
            notification.classList.remove("hidden");
            setTimeout(() => {
                notification.classList.add("hidden");
            }, 2000);
        </script>';
    }
    ?>

<script>
function toggleAllCheckboxes(source) {
    const checkboxes = document.querySelectorAll('input[type="checkbox"][name="selected_items[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
}

document.getElementById('requestForm')?.addEventListener('submit', function(e) {
    const selectedCheckboxes = document.querySelectorAll('input[type="checkbox"][name="selected_items[]"]:checked');
    if (selectedCheckboxes.length === 0) {
        alert('Silakan pilih setidaknya satu item untuk diajukan!');
        e.preventDefault();
    }
});

window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success') && urlParams.get('success') === '1') {
        const notification = document.getElementById('notification');
        notification.classList.remove('hidden');
        setTimeout(() => {
            notification.classList.add('hidden');
        }, 2000);
    }
});
</script>

</body>
</html>
