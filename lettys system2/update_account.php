<?php
session_start();
require 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Process form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $contact = htmlspecialchars($_POST['contact']);
    $address = htmlspecialchars($_POST['address']);

    try {
        // Update user details in the database
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, contact = ?, address = ? WHERE id = ?");
        $stmt->execute([$name, $email, $contact, $address, $user_id]);

        // Redirect to account page with success message
        $_SESSION['success'] = "Account updated successfully!";
        header("Location: account.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating account: " . $e->getMessage());
    }
}
