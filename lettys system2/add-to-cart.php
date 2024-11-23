<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];

    $item = [
        'id' => $product_id,
        'name' => $product_name,
        'price' => $product_price,
        'quantity' => 1,
    ];

    // Initialize cart
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if the product is already in the cart
    $is_in_cart = false;
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['id'] === $product_id) {
            $cart_item['quantity']++;
            $is_in_cart = true;
            break;
        }
    }

    // Add new product if not in cart
    if (!$is_in_cart) {
        $_SESSION['cart'][] = $item;
    }

    header('Location: cart.php');
    exit;
}
?>
