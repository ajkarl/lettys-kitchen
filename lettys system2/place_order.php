<?php
session_start();
require 'db.php'; // Include your database connection

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $address = $_POST['address'];
    $instructions = $_POST['instructions'];
    $deliveryOption = $_POST['delivery_option'];
    $schedule = $_POST['schedule'];
    $paymentMethod = $_POST['payment_method'];
    $tip = isset($_POST['tip']) ? $_POST['tip'] : 0;

    // Calculate total amount
    $totalAmount = 0;
    foreach ($_SESSION['cart'] as $productId => $item) {
        $quantity = isset($item['quantity']) ? $item['quantity'] : 1; // Default to 1 if quantity is not set
        $totalAmount += $quantity * $item['price'];
    }

    // Insert order into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO orders (address, instructions, delivery_option, schedule, payment_method, total_amount) 
                               VALUES (:address, :instructions, :delivery_option, :schedule, :payment_method, :total_amount)");
        $stmt->execute([
            ':address' => $address,
            ':instructions' => $instructions,
            ':delivery_option' => $deliveryOption,
            ':schedule' => $schedule,
            ':payment_method' => $paymentMethod,
            ':total_amount' => $totalAmount
        ]);

        // Get the order ID
        $orderId = $pdo->lastInsertId();

        // Insert each product in the cart as part of the order
        foreach ($_SESSION['cart'] as $productId => $item) {
            $quantity = isset($item['quantity']) ? $item['quantity'] : 1; // Default to 1 if quantity is not set
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                                   VALUES (:order_id, :product_id, :quantity, :price)");
            $stmt->execute([
                ':order_id' => $orderId,
                ':product_id' => $productId,
                ':quantity' => $quantity,
                ':price' => $item['price']
            ]);
        }

        // Clear the cart after the order is placed
        unset($_SESSION['cart']);

        // Redirect to a thank you page or order confirmation page
        header('Location: order_confirmation.php');
        exit;

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
