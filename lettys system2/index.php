<?php
session_start();
require 'db.php'; // Include database connection

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Fetch products from the database
$query = $pdo->query("SELECT * FROM products");
$products = $query->fetchAll(PDO::FETCH_ASSOC);

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letty's Kitchen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">

<!-- Header -->
<header class="bg-red-600 text-white">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
        <div class="text-2xl font-bold">Letty's Kitchen</div>
        <div class="flex items-center space-x-4">
            <a class="hover:text-gray-300" href="index.php">Home</a>
            <a class="hover:text-gray-300" href="productscustomer.php">Shop</a>
            <a class="hover:text-gray-300" href="#">About</a>
            <a class="hover:text-gray-300" href="#">Contact</a>
            <a class="hover:text-gray-300" href="cart.php">
                <i class="fas fa-shopping-cart"></i> 
                <?php echo count($_SESSION['cart']); ?>
            </a>

            <!-- Login/Signup or Profile -->
            <?php if ($isLoggedIn): ?>
                <!-- Profile Icon if logged in -->
                <a href="account.php" class="hover:text-gray-300">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($userName); ?>
                </a>
                <a href="signup.php" class="hover:text-gray-300">Logout</a>
            <?php else: ?>
                <!-- Login/Signup Icon if not logged in -->
                <a href="login.php" class="hover:text-gray-300">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="signup.php" class="hover:text-gray-300">
                    <i class="fas fa-user-plus"></i> Sign Up
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="container mx-auto py-8 px-6">
    <!-- Hero Section -->
    <section class="bg-red-500 text-white p-8 rounded-lg mb-8">
        <h1 class="text-4xl font-bold mb-4">Welcome to Letty's Kitchen</h1>
        <p class="text-lg mb-4">Your one-stop shop for all your kitchen needs.</p>
        <a href="#products" class="bg-white text-red-500 px-4 py-2 rounded">Shop Now</a>
    </section>

    <!-- Product Section -->
    <section id="products">
        <h2 class="text-2xl font-bold mb-4">Featured Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
            <div class="bg-white p-4 rounded-lg shadow">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-48 object-cover mb-4 rounded">
                <h3 class="text-lg font-bold mb-2"><?php echo $product['name']; ?></h3>
                <p class="text-gray-700 mb-2">$<?php echo number_format($product['price'], 2); ?></p>
                <form method="POST" action="add-to-cart.php">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Add to Cart</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<!-- Footer -->
<footer class="bg-red-600 text-white py-4">
    <div class="container mx-auto text-center">
        <p>© <?php echo date('Y'); ?> Letty's Kitchen. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
