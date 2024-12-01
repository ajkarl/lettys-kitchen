<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';

if (!isset($_SESSION['driver_id'])) {
    header('Location: driver_login.php');
    exit;
}

$driver_id = $_SESSION['driver_id'];
$driver_name = $_SESSION['driver_name'];

// Fetch available orders
try {
    $ordersQuery = $pdo->query("
        SELECT orders.id, users.name AS customer_name, orders.details, orders.status 
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        WHERE orders.status = 'Pending'
    ");
    $orders = $ordersQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}

// Accept order
if (isset($_POST['accept_order'])) {
    $order_id = $_POST['order_id'];

    try {
        // Fetch the order and its items
        $orderQuery = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $orderQuery->execute([$order_id]);
        $order = $orderQuery->fetch(PDO::FETCH_ASSOC);

        // Calculate total order price
        $totalOrderPrice = 0;
        $orderItemsQuery = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $orderItemsQuery->execute([$order_id]);
        $orderItems = $orderItemsQuery->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orderItems as $item) {
            $totalOrderPrice += $item['total']; // total is already calculated as quantity * price
        }

        // Update order status to 'Accepted' and assign the driver
        $stmt = $pdo->prepare("UPDATE orders SET status = 'Accepted', driver_id = ?, driver_name = ? WHERE id = ?");
        $stmt->execute([$driver_id, $driver_name, $order_id]);

        // Update total sales in admin_total_sales
        $salesQuery = $pdo->prepare("SELECT total_sales FROM admin_total_sales WHERE id = 1");
        $salesQuery->execute();
        $salesData = $salesQuery->fetch(PDO::FETCH_ASSOC);
        $newTotalSales = $salesData['total_sales'] + $totalOrderPrice;

        // Update the total sales
        $updateSalesQuery = $pdo->prepare("UPDATE admin_total_sales SET total_sales = ? WHERE id = 1");
        $updateSalesQuery->execute([$newTotalSales]);

        // Insert sales data into the sales table
        foreach ($orderItems as $item) {
            // Check if product ID exists in the products table
            $productCheck = $pdo->prepare("SELECT id FROM products WHERE id = ?");
            $productCheck->execute([$item['product_id']]);

            if ($productCheck->rowCount() > 0) {
                $saleStmt = $pdo->prepare("INSERT INTO sales (product_id, quantity, price, sale_date) VALUES (?, ?, ?, NOW())");
                $saleStmt->execute([$item['product_id'], $item['quantity'], $item['price']]);
            } else {
                // Log invalid product ID
                error_log("Skipped invalid product ID: " . $item['product_id']);
            }
        }

        // Redirect to the dashboard
        header('Location: driver_dashboard.php');
        exit;

    } catch (PDOException $e) {
        die("Error updating order: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-blue-600 text-white shadow">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">Driver Dashboard</h1>
            <div>
                <span class="mr-4">Welcome, <?= htmlspecialchars($driver_name) ?></span>
                <a href="orders complete.php" class="bg-red-500 px-4 py-2 rounded text-white hover:bg-red-700">Orders Complete</a>
                <a href="driver_register.php" class="bg-red-500 px-4 py-2 rounded text-white hover:bg-red-700">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-8">
        <h2 class="text-2xl font-bold mb-4">Available Orders</h2>

        <?php if (!empty($orders)): ?>
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-4 py-2">Order ID</th>
                            <th class="border border-gray-300 px-4 py-2">Customer Name</th>
                            <th class="border border-gray-300 px-4 py-2">Details</th>
                            <th class="border border-gray-300 px-4 py-2">Status</th>
                            <th class="border border-gray-300 px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 text-center"><?= $order['id'] ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($order['details']) ?></td>
                                <td class="border border-gray-300 px-4 py-2 text-center"><?= $order['status'] ?></td>
                                <td class="border border-gray-300 px-4 py-2 text-center">
                                    <form method="POST">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <button type="submit" name="accept_order" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">Accept</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-700">No orders available at the moment.</p>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-4 mt-8">
        <div class="container mx-auto text-center">
            <p>&copy; <?= date('Y') ?> Driver Dashboard. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
