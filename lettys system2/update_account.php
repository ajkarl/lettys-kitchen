<?php
session_start();
require 'db.php'; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Get the updated user data from the form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];

    // Update user data in the database
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, contact = ?, address = ? WHERE id = ?");
    $stmt->execute([$name, $email, $contact, $address, $user_id]);

    // Redirect back to the account page
    header('Location: account.php');
    exit();
}
?>
