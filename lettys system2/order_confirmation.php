<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<header class="bg-red-600 text-white py-4">
    <div class="container mx-auto text-center">
        <h1 class="text-2xl font-bold">Order Confirmation</h1>
    </div>
</header>

<main class="container mx-auto py-8 px-6 text-center">
    <h2 class="text-xl font-semibold">Thank you for your order!</h2>
    <p>Your order has been successfully placed. We will contact you shortly for further details.</p>
    <a href="index.php" class="mt-4 inline-block text-blue-600">Back to Home</a>
</main>

<footer class="bg-red-600 text-white py-4">
    <div class="container mx-auto text-center">
        <p>© <?php echo date('Y'); ?> Letty's Kitchen. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
