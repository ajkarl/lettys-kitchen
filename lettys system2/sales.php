<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<h2 class="text-2xl font-bold mb-4">Sales</h2>

<?php
// Fetch the total sales from the admin_total_sales table
$stmt = $pdo->query("SELECT total_sales FROM admin_total_sales WHERE id = 1");
$totalSalesData = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if data was returned
if ($totalSalesData) {
    // If data was returned, get the total sales value
    $totalSales = $totalSalesData['total_sales'];
} else {
    // If no data is returned, set the default value to 0
    $totalSales = 0;
    echo "<p class='text-red-500'>Error: Could not retrieve total sales. Default value is $0.00.</p>";
}

// Fetch sales data
$stmt = $pdo->query("SELECT sales.id, products.name AS product_name, sales.quantity, sales.price, sales.sale_date 
                     FROM sales JOIN products ON sales.product_id = products.id");
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Display Total Sales -->
<div class="bg-gray-100 p-4 rounded shadow mb-6">
    <h3 class="text-xl font-bold mb-2">Total Sales</h3>
    <p class="text-lg">$<?= number_format($totalSales, 2) ?></p>
</div>

<!-- Display Sales Table -->
<table class="w-full bg-white rounded shadow">
    <thead>
        <tr>
            <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">Product</th>
            <th class="border px-4 py-2">Quantity</th>
            <th class="border px-4 py-2">Price</th>
            <th class="border px-4 py-2">Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sales as $sale): ?>
            <tr>
                <td class="border px-4 py-2"><?= $sale['id'] ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($sale['product_name']) ?></td>
                <td class="border px-4 py-2"><?= $sale['quantity'] ?></td>
                <td class="border px-4 py-2">$<?= number_format($sale['price'], 2) ?></td>
                <td class="border px-4 py-2"><?= $sale['sale_date'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
