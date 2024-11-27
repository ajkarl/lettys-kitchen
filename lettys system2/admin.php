<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

require 'db.php'; // Include the database connection

// Query to get total sales
$totalSalesQuery = $pdo->query("SELECT SUM(price * quantity) AS total_sales FROM sales");
$totalSales = $totalSalesQuery->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0;

// Query to get total returns
$totalReturnsQuery = $pdo->query("SELECT SUM(total_price) AS total_returns FROM returns");
$totalReturns = $totalReturnsQuery->fetch(PDO::FETCH_ASSOC)['total_returns'] ?? 0;

// Query to get the top-selling product
$topSellingQuery = $pdo->query("
    SELECT products.name, SUM(sales.quantity) AS total_quantity
    FROM sales
    JOIN products ON sales.product_id = products.id
    GROUP BY sales.product_id
    ORDER BY total_quantity DESC
    LIMIT 1
");
$topSelling = $topSellingQuery->fetch(PDO::FETCH_ASSOC);
$topProduct = $topSelling['name'] ?? 'N/A';

// Query to get customer feedback
$feedbackQuery = $pdo->query("SELECT customer_name, feedback FROM feedback");
$feedbacks = $feedbackQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Letty's Kitchen Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-red-600 text-white">
        <div class="container mx-auto flex justify-between items-center py-4 px-6">
            <div class="text-2xl font-bold">Letty's Kitchen Admin</div>
            <div class="flex items-center space-x-4">
                <a class="hover:text-gray-300" href="#">Dashboard</a>
                <a class="hover:text-gray-300" href="products.php">Products</a>
                <a class="hover:text-gray-300" href="sales.php">Sales</a>
                <a class="hover:text-gray-300" href="topselling.php">Top Selling</a>
                <a class="hover:text-gray-300" href="feedback.php">Feedback</a>
                <a class="hover:text-gray-300" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto py-8 px-6">
        <!-- Dashboard Section -->
        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">Dashboard</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-bold mb-2">Total Sales</h3>
                    <p class="text-gray-700 mb-2">$<?= number_format($totalSales, 2) ?></p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-bold mb-2">Total Returns</h3>
                    <p class="text-gray-700 mb-2">$<?= number_format($totalReturns, 2) ?></p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-bold mb-2">Top Selling Product</h3>
                    <p class="text-gray-700 mb-2"><?= htmlspecialchars($topProduct) ?></p>
                </div>
            </div>
        </section>

        <!-- Feedback Section -->
        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">Customer Feedback</h2>
            <div class="bg-white p-6 rounded-lg shadow">
                <?php if (!empty($feedbacks)): ?>
                    <?php foreach ($feedbacks as $feedback): ?>
                        <div class="mb-4">
                            <h3 class="text-lg font-bold mb-2"><?= htmlspecialchars($feedback['customer_name']) ?></h3>
                            <p class="text-gray-700 mb-2"><?= htmlspecialchars($feedback['feedback']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-700">No feedback available.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    
    <!-- Footer -->
    <footer class="bg-red-600 text-white py-4">
        <div class="container mx-auto text-center">
            <p>© <?= date('Y') ?> Letty's Kitchen. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
