<?php
session_start();
$conn = new mysqli("localhost", "root", "", "letty_kitchen1");

// Check for connection error
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Query the products table
$sql = "SELECT * FROM products LIMIT 3";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letty's Kitchen</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="navbar">
        <div class="container">
            <h1 class="logo">Letty's Kitchen</h1>
            <nav>
                <ul class="nav-links">
                    <li><a href="#">Home</a></li>
                    <li><a href="index.php">Menu</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h2>Delicious Filipino Dishes Delivered to Your Doorstep</h2>
            <p>Enjoy the authentic taste of Filipino cuisine, crafted with love and the finest ingredients.</p>
            <a href="index.php" class="btn">Explore Our Menu</a>
        </div>
    </section>

    <section class="featured">
        <div class="container">
            <h2>Featured Dishes</h2>
            <div class="product-grid">
                <?php 
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) { ?>
                        <div class="product-card">
                            <img src="<?php echo htmlspecialchars(isset($row['image_path']) ? $row['image_path'] : 'default.jpg'); ?>" 
                                 alt="<?php echo htmlspecialchars(isset($row['name']) ? $row['name'] : 'Unnamed Dish'); ?>" 
                                 class="product-image">
                            <h3><?php echo htmlspecialchars(isset($row['name']) ? $row['name'] : 'Unnamed Dish'); ?></h3>
                            <p><?php echo htmlspecialchars(isset($row['description']) ? $row['description'] : 'Description not available'); ?></p>
                            <p class="price">$<?php echo isset($row['price']) ? number_format($row['price'], 2) : '0.00'; ?></p>
                            <a href="add_to_cart.php?product_id=<?php echo isset($row['id']) ? $row['id'] : '#'; ?>" class="btn">Add to Cart</a>
                        </div>
                <?php } 
                } else { ?>
                    <p>No featured dishes available at the moment.</p>
                <?php } ?>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Letty's Kitchen. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
