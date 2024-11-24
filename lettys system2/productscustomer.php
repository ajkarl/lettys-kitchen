<?php
session_start();
require 'db.php'; // Include database connection file

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle "Add to Cart" AJAX request
if (isset($_POST['ajax_add_to_cart'])) {
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $productPrice = $_POST['product_price'];

    // Add product to the session cart
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Letty's Kitchen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        body { font-family: 'Roboto', sans-serif; }
        .snackbar {
            visibility: hidden;
            min-width: 250px;
            background-color: #04AA6D;
            color: white;
            text-align: center;
            border-radius: 5px;
            padding: 10px;
            position: fixed;
            z-index: 1;
            left: 50%;
            bottom: 30px;
            transform: translateX(-50%);
            font-size: 1rem;
        }
        .snackbar.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }
        @keyframes fadein {
            from { bottom: 0; opacity: 0; }
            to { bottom: 30px; opacity: 1; }
        }
        @keyframes fadeout {
            from { bottom: 30px; opacity: 1; }
            to { bottom: 0; opacity: 0; }
        }
    </style>
</head>
<body class="bg-gray-100">

<!-- Header -->
<header class="bg-red-600 text-white">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
        <div class="text-2xl font-bold">Letty's Kitchen</div>
        <div class="flex items-center space-x-4">
            <a class="hover:text-gray-300" href="index.php">Home</a>
            <a class="hover:text-gray-300" href="cart.php">
                <i class="fas fa-shopping-cart"></i>
                <span id="cart-count"><?php echo count($_SESSION['cart']); ?></span>
            </a>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="container mx-auto py-8 px-6">
    <!-- Snackbar -->
    <div id="snackbar" class="snackbar"></div>

    <section id="products">
        <h2 class="text-2xl font-bold mb-4">Our Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
            <div class="bg-white p-4 rounded-lg shadow">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-48 object-cover mb-4 rounded">
                <h3 class="text-lg font-bold mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="text-gray-700 mb-2">₱<?php echo number_format($product['price'], 2); ?></p>
                <button class="add-to-cart bg-red-500 text-white px-4 py-2 rounded"
                        data-id="<?php echo $product['id']; ?>"
                        data-name="<?php echo $product['name']; ?>"
                        data-price="<?php echo $product['price']; ?>">
                    Add to Cart
                </button>
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
    $(document).on('click', '.add-to-cart', function () {
        const productId = $(this).data('id');
        const productName = $(this).data('name');
        const productPrice = $(this).data('price');

        $.post('productscustomer.php', {
            ajax_add_to_cart: true,
            product_id: productId,
            product_name: productName,
            product_price: productPrice
        }, function (response) {
            try {
                const result = JSON.parse(response);
                if (result.success) {
                    // Update cart count
                    $('#cart-count').text(result.cartCount);

                    // Show snackbar notification
                    const snackbar = $('#snackbar');
                    snackbar.text(`${productName} has been added to your cart.`);
                    snackbar.addClass('show');

                    // Remove the class after 3 seconds
                    setTimeout(() => {
                        snackbar.removeClass('show');
                    }, 3000);
                }
            } catch (e) {
                console.error('Error parsing response:', response);
            }
        }).fail(function (xhr, status, error) {
            console.error('AJAX request failed:', status, error);
        });
    });
</script>

</body>
</html>
