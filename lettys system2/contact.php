<?php
$conn = new mysqli("localhost", "root", "", "letty_kitchen1");

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Letty's Kitchen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-red-600 text-white py-4">
        <div class="container mx-auto flex justify-between items-center px-4">
            <h1 class="text-2xl font-bold">Letty's Kitchen</h1>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="home page.php" class="hover:text-gray-300">Home</a></li>
                    <li><a href="index.php" class="hover:text-gray-300">Menu</a></li>
                    <li><a href="about.php" class="hover:text-gray-300">About Us</a></li>
                    <li><a href="contact.php" class="text-gray-200 underline">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8">Contact Us</h2>
            <p class="text-center text-gray-600 mb-8">We'd love to hear from you! Fill out the form below or reach us through our contact details.</p>
            
            <div class="max-w-2xl mx-auto bg-white shadow-md rounded-lg p-6">
                <form action="send_contact.php" method="POST" class="space-y-4">
                    <div>
                        <label for="name" class="block text-gray-700">Name</label>
                        <input type="text" id="name" name="name" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                    </div>
                    <div>
                        <label for="email" class="block text-gray-700">Email</label>
                        <input type="email" id="email" name="email" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                    </div>
                    <div>
                        <label for="message" class="block text-gray-700">Message</label>
                        <textarea id="message" name="message" rows="4" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600"></textarea>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>

            <div class="text-center mt-8">
                <h3 class="text-xl font-bold mb-2">Our Contact Details</h3>
                <p>Email: <a href="mailto:support@lettykitchen.com" class="text-red-600">support@lettykitchen.com</a></p>
                <p>Phone: +1 234 567 8900</p>
                <p>Address: 123 Food Street, Manila, Philippines</p>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4">
        <div class="container mx-auto text-center">
            <p>&copy; 2024 Letty's Kitchen. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
