<?php
session_start();

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding items to the cart
if (isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $productPrice = $_POST['product_price'];

    // Check if the product already exists in the cart
    $itemFound = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $productId) {
            $item['quantity'] += 1; // Increment quantity if the item already exists
            $itemFound = true;
            break;
        }
    }

    // Add new item if it doesn't exist in the cart
    if (!$itemFound) {
        $_SESSION['cart'][] = [
            'id' => $productId,
            'name' => $productName,
            'price' => $productPrice,
            'quantity' => 1, // Initialize quantity to 1
        ];
    }

    echo json_encode([
        'success' => true,
        'cartCount' => count($_SESSION['cart']),
    ]);
    exit;
}

echo json_encode(['success' => false]);
exit;
