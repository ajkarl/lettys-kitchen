<?php
include 'db.php';
include 'header.php';
?>

<h2 class="text-2xl font-bold mb-4">Products</h2>

<!-- Add Product -->
<form method="POST" enctype="multipart/form-data" class="mb-6 bg-white p-4 rounded shadow">
    <h3 class="text-lg font-bold mb-2">Add Product</h3>
    <input type="text" name="name" placeholder="Product Name" required class="w-full mb-2 p-2 border rounded">
    <textarea name="description" placeholder="Product Description" required class="w-full mb-2 p-2 border rounded"></textarea>
    <input type="number" step="0.01" name="price" placeholder="Price" required class="w-full mb-2 p-2 border rounded">
    <label class="block mb-2">Upload Image:</label>
    <input type="file" name="image" accept="image/*" class="w-full mb-2 p-2 border rounded">
    <button type="submit" name="add_product" class="bg-red-500 text-white px-4 py-2 rounded">Add</button>
</form>

<?php
// Add Product Handler
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
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
    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $image_url]);
    echo "<p class='text-green-500'>Product added successfully!</p>";
}

// Update Product Handler
if (isset($_POST['edit_product'])) {
    $id = $_POST['product_id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE products SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    echo "<p class='text-green-500'>Product updated successfully!</p>";
}

// Delete Product Handler
if (isset($_POST['delete_product'])) {
    $productId = $_POST['delete_product_id'];

    // Check if the product has associated sales
    $salesCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM sales WHERE product_id = ?");
    $salesCheckStmt->execute([$productId]);
    $hasSales = $salesCheckStmt->fetchColumn() > 0;

    if ($hasSales) {
        echo "<p class='text-red-500'>Cannot delete product with associated sales.</p>";
    } else {
        // Delete product
        $deleteStmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $deleteStmt->execute([$productId]);
        echo "<p class='text-green-500'>Product deleted successfully!</p>";
    }
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
            <th class="border px-4 py-2">Description</th>
            <th class="border px-4 py-2">Price</th>
            <th class="border px-4 py-2">Image</th>
            <th class="border px-4 py-2">Status</th>
            <th class="border px-4 py-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td class="border px-4 py-2"><?= $product['id'] ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($product['name']) ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($product['description']) ?></td>
                <td class="border px-4 py-2">₱<?= number_format($product['price'], 2) ?></td>
                <td class="border px-4 py-2">
                    <?php if (!empty($product['image_url'])): ?>
                        <img src="<?= $product['image_url'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-16 h-16 object-cover rounded">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td class="border px-4 py-2"><?= $product['status'] ?? 'Available' ?></td>
                <td class="border px-4 py-2">
                    <form method="POST" class="inline">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <select name="status" class="border rounded p-1">
                            <option value="Available" <?= ($product['status'] ?? 'Available') === 'Available' ? 'selected' : '' ?>>Available</option>
                            <option value="Sold Out" <?= ($product['status'] ?? '') === 'Sold Out' ? 'selected' : '' ?>>Sold Out</option>
                            <option value="Not Available" <?= ($product['status'] ?? '') === 'Not Available' ? 'selected' : '' ?>>Not Available</option>
                        </select>
                        <button type="submit" name="edit_product" class="bg-blue-500 text-white px-2 py-1 rounded">Update</button>
                    </form>

                    <!-- Delete Button -->
                    <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                        <input type="hidden" name="delete_product_id" value="<?= $product['id'] ?>">
                        <button type="submit" name="delete_product" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
