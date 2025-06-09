<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/session.php';
require_once __DIR__ . '/../../../config/base_url.php';

// Check login and role
checkLoginAndRedirect();
if (getCurrentUserRole() !== 'gudang') {
    header('Location: ' . getBaseUrl() . 'views/auth/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Masuk - Gudang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
</head>

<body class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#4A9AB7] text-white flex flex-col py-10 px-6 rounded-tr-[40px] rounded-br-[40px]">
        <div class="flex flex-col items-center mb-12">
            <img src="/Indo_NoodleTrack-master/assets/images/logo.jpg" alt="Logo" class="w-16 h-16 mb-2 rounded-full">
            <span class="text-xl font-bold leading-5 text-center">ind0<br>noodle<br>track.</span>
        </div>
        <nav class="flex flex-col gap-4 text-sm font-semibold">
            <a href="dashboardgudang.php" class="flex items-center gap-2 hover:bg-white/10 px-4 py-2 rounded-lg">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="penerimaanpermintaanmasuk.php" class="flex items-center gap-2 bg-white text-[#4A9AB7] px-4 py-2 rounded-lg shadow active">
                <i class="fas fa-file-invoice"></i> Permintaan Masuk
            </a>
            <a href="returmasuk.php" class="flex items-center gap-2 hover:bg-white/10 px-4 py-2 rounded-lg">
                <i class="fas fa-sync-alt"></i> Retur Masuk
            </a>
            <a href="monitoringgudang.php" class="flex items-center gap-2 hover:bg-white/10 px-4 py-2 rounded-lg">
                <i class="fas fa-cube"></i> Monitoring
            </a>
            <a href="stok-bahan-baku.php" class="flex items-center gap-2 hover:bg-white/10 px-4 py-2 rounded-lg">
                <i class="fas fa-box"></i> Stok
            </a>
            <a href="../../auth/login.php" class="flex items-center gap-2 mt-6 bg-white text-[#4A9AB7] px-4 py-2 rounded-lg shadow hover:opacity-90">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </aside>

<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/session.php';
require_once __DIR__ . '/../../../config/base_url.php';

// Check login and role
checkLoginAndRedirect();
if (getCurrentUserRole() !== 'gudang') {
    header('Location: ' . getBaseUrl() . 'views/auth/login.php');
    exit();
}

// Get database connection
$conn = getDBConnection();

// Get total pending requests
$stmt = $conn->prepare("SELECT COUNT(*) as total_pending FROM requests WHERE status = 'pending'");
$stmt->execute();
$total_pending = $stmt->get_result()->fetch_assoc()['total_pending'];

// Get requests with details
$stmt = $conn->prepare("SELECT r.*, u.username as user_name FROM requests r JOIN users u ON r.requested_by = u.id WHERE r.status != 'completed' ORDER BY r.created_at DESC");
$stmt->execute();
$requests = $stmt->get_result();
?>

    <!-- Main Content -->
    <div class="flex-1 p-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-[#2e94a6]">Permintaan Masuk</h1>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-semibold">Divisi Gudang</p>
                    <p class="text-xs text-gray-500">User: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                </div>
                <img src="https://via.placeholder.com/40" alt="User" class="w-10 h-10 rounded-full border-2 border-gray-300">
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white border border-[#e0f2f1] p-6 rounded-2xl shadow">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Total Permintaan</p>
                        <p class="text-2xl font-bold text-[#2e94a6]"><?php echo $total_pending; ?></p>
                    </div>
                    <div class="p-3 bg-[#e0f2f1] rounded-full">
                        <i class="fas fa-file-invoice text-[#2e94a6] text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="flex justify-between items-center gap-4 bg-[#f0fdfc] p-4 rounded-lg mb-6 shadow-sm">
            <form id="searchForm" class="flex-1">
                <input type="text" id="searchInput" name="search" placeholder="🔍 Cari permintaan..." class="w-full p-2 rounded border border-gray-300">
            </form>
            <select id="statusFilter" class="p-2 rounded border border-gray-300">
                <option value="">🔍 Semua Status</option>
                <option value="pending">⏳ Pending</option>
                <option value="approved">✅ Disetujui</option>
                <option value="rejected">❌ Ditolak</option>
            </select>
        </div>

        <!-- Requests Table -->
        <div class="overflow-x-auto bg-white rounded-2xl border-2 border-[#e0f2f1] shadow">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-[#4ac0c6] text-white">
                        <th class="text-left p-4">ID Order</th>
                        <th class="text-left p-4">Total Bahan</th>
                        <th class="text-left p-4">Tanggal</th>
                        <th class="text-left p-4">Detail</th>
                        <th class="text-left p-4">Status</th>
                        <th class="text-left p-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($request = $requests->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4">#<?php echo htmlspecialchars($request['id']); ?></td>
                        <td class="p-4">
                            <?php
                            $stmt = $conn->prepare("SELECT COUNT(*) as total_items FROM request_items WHERE request_id = ?");
                            $stmt->bind_param("i", $request['id']);
                            $stmt->execute();
                            $total_items = $stmt->get_result()->fetch_assoc()['total_items'];
                            echo htmlspecialchars($total_items);
                            ?>
                        </td>
                        <td class="p-4"><?php echo date('d/m/Y', strtotime($request['created_at'])); ?></td>
                        <td class="p-4">
                            <button onclick="showDetail(<?php echo htmlspecialchars($request['id']); ?>)" class="text-blue-500 hover:text-blue-700">
                                Lihat Detail
                            </button>
                        </td>
                        <td class="p-4">
                            <span class="status-badge status-<?php echo htmlspecialchars($request['status']); ?>">
                                <?php 
                                switch($request['status']) {
                                    case 'pending': echo 'Pending'; break;
                                    case 'approved': echo 'Disetujui'; break;
                                    case 'rejected': echo 'Ditolak'; break;
                                    default: echo 'Unknown';
                                }
                                ?>
                            </span>
                        </td>
                        <td class="p-4">
                            <?php if ($request['status'] === 'pending'): ?>
                            <div class="flex gap-2">
                                <button onclick="approveRequest(<?php echo htmlspecialchars($request['id']); ?>)" class="text-green-500 hover:text-green-700">
                                    <i class="fas fa-check"></i> Setujui
                                </button>
                                <button onclick="rejectRequest(<?php echo htmlspecialchars($request['id']); ?>)" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Detail Modal -->
        <div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 max-w-2xl w-full">
                <h2 class="text-xl font-bold mb-4">Detail Permintaan</h2>
                <div id="detailContent" class="space-y-4">
                    <!-- Detail content will be populated by JavaScript -->
                </div>
                <div class="flex justify-end mt-4">
                    <button onclick="closeDetail()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .status-badge {
            font-size: 0.875rem;
            border-radius: 12px;
            padding: 4px 10px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #858796;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>

    <script>
        // Show detail modal
        function showDetail(requestId) {
            const modal = document.getElementById('detailModal');
            modal.classList.remove('hidden');
            
            // Fetch request details
            fetch(`get-request-details.php?id=${requestId}`)
                .then(response => response.json())
                .then(data => {
                    const detailContent = document.getElementById('detailContent');
                    detailContent.innerHTML = `
                        <div class="space-y-2">
                            <div>
                                <strong>Permintaan oleh:</strong> ${data.user_name}
                            </div>
                            <div>
                                <strong>Tanggal:</strong> ${data.created_at}
                            </div>
                            <div class="border-t border-b py-2">
                                <strong>Daftar Bahan Baku:</strong>
                            </div>
                            ${data.items.map(item => `
                                <div class="flex justify-between border-b py-2">
                                    <div>
                                        <strong>${item.nama}</strong><br>
                                        ${item.quantity} ${item.satuan}
                                    </div>
                                    <div>
                                        <span class="status-badge status-${item.status}">${item.status}</span>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mengambil detail permintaan');
                });
        }

        // Close detail modal
        function closeDetail() {
            const modal = document.getElementById('detailModal');
            modal.classList.add('hidden');
        }

        // Approve request
        function approveRequest(requestId) {
            if (confirm('Apakah Anda yakin ingin menyetujui permintaan ini?')) {
                fetch('approve-request.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `request_id=${requestId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Permintaan berhasil disetujui');
                        window.location.href = data.redirect;
                    } else {
                        alert('Gagal menyetujui permintaan: ' + data.message);
                        window.location.href = data.redirect;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyetujui permintaan');
                });
            }
        }

        // Reject request
        function rejectRequest(requestId) {
            if (confirm('Apakah Anda yakin ingin menolak permintaan ini?')) {
                fetch('reject-request.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `request_id=${requestId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Permintaan berhasil ditolak');
                        window.location.href = data.redirect;
                    } else {
                        alert('Gagal menolak permintaan: ' + data.message);
                        window.location.href = data.redirect;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menolak permintaan');
                });
            }
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const id = row.querySelector('td:first-child').textContent.toLowerCase();
                const date = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const status = row.querySelector('.status-badge').textContent.toLowerCase();
                
                if (id.includes(searchTerm) || date.includes(searchTerm) || status.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Filter by status
        document.getElementById('statusFilter').addEventListener('change', function() {
            const status = this.value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const statusBadge = row.querySelector('.status-badge');
                if (status === '' || statusBadge.classList.contains(`status-${status}`)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>
