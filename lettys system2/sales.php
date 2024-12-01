<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<h2 class="text-2xl font-bold mb-4">Sales</h2>

<?php
try {
    // Fetch total sales from admin_total_sales
    $stmt = $pdo->prepare("SELECT total_sales FROM admin_total_sales WHERE id = 1");
    $stmt->execute();
    $totalSalesData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get total sales or set to 0 if not found
    $totalSales = $totalSalesData ? $totalSalesData['total_sales'] : 0;

    // Fetch individual sales records
    $stmt = $pdo->prepare("
        SELECT sales.id, products.name AS product_name, sales.quantity, sales.price, sales.sale_date 
        FROM sales 
        JOIN products ON sales.product_id = products.id
    ");
    $stmt->execute();
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo "<p class='text-red-500'>Error: Could not fetch data. " . htmlspecialchars($e->getMessage()) . "</p>";
    $totalSales = 0;
    $sales = [];
}
?>

<!-- Display Total Sales -->
<div class="bg-gray-100 p-4 rounded shadow mb-6">
    <h3 class="text-xl font-bold mb-2">Total Sales</h3>
    <p class="text-lg text-green-600">₱<?= number_format($totalSales, 2) ?></p>
</div>

<!-- Display Sales Table -->
<div class="overflow-x-auto">
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
            <?php if (!empty($sales)): ?>
                <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td class="border px-4 py-2"><?= $sale['id'] ?></td>
                        <td class="border px-4 py-2"><?= htmlspecialchars($sale['product_name']) ?></td>
                        <td class="border px-4 py-2"><?= $sale['quantity'] ?></td>
                        <td class="border px-4 py-2">$<?= number_format($sale['price'], 2) ?></td>
                        <td class="border px-4 py-2"><?= $sale['sale_date'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center border px-4 py-2 text-red-500">No sales data available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
