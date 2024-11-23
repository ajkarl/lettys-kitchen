<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<h2 class="text-2xl font-bold mb-4">Products</h2>

<!-- Add Product -->
<form method="POST" class="mb-6 bg-white p-4 rounded shadow">
    <h3 class="text-lg font-bold mb-2">Add Product</h3>
    <input type="text" name="name" placeholder="Product Name" required class="w-full mb-2 p-2 border rounded">
    <input type="number" step="0.01" name="price" placeholder="Price" required class="w-full mb-2 p-2 border rounded">
    <input type="text" name="image_url" placeholder="Image URL (optional)" class="w-full mb-2 p-2 border rounded">
    <button type="submit" name="add_product" class="bg-red-500 text-white px-4 py-2 rounded">Add</button>
</form>

<?php
// Add Product Handler
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'] ?: null;

    $stmt = $pdo->prepare("INSERT INTO products (name, price, image_url) VALUES (?, ?, ?)");
    $stmt->execute([$name, $price, $image_url]);
    echo "<p class='text-green-500'>Product added successfully!</p>";
}

// Display Products
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table class="w-full bg-white rounded shadow">
    <thead>
        <tr>
            <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">Name</th>
            <th class="border px-4 py-2">Price</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td class="border px-4 py-2"><?= $product['id'] ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($product['name']) ?></td>
                <td class="border px-4 py-2">$<?= number_format($product['price'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
