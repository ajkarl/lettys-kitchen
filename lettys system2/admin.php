<?php
// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=letty_kitchen1', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Query to get total sales
try {
    $totalSalesQuery = $pdo->query("SELECT SUM(price * quantity) AS total_sales FROM sales");
    $totalSales = $totalSalesQuery->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0;
} catch (PDOException $e) {
    $totalSales = 0; // Default value if table or query fails
}

// Query to get total returns
try {
    $totalReturnsQuery = $pdo->query("SELECT SUM(total_price) AS total_returns FROM returns");
    $totalReturns = $totalReturnsQuery->fetch(PDO::FETCH_ASSOC)['total_returns'] ?? 0;
} catch (PDOException $e) {
    $totalReturns = 0; // Default value if table or query fails
}

// Query to get the top-selling product
try {
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
} catch (PDOException $e) {
    $topProduct = 'N/A';
}

// Query to get customer feedback
try {
    $feedbackQuery = $pdo->query("SELECT customer_name, feedback FROM feedback");
    $feedbacks = $feedbackQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $feedbacks = [];
}

// Add Product functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add-product'])) {
    $productName = $_POST['product-name'] ?? '';
    $productPrice = $_POST['product-price'] ?? '';
    $productImage = $_POST['product-image'] ?? '';

    if ($productName && $productPrice) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, price, image_url) VALUES (?, ?, ?)");
            $stmt->execute([$productName, $productPrice, $productImage]);
            echo "<script>alert('Product added successfully!');</script>";
        } catch (PDOException $e) {
            echo "<script>alert('Failed to add product: {$e->getMessage()}');</script>";
        }
    } else {
        echo "<script>alert('Please provide product name and price!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Letty's Kitchen Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
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
                <a class="hover:text-gray-300" href="#"><i class="fas fa-sign-out-alt"></i></a>
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
