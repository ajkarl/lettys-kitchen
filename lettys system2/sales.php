<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<h2 class="text-2xl font-bold mb-4">Sales</h2>

<?php
$stmt = $pdo->query("SELECT sales.id, products.name AS product_name, sales.quantity, sales.price, sales.sale_date 
                     FROM sales JOIN products ON sales.product_id = products.id");
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
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
