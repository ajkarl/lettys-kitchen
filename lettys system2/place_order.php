<?php
session_start();
require 'db.php'; // Include your database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture form fields
    $deliveryOption = $_POST['delivery_option'];
    $address = $_POST['address']; // Ensure the address field is correctly captured
    $instructions = $_POST['instructions'];
    $schedule = $_POST['schedule'];
    $tip = $_POST['tip'];
    $paymentMethod = $_POST['payment_method'];
    $totalAmount = 0;

    // Calculate total amount from cart
    foreach ($_SESSION['cart'] as $productId => $item) {
        $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
        $totalAmount += $quantity * $item['price'];
    }

    // Prepare SQL query to insert order into the database
    $sql = "INSERT INTO orders (user_id, delivery_option, address, instructions, schedule, tip, payment_method, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    // Execute the query with parameters
    $stmt->execute([$_SESSION['user_id'], $deliveryOption, $address, $instructions, $schedule, $tip, $paymentMethod, $totalAmount]);

    // Get the order ID
    $orderId = $pdo->lastInsertId();

    // Now, insert the products from the cart into the order_items table
    foreach ($_SESSION['cart'] as $productId => $item) {
        $productName = $item['name'];
        $productPrice = $item['price'];
        $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
        $itemTotal = $quantity * $productPrice;

        // Insert order items into the order_items table
        $sql = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price, total) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId, $productId, $productName, $quantity, $productPrice, $itemTotal]);
    }

    // Clear the cart after placing the order
    unset($_SESSION['cart']);

    // Redirect to order confirmation page
    header('Location: order_confirmation.php?order_id=' . $orderId);
    exit;
}
?>
