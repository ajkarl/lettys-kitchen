<?php
session_start();
require 'db.php'; // Include your database connection

// Handle remove item request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_id'])) {
        $removeId = $_POST['remove_id'];
        if (isset($_SESSION['cart'][$removeId])) {
            unset($_SESSION['cart'][$removeId]);
        }
    }

    // Handle quantity update request (Increase or Decrease)
    if (isset($_POST['update_quantity'])) {
        if (isset($_POST['product_id'])) {
            $productId = $_POST['product_id'];
            $currentQuantity = isset($_SESSION['cart'][$productId]['quantity']) ? $_SESSION['cart'][$productId]['quantity'] : 1;

            if ($_POST['update_quantity'] === 'plus') {
                $_SESSION['cart'][$productId]['quantity'] = $currentQuantity + 1;
            } elseif ($_POST['update_quantity'] === 'minus' && $currentQuantity > 1) {
                $_SESSION['cart'][$productId]['quantity'] = $currentQuantity - 1;
            }
        }
    }

    // Handle "Order Now" action for a specific product
    if (isset($_POST['order_now'])) {
        if (isset($_POST['order_id'])) {
            $orderTotal = 0;
            foreach ($_POST['order_id'] as $productId) {
                if (isset($_SESSION['cart'][$productId])) {
                    $item = $_SESSION['cart'][$productId];
                    $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
                    $orderTotal += $quantity * $item['price'];

                    $stmt = $pdo->prepare("UPDATE admin_total_sales SET total_sales = total_sales + ?");
                    $stmt->execute([$orderTotal]);

                    unset($_SESSION['cart'][$productId]);
                }
            }

            echo "<p class='text-green-500 text-center mb-4'>Order placed successfully! Total: ₱" . number_format($orderTotal, 2) . "</p>";
        }
    }

    // Handle "Order All" action for all items in the cart
    if (isset($_POST['order_all'])) {
        if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
            $orderTotal = array_sum(array_map(fn($item) => (isset($item['quantity']) ? $item['quantity'] : 1) * $item['price'], $_SESSION['cart']));

            $stmt = $pdo->prepare("UPDATE admin_total_sales SET total_sales = total_sales + ?");
            $stmt->execute([$orderTotal]);

            echo "<p class='text-green-500 text-center mb-4'>Order placed successfully! Total: ₱" . number_format($orderTotal, 2) . "</p>";

            unset($_SESSION['cart']);
        } else {
            echo "<p class='text-red-500 text-center mb-4'>Your cart is empty. Add items before ordering!</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<header class="bg-red-600 text-white py-4">
    <div class="container mx-auto text-center">
        <a class="hover:text-gray-300" href="index.php">Home</a>
        <h1 class="text-2xl font-bold">Your Cart</h1>
    </div>
</header>

<main class="container mx-auto py-8 px-6">
    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <form method="POST" action="">
            <table class="min-w-full bg-white border rounded mb-6">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border">Product</th>
                        <th class="py-2 px-4 border">Price</th>
                        <th class="py-2 px-4 border">Quantity</th>
                        <th class="py-2 px-4 border">Total</th>
                        <th class="py-2 px-4 border">Order Now</th>
                        <th class="py-2 px-4 border">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $productId => $item): ?>
                    <tr>
                        <td class="py-2 px-4 border"><?php echo htmlspecialchars($item['name']); ?></td>
                        <td class="py-2 px-4 border">₱<?php echo number_format($item['price'], 2); ?></td>
                        <td class="py-2 px-4 border">
                            <div class="flex items-center space-x-2">
                                <form method="POST" action="">
                                    <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                    <button type="submit" name="update_quantity" value="minus" class="bg-gray-300 text-black px-2 py-1 rounded">-</button>
                                    <input type="number" name="quantity" value="<?php echo isset($item['quantity']) ? $item['quantity'] : 1; ?>" min="1" class="w-16 text-center border rounded" readonly>
                                    <button type="submit" name="update_quantity" value="plus" class="bg-gray-300 text-black px-2 py-1 rounded">+</button>
                                </form>
                            </div>
                        </td>
                        <td class="py-2 px-4 border">₱<?php echo number_format((isset($item['quantity']) ? $item['quantity'] : 1) * $item['price'], 2); ?></td>
                        <td class="py-2 px-4 border">
                            <input type="checkbox" name="order_id[]" value="<?php echo $productId; ?>">
                        </td>
                        <td class="py-2 px-4 border">
                            <form method="POST" action="">
                                <input type="hidden" name="remove_id" value="<?php echo $productId; ?>">
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="flex justify-between items-center">
                <p class="text-lg font-bold">
                    Total: ₱<?php 
                        echo number_format(array_sum(
                            array_map(fn($item) => (isset($item['quantity']) ? $item['quantity'] : 1) * $item['price'], $_SESSION['cart'])
                        ), 2); 
                    ?>
                </p>
            </div>

            <div class="flex justify-center mt-4">
            </div>
        </form>
    <?php else: ?>
        <p class="text-center text-gray-700 mt-4">Your cart is empty.</p>
    <?php endif; ?>
    
    <!-- Proceed to Checkout Button -->
    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <div class="flex justify-center mt-6">
            <form action="checkout.php" method="GET">
                <button type="submit" class="bg-yellow-500 text-white px-6 py-2 rounded">Proceed to Checkout</button>
            </form>
        </div>
    <?php endif; ?>
</main>

<footer class="bg-red-600 text-white py-4">
    <div class="container mx-auto text-center">
        <p>© <?php echo date('Y'); ?> Letty's Kitchen. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
