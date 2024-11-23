<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['product_name'];
    $price = $_POST['product_price'];
    $image = $_POST['product_image'];

    $stmt = $pdo->prepare("INSERT INTO products (name, price, image) VALUES (:name, :price, :image)");
    $stmt->execute([
        ':name' => $name,
        ':price' => $price,
        ':image' => $image,
    ]);

    header('Location: admin.php?success=1');
    exit;
}
?>
