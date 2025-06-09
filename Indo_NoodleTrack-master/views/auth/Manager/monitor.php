<?php
session_start();
require_once '../../../config/database.php';

// Check login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header('Location: ../../login.php');
    exit();
}

// Query untuk mengambil data monitoring
$query = "SELECT * FROM orders 
          JOIN order_details ON orders.order_id = order_details.order_id 
          JOIN products ON order_details.product_id = products.product_id 
          WHERE orders.status != 'completed' 
          ORDER BY orders.created_at DESC";

$result = mysqli_query($conn, $query);

// Ambil data order terbaru
$order = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring - Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: "Poppins", sans-serif;
        }
    </style>
</head>
<body class="bg-white">
    <div class="flex min-h-screen max-h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#4A9AB7] text-white flex flex-col py-10 px-6 rounded-tr-3xl rounded-br-3xl">
            <div class="flex flex-col items-center mb-12">
                <img src="/Indo_NoodleTrack-master/assets/images/logo.jpg" alt="Logo" class="w-16 h-16 mb-2 rounded-full">
                <span class="text-xl font-bold">indo<br>noodle<br>track.</span>
            </div>
            <nav class="flex flex-col gap-4 text-sm font-semibold">
                <a href="./dashboard.php" class="flex items-center gap-2">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="./penerimaanpermintaanmasuk.php" class="flex items-center gap-2">
                    <i class="fas fa-file-invoice"></i> Permintaan Masuk
                </a>
                <a href="./returmasuk.php" class="flex items-center gap-2">
                    <i class="fas fa-sync-alt"></i> Retur Masuk
                </a>
                <a href="./monitor.php" class="flex items-center gap-2 active">
                    <i class="fas fa-cube"></i> Monitoring
                </a>
                <a href="./stok-bahan-baku.php" class="flex items-center gap-2">
                    <i class="fas fa-box"></i> Stok
                </a>
                <a href="../../auth/login.php" class="flex items-center gap-2 mt-4">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>

        <!-- Main content -->
        <main class="flex-1 flex flex-col bg-white rounded-tl-3xl rounded-bl-3xl overflow-hidden">
            <!-- Header -->
            <header class="flex items-center justify-between px-8 py-6 border-b border-gray-200 select-none">
                <h1 class="text-3xl font-extrabold text-[#4A9AB7]">
                    Monitoring
                </h1>
                <div class="relative w-64">
                    <input class="w-full rounded-full bg-[#D4F1F7] text-[#4A9AB7] text-xs py-1.5 px-5 focus:outline-none" placeholder="Search" type="search"/>
                    <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-[#4A9AB7] text-xs">
                    </i>
                </div>
                <div class="flex items-center space-x-3">
                    <img alt="User profile photo" class="rounded-full w-10 h-10 object-cover" height="40" src="https://storage.googleapis.com/a1aa/image/bb8b71b3-3194-4a7e-0787-7eea05cdebe9.jpg" width="40"/>
                    <div>
                        <p class="text-[#2E1E1E] font-semibold text-sm leading-5">
                            Manager
                        </p>
                        <p class="text-[#8B8B8B] text-xs leading-4">
                            User id : <?php echo $_SESSION['user_id']; ?>
                        </p>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <section class="flex flex-1 overflow-hidden">
                <!-- Left content -->
                <section class="flex-1 bg-[#D4F1F7] p-6 overflow-y-auto rounded-tr-3xl rounded-br-3xl">
                    <?php if ($order) : ?>
                    <h2 class="text-base font-bold mb-4">
                        Order ID :
                        <span class="font-extrabold">
                            <?php echo $order['order_id']; ?>
                        </span>
                    </h2>
                    
                    <!-- With driver label -->
                    <div class="flex items-center mb-3 space-x-2">
                        <div class="w-3.5 h-3.5 rounded-full bg-[#D98B2B]">
                        </div>
                        <p class="text-[#9E9E9E] font-semibold text-sm">
                            <?php echo $order['driver_id'] ? 'With driver' : 'No driver assigned'; ?>
                        </p>
                    </div>

                    <!-- Status cards -->
                    <div class="flex space-x-4 mb-5 max-w-4xl">
                        <div class="bg-white rounded-lg p-4 flex items-center space-x-3 w-36 shadow-sm">
                            <div class="bg-[#E6E9F7] p-2.5 rounded-lg flex justify-center items-center">
                                <i class="fas fa-chart-pie text-[#4A4A6A] text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-[#9E9E9E] mb-0.5">Order Placed</p>
                                <p class="text-sm font-normal text-black"><?php echo $order['created_at']; ?></p>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-4 flex items-center space-x-3 w-36 shadow-sm">
                            <div class="bg-[#E6E9F7] p-2.5 rounded-lg flex justify-center items-center">
                                <i class="fas fa-shopping-cart text-[#4A4A6A] text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-[#9E9E9E] mb-0.5">Shipped</p>
                                <p class="text-sm font-normal text-black"><?php echo $order['shipped_at'] ?? 'Pending'; ?></p>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-4 flex items-center space-x-3 w-36 shadow-sm">
                            <div class="bg-[#E6E9F7] p-2.5 rounded-lg flex justify-center items-center">
                                <i class="fas fa-cube text-[#4A4A6A] text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-[#9E9E9E] mb-0.5">Completed</p>
                                <p class="text-sm font-semibold text-black"><?php echo $order['completed_at'] ?? 'Pending'; ?></p>
                            </div>
                        </div>
                    </div>

                    <hr class="border-t border-[#8BC6C9] mb-5"/>

                    <!-- Shipping addresses -->
                    <div class="flex space-x-4 mb-5 max-w-4xl">
                        <div aria-label="Shipping Address Warehouse" class="bg-white rounded-lg p-4 shadow-md w-80 relative">
                            <h3 class="font-semibold text-sm mb-1 flex justify-between items-center">
                                Shipping Address (Warehouse)
                                <button aria-label="Edit Shipping Address Warehouse" class="text-black text-lg">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </h3>
                            <p class="text-[10px] text-[#9E9E9E] leading-4">
                                <?php echo $order['warehouse_address']; ?>
                            </p>
                        </div>
                        <div aria-label="Shipping Address Production" class="bg-white rounded-lg p-4 shadow-md w-80 relative">
                            <h3 class="font-semibold text-sm mb-1 flex justify-between items-center">
                                Shipping Address (Production)
                                <button aria-label="Edit Shipping Address Production" class="text-black text-lg">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </h3>
                            <p class="text-[10px] text-[#9E9E9E] leading-4">
                                <?php echo $order['production_address']; ?>
                            </p>
                        </div>
                    </div>

                    <hr class="border-t border-[#8BC6C9] mb-5"/>

                    <!-- Order Item -->
                    <h3 class="text-[#9E9E9E] font-semibold text-sm mb-5">Order Item</h3>
                    <ul class="space-y-5 max-w-4xl">
                        <?php 
                        $items = mysqli_query($conn, "SELECT * FROM order_details 
                                                    JOIN products ON order_details.product_id = products.product_id 
                                                    WHERE order_details.order_id = '{$order['order_id']}'");
                        while($item = mysqli_fetch_assoc($items)) : 
                        ?>
                        <li class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <img alt="<?php echo $item['product_name']; ?>" class="w-14 h-14 object-contain bg-white p-1 rounded" 
                                     src="<?php echo $item['product_image']; ?>" />
                                <div>
                                    <p class="text-xs text-[#2E2E2E]">
                                        <?php echo $item['product_name']; ?>
                                    </p>
                                    <p class="text-[9px] text-[#9E9E9E]">
                                        Exp : <?php echo $item['expiry_date']; ?>
                                    </p>
                                </div>
                            </div>
                            <p class="text-xs text-[#2E2E2E]">
                                <?php echo $item['quantity']; ?> Kg
                            </p>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                    <?php else : ?>
                    <div class="text-center py-12 text-gray-500">
                        <p>No active orders found</p>
                    </div>
                    <?php endif; ?>
                </section>

                <!-- Right content -->
                <aside class="w-72 bg-[#D4F1F7] border-l border-[#8BC6C9] p-5 overflow-y-auto select-none">
                    <h3 class="font-semibold text-sm mb-5">Raw Material Journey</h3>
                    <div class="space-y-4">
                        <?php if ($order) : ?>
                        <div class="bg-white rounded-lg p-4">
                            <h4 class="font-semibold text-sm mb-2">Order Placed</h4>
                            <p class="text-[10px] text-[#9E9E9E]"><?php echo $order['created_at']; ?></p>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <h4 class="font-semibold text-sm mb-2">Shipped</h4>
                            <p class="text-[10px] text-[#9E9E9E]"><?php echo $order['shipped_at'] ?? 'Pending'; ?></p>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <h4 class="font-semibold text-sm mb-2">Arrived at Warehouse</h4>
                            <p class="text-[10px] text-[#9E9E9E]"><?php echo $order['arrived_at'] ?? 'Pending'; ?></p>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <h4 class="font-semibold text-sm mb-2">Completed</h4>
                            <p class="text-[10px] text-[#9E9E9E]"><?php echo $order['completed_at'] ?? 'Pending'; ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </aside>
            </section>
        </main>
    </div>
</body>
</html>
