<?php 
session_start();
require 'db.php'; // Include database connection

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle "Add to Cart" AJAX Request
if (isset($_POST['ajax_add_to_cart'])) {
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $productPrice = $_POST['product_price'];

    // Add the product to the cart
    $_SESSION['cart'][] = [
        'id' => $productId,
        'name' => $productName,
        'price' => $productPrice,
    ];

    echo json_encode([
        'success' => true,
        'cartCount' => count($_SESSION['cart']),
    ]);
    exit;
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }

        /* Snackbar Styles */
        .snackbar {
            position: fixed;
            bottom: -50px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: #fff;
            padding: 12px 24px;
            border-radius: 5px;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s, bottom 0.3s;
        }

        .snackbar.show {
            bottom: 20px;
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gray-100">

<!-- Header -->
<header class="bg-red-600 text-white">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
        <div class="text-2xl font-bold">Letty's Kitchen</div>
        <div class="flex items-center space-x-4">
            <a class="hover:text-gray-300" href="home page.php">Home</a>
            <a class="hover:text-gray-300" href="productscustomer.php">Shop</a>
            <a class="hover:text-gray-300" href="cart.php">
                <i class="fas fa-shopping-cart"></i> 
                <span id="cart-count"><?php echo count($_SESSION['cart']); ?></span>
            </a>

            <!-- Login/Signup or Profile -->
            <?php if ($isLoggedIn): ?>
                <a href="account.php" class="hover:text-gray-300">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($userName); ?>
                </a>
                <a href="signup.php" class="hover:text-gray-300">Logout</a>
            <?php else: ?>
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
        <p class="text-lg mb-4">Your one-stop shop for all your lutong bahay ulam</p>
        <a href="#products" class="bg-white text-red-500 px-4 py-2 rounded">Shop Now</a>
    </section>

    <!-- Product Section -->
    <section id="products">
        <h2 class="text-2xl font-bold mb-4">Featured Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
            <div class="bg-white p-4 rounded-lg shadow">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-48 object-cover mb-4 rounded">
                <h3 class="text-lg font-bold mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="text-gray-700 mb-2"><?php echo htmlspecialchars($product['description']); ?></p> <!-- Description -->
                <p class="text-gray-700 mb-2">₱<?php echo number_format($product['price'], 2); ?></p>
                
                <?php if ($product['status'] === 'Sold Out' || $product['status'] === 'Not Available'): ?>
                    <p class="text-red-500 font-bold mb-2"><?php echo $product['status']; ?></p>
                    <button disabled class="bg-gray-400 text-white px-4 py-2 rounded cursor-not-allowed">Unavailable</button>
                <?php else: ?>
                    <button class="add-to-cart bg-red-500 text-white px-4 py-2 rounded"
                        data-id="<?php echo $product['id']; ?>"
                        data-name="<?php echo htmlspecialchars($product['name']); ?>"
                        data-price="<?php echo $product['price']; ?>">
                        Add to Cart
                    </button>
                <?php endif; ?>
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

<script>
    // Function to show Snackbar
    function showSnackbar(message) {
        const snackbar = document.createElement('div');
        snackbar.innerText = message;
        snackbar.className = 'snackbar';
        document.body.appendChild(snackbar);

        setTimeout(() => {
            snackbar.classList.add('show');
        }, 100);

        // Remove Snackbar after 3 seconds
        setTimeout(() => {
            snackbar.classList.remove('show');
            setTimeout(() => snackbar.remove(), 300); // Clean up DOM
        }, 3000);
    }

    // AJAX Add to Cart
    $(document).on('click', '.add-to-cart', function() {
        const productId = $(this).data('id');
        const productName = $(this).data('name');
        const productPrice = $(this).data('price');

        $.post('productscustomer.php', {
            ajax_add_to_cart: true,
            product_id: productId,
            product_name: productName,
            product_price: productPrice
        }, function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                // Update cart count silently
                $('#cart-count').text(result.cartCount);

                // Show Snackbar
                showSnackbar(`${productName} has been added to your cart.`);
            }
        });
    });
</script>

</body>
</html>
