<?php
session_start();

// Check if user is logged in and has produksi role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'produksi') {
    header('Location: ../../login.php');
    exit();
}

// Include database connection
require_once __DIR__ . '/../../../config/database.php';

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

// Get statistics
// Total production orders
$stmt = $conn->prepare("SELECT COUNT(*) as total_orders FROM orders WHERE status IN ('pending', 'processing')");
$stmt->execute();
$result = $stmt->get_result();
$total_orders = $result->fetch_assoc()['total_orders'] ?? 0;

// Completed production
$stmt = $conn->prepare("SELECT COUNT(*) as completed_orders FROM orders WHERE status = 'completed'");
$stmt->execute();
$result = $stmt->get_result();
$completed_orders = $result->fetch_assoc()['completed_orders'] ?? 0;

// Production progress
$stmt = $conn->prepare("SELECT COUNT(*) as in_progress FROM orders WHERE status = 'processing'");
$stmt->execute();
$result = $stmt->get_result();
$in_progress = $result->fetch_assoc()['in_progress'] ?? 0;

// Get recent orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE status IN ('pending', 'processing') ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$result = $stmt->get_result();
$recent_orders = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IndoNoodle Track - Produksi Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dashboard-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .stat-card h3 {
            color: #333;
            margin-bottom: 10px;
        }
        .stat-card p {
            font-size: 24px;
            font-weight: bold;
            color: #666;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">IndoNoodle Track</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="production-orders.php">Production Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="production-progress.php">Production Progress</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="materials.php">Materials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../../views/auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <h3>Total Production Orders</h3>
                    <p><?php echo number_format($total_orders); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h3>Completed Production</h3>
                    <p><?php echo number_format($completed_orders); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h3>In Progress</h3>
                    <p><?php echo number_format($in_progress); ?></p>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="dashboard-card">
                    <h3>Recent Production Orders</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $order['status'] === 'completed' ? 'success' : ($order['status'] === 'processing' ? 'warning' : 'secondary'); ?>">
                                        <?php echo htmlspecialchars($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
