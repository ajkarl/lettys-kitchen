<?php
// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=letty_kitchen1', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch top-selling products
try {
    $topSellingQuery = $pdo->query("
        SELECT 
            products.name AS product_name, 
            products.price, 
            SUM(sales.quantity) AS total_quantity
        FROM sales
        JOIN products ON sales.product_id = products.id
        GROUP BY sales.product_id
        ORDER BY total_quantity DESC
        LIMIT 10
    ");
    $topSellingProducts = $topSellingQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching top-selling products: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Selling Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <?php include('header.php'); ?>

    <!-- Main Content -->
    <main class="container mx-auto py-8 px-6">
        <h2 class="text-2xl font-bold mb-4">Top Selling Products</h2>
        <div class="bg-white p-6 rounded-lg shadow">
            <?php if (!empty($topSellingProducts)): ?>
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 p-4">Product Name</th>
                            <th class="border border-gray-300 p-4">Price</th>
                            <th class="border border-gray-300 p-4">Quantity Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topSellingProducts as $product): ?>
                            <tr>
                                <td class="border border-gray-300 p-4"><?= htmlspecialchars($product['product_name']) ?></td>
                                <td class="border border-gray-300 p-4">$<?= number_format($product['price'], 2) ?></td>
                                <td class="border border-gray-300 p-4"><?= htmlspecialchars($product['total_quantity']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-gray-700">No top-selling products available.</p>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <?php include('footer.php'); ?>
</body>
</html>
