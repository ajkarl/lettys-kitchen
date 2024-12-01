<?php
session_start();
require 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details from the database
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT name, email, contact, address FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-white">
    <div class="bg-red-600 p-4 flex items-center">
        <i class="fas fa-user-circle text-white text-4xl mr-2"></i>
        <h1 class="text-white text-2xl font-bold">Account</h1>
        <div class="ml-auto">
            <a href="index.php" class="text-white text-lg flex items-center">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>
    <div class="p-6">
        <h2 class="text-lg font-bold mb-4">Account Information</h2>
        <form action="update_account.php" method="POST" class="space-y-4">
            <div class="mb-4">
                <label class="block text-gray-700 font-bold">Name</label>
                <input 
                    type="text" 
                    name="name" 
                    value="<?php echo htmlspecialchars($user['name']); ?>" 
                    class="border border-gray-300 p-2 w-full rounded" 
                    required
                >
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    value="<?php echo htmlspecialchars($user['email']); ?>" 
                    class="border border-gray-300 p-2 w-full rounded" 
                    required
                >
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold">Contact</label>
                <input 
                    type="text" 
                    name="contact" 
                    value="<?php echo htmlspecialchars($user['contact']); ?>" 
                    class="border border-gray-300 p-2 w-full rounded" 
                    required
                >
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold">Address</label>
                <input 
                    type="text" 
                    name="address" 
                    value="<?php echo htmlspecialchars($user['address']); ?>" 
                    class="border border-gray-300 p-2 w-full rounded" 
                    required
                >
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 text-white bg-red-600 rounded-full hover:bg-red-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</body>
</html>
