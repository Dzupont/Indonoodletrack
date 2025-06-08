<?php
session_start();
require_once '../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit();
}

// Get return ID from URL parameter
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Fetch return details
    $sql = "SELECT r.*, m.nama as material_name, u.username as returned_by_name, 
                   a.username as approved_by_name
            FROM returns r 
            LEFT JOIN material m ON r.material_id = m.id 
            LEFT JOIN users u ON r.returned_by = u.id 
            LEFT JOIN users a ON r.approved_by = a.id 
            WHERE r.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $return = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($return) {
        ?>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>Material</th>
                    <td><?php echo htmlspecialchars($return['material_name']); ?></td>
                </tr>
                <tr>
                    <th>Jumlah</th>
                    <td><?php echo htmlspecialchars($return['quantity']); ?></td>
                </tr>
                <tr>
                    <th>Alasan</th>
                    <td><?php echo nl2br(htmlspecialchars($return['reason'])); ?></td>
                </tr>
                <tr>
                    <th>Dikembalikan Oleh</th>
                    <td><?php echo htmlspecialchars($return['returned_by_name']); ?></td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td><?php echo date('d/m/Y H:i', strtotime($return['created_at'])); ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <?php if ($return['approved_by']): ?>
                            <span class="badge bg-success">Disetujui oleh <?php echo htmlspecialchars($return['approved_by_name']); ?></span>
                        <?php else: ?>
                            <span class="badge bg-warning">Menunggu Persetujuan</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    } else {
        echo "<div class='alert alert-danger'>Retur tidak ditemukan</div>";
    }
} else {
    echo "<div class='alert alert-danger'>ID retur tidak valid</div>";
}
?>
