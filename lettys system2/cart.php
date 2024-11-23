<?php
session_start();

// Handle remove item request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    $removeId = $_POST['remove_id'];
    // Remove the item with the matching ID from the cart
    if (isset($_SESSION['cart'][$removeId])) {
        unset($_SESSION['cart'][$removeId]);
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
        <table class="min-w-full bg-white border rounded">
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
                    <td class="py-2 px-4 border">$<?php echo number_format($item['price'], 2); ?></td>
                    <td class="py-2 px-4 border"><?php echo (int)$item['quantity']; ?></td>
                    <td class="py-2 px-4 border">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
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
