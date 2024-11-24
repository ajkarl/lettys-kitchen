<?php
// Start the session
session_start();
require 'db.php'; // Include database connection

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input and trim spaces
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate input
    if (empty($name) || empty($email) || empty($contact) || empty($address) || empty($password) || empty($confirmPassword)) {
        $message = "<p class='text-red-500 text-center'>All fields are required!</p>";
    } elseif ($password !== $confirmPassword) {
        $message = "<p class='text-red-500 text-center'>Passwords do not match!</p>";
    } else {
        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $emailExists = $stmt->fetchColumn();

        if ($emailExists) {
            $message = "<p class='text-red-500 text-center'>This email is already taken. Please try a different one.</p>";
        } else {
            // Insert new user into the database
            $stmt = $pdo->prepare("INSERT INTO users (name, email, contact, address, password) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $contact, $address, $hashedPassword])) {
                $message = "<p class='text-green-500 text-center'>Signup successful! You can now <a href='login.php' class='underline'>log in</a>.</p>";
            } else {
                $message = "<p class='text-red-500 text-center'>Error during signup. Please try again.</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
</head>
<body class="flex items-center justify-center min-h-screen bg-white">
    <div class="w-full max-w-md p-8 space-y-6">
        <div class="text-center">
            <h1 class="text-3xl font-bold">Sign Up</h1>
            <p class="text-gray-500">Create your account</p>
        </div>

        <!-- Display Message -->
        <?php if (!empty($message)) echo $message; ?>

        <!-- Signup Form -->
        <form method="POST" action="signup.php" class="space-y-4">
            <div>
                <input type="text" name="name" placeholder="Name" required class="w-full px-4 py-3 border rounded-full focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <input type="email" name="email" placeholder="Email Address" required class="w-full px-4 py-3 border rounded-full focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <input type="text" name="contact" placeholder="Contact Number" required class="w-full px-4 py-3 border rounded-full focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <input type="text" name="address" placeholder="Address" required class="w-full px-4 py-3 border rounded-full focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-3 border rounded-full focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required class="w-full px-4 py-3 border rounded-full focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div class="text-center">
                <button type="submit" class="w-full px-4 py-3 text-white bg-red-500 rounded-full hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500">Sign Up</button>
            </div>
        </form>

        <div class="text-center mt-8">
            <p class="text-4xl font-pacifico text-red-500">Letty's Kitchen</p>
        </div>
        <div class="text-center mt-4 space-y-2">
            <a href="login.php" class="w-full inline-block px-4 py-3 text-white bg-blue-500 rounded-full hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Log In</a>
            <a href="admin_login.php" class="w-full inline-block px-4 py-3 text-white bg-green-500 rounded-full hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500">Admin Log In</a>
        </div>
    </div>
</body>
</html>
