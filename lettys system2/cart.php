<?php
session_start();
require 'db.php'; // Include your database connection

// Handle remove item request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Remove an item from the cart
    if (isset($_POST['remove_id'])) {
        $removeId = $_POST['remove_id'];
        if (isset($_SESSION['cart'][$removeId])) {
            unset($_SESSION['cart'][$removeId]);
        }
    }

    // Handle quantity update request
    if (isset($_POST['update_quantity'])) {
        // Check if the 'product_id' is set in the POST request
        if (isset($_POST['product_id'])) {
            $productId = $_POST['product_id'];
            $newQuantity = $_POST['quantity'];

            // Update the quantity in the cart
            if (isset($_SESSION['cart'][$productId]) && $newQuantity > 0) {
                $_SESSION['cart'][$productId]['quantity'] = $newQuantity;
            }
        }
    }

    // Handle order now action
    if (isset($_POST['order_now'])) {
        if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
            // Calculate the total order value
            $orderTotal = array_sum(
                array_map(fn($item) => (isset($item['quantity']) ? $item['quantity'] : 1) * $item['price'], $_SESSION['cart'])
            );

            // Update the admin's total sales in the database
            $stmt = $pdo->prepare("UPDATE admin_total_sales SET total_sales = total_sales + ?");
            $stmt->execute([$orderTotal]);

            // Success message
            echo "<p class='text-green-500 text-center mb-4'>Order placed successfully! Total: ₱" . number_format($orderTotal, 2) . "</p>";

            // Clear the cart after placing the order
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
    <style>
        body { font-family: Arial, sans-serif; }
    </style>
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
                        <th class="py-2 px-4 border">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $productId => $item): ?>
                    <tr>
                        <td class="py-2 px-4 border"><?php echo htmlspecialchars($item['name']); ?></td>
                        <td class="py-2 px-4 border">₱<?php echo number_format($item['price'], 2); ?></td>
                        <td class="py-2 px-4 border">
                            <!-- Plus/Minus Buttons for Quantity -->
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
                <button type="submit" name="order_now" class="bg-green-500 text-white px-4 py-2 rounded">Order Now</button>
            </div>
        </form>
    <?php else: ?>
        <p class="text-center text-gray-700 mt-4">Your cart is empty.</p>
    <?php endif; ?>
</main>

<footer class="bg-red-600 text-white py-4">
    <div class="container mx-auto text-center">
        <p>© <?php echo date('Y'); ?> Letty's Kitchen. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
