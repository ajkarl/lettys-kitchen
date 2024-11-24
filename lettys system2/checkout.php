<?php
session_start();
require 'db.php'; // Include your database connection

// If the cart is empty or not set, show a message
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Your cart is empty!";
    exit;
}

// Calculate total amount
$totalAmount = 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<header class="bg-red-600 text-white py-4">
    <div class="container mx-auto text-center">
        <a class="hover:text-gray-300" href="index.php">Home</a>
        <h1 class="text-2xl font-bold">Checkout</h1>
    </div>
</header>

<main class="container mx-auto py-8 px-6">
    <h2 class="text-2xl font-semibold mb-4">Order Summary</h2>

    <table class="min-w-full bg-white border rounded mb-6">
        <thead>
            <tr>
                <th class="py-2 px-4 border">Product</th>
                <th class="py-2 px-4 border">Price</th>
                <th class="py-2 px-4 border">Quantity</th>
                <th class="py-2 px-4 border">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($_SESSION['cart'] as $productId => $item):
                // Ensure quantity is set, default to 1 if not
                $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
                $itemTotal = $quantity * $item['price'];
                $totalAmount += $itemTotal;
            ?>
            <tr>
                <td class="py-2 px-4 border"><?php echo htmlspecialchars($item['name']); ?></td>
                <td class="py-2 px-4 border">₱<?php echo number_format($item['price'], 2); ?></td>
                <td class="py-2 px-4 border"><?php echo $quantity; ?></td>
                <td class="py-2 px-4 border">₱<?php echo number_format($itemTotal, 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="flex justify-between items-center">
        <p class="text-lg font-bold">
            Total: ₱<?php echo number_format($totalAmount, 2); ?>
        </p>
    </div>

    <form method="POST" action="place_order.php">
        <!-- Delivery Information -->
        <div class="mt-6">
            <h3 class="text-lg font-semibold mb-4">Delivery Information</h3>
            <label for="address" class="block text-sm">Delivery Address:</label>
            <input type="text" id="address" name="address" class="w-full px-4 py-2 border rounded" required placeholder="Enter your delivery address">

            <label for="instructions" class="block text-sm mt-4">Special Instructions for the Driver:</label>
            <textarea id="instructions" name="instructions" class="w-full px-4 py-2 border rounded" rows="4" placeholder="Any special instructions for the driver?"></textarea>

            <label for="delivery_option" class="block text-sm mt-4">Delivery Option:</label>
            <select id="delivery_option" name="delivery_option" class="w-full px-4 py-2 border rounded">
                <option value="standard">Standard Delivery</option>
                <option value="express">Express Delivery</option>
            </select>

            <label for="schedule" class="block text-sm mt-4">Delivery Schedule:</label>
            <input type="datetime-local" id="schedule" name="schedule" class="w-full px-4 py-2 border rounded" required>

            <label for="payment_method" class="block text-sm mt-4">Payment Method:</label>
            <select id="payment_method" name="payment_method" class="w-full px-4 py-2 border rounded" required>
                <option value="gcash">GCash</option>
                <option value="credit_card">Credit Card</option>
                <option value="paypal">PayPal</option>
            </select>

            <label for="tip" class="block text-sm mt-4">Tip (if paying via GCash):</label>
            <input type="number" id="tip" name="tip" class="w-full px-4 py-2 border rounded" placeholder="Enter tip amount" min="0" step="50">

            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded mt-6">Place Order</button>
        </div>
    </form>

</main>

<footer class="bg-red-600 text-white py-4">
    <div class="container mx-auto text-center">
        <p>© <?php echo date('Y'); ?> Letty's Kitchen. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
