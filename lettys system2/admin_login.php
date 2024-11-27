<?php
session_start();
require 'db.php'; // Include the database connection

// Process Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username && $password) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if ($admin && hash('sha256', $password) === $admin['password']) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header('Location: admin.php');
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error = "Login failed: " . $e->getMessage();
        }
    } else {
        $error = "Please enter both username and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center">Admin Login</h2>
            <?php if (!empty($error)): ?>
                <p class="text-red-500 text-sm mb-4"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-4">
                    <label for="username" class="block text-sm font-bold mb-2">Username</label>
                    <input type="text" id="username" name="username" class="w-full p-3 border rounded" required>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-sm font-bold mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full p-3 border rounded" required>
                </div>
                <button type="submit" name="login" class="w-full bg-red-600 text-white py-2 rounded">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
