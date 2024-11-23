<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<h2 class="text-2xl font-bold mb-4">Products</h2>

<!-- Add Product -->
<form method="POST" enctype="multipart/form-data" class="mb-6 bg-white p-4 rounded shadow">
    <h3 class="text-lg font-bold mb-2">Add Product</h3>
    <input type="text" name="name" placeholder="Product Name" required class="w-full mb-2 p-2 border rounded">
    <input type="number" step="0.01" name="price" placeholder="Price" required class="w-full mb-2 p-2 border rounded">
    <label class="block mb-2">Upload Image:</label>
    <input type="file" name="image" accept="image/*" class="w-full mb-2 p-2 border rounded">
    <button type="submit" name="add_product" class="bg-red-500 text-white px-4 py-2 rounded">Add</button>
</form>

<?php
// Add Product Handler
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $uploadDir = 'uploads/';
        $uploadFilePath = $uploadDir . $imageName;

        // Ensure the upload directory exists
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file to the server's directory
        if (move_uploaded_file($imageTmpPath, $uploadFilePath)) {
            $image_url = $uploadFilePath; // Save the file path to the database
        } else {
            echo "<p class='text-red-500'>Error uploading the image.</p>";
            $image_url = null;
        }
    } else {
        $image_url = null;
    }

    // Insert product into the database
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
            <th class="border px-4 py-2">Image</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td class="border px-4 py-2"><?= $product['id'] ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($product['name']) ?></td>
                <td class="border px-4 py-2">$<?= number_format($product['price'], 2) ?></td>
                <td class="border px-4 py-2">
                    <?php if (!empty($product['image_url'])): ?>
                        <img src="<?= $product['image_url'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-16 h-16 object-cover rounded">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
