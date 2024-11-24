<?php
session_start(); // Start the session to track the user's login status

require 'db.php'; // Include the database connection (make sure db.php is correct)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the email and password from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email and password are provided
    if (empty($email) || empty($password)) {
        echo "<p class='text-red-500'>Email and password are required!</p>";
    } else {
        // Query the database to find the user with the provided email
        $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists and password matches
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables for the user
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            // Redirect to index.php after successful login
            header('Location: index.php');
            exit();  // Always call exit after header to stop further script execution
        } else {
            echo "<p class='text-red-500'>Invalid credentials. Please try again.</p>";
        }
    }
}
?>
