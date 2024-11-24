<!DOCTYPE html>
<html lang="en">
<head>
    <title>Letty's Kitchen - Log In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <style>
        .logo-font {
            font-family: 'Pacifico', cursive;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-white">
    <div class="text-center">
        <h1 class="text-5xl text-red-600 logo-font">Letty's Kitchen</h1>
        <p class="text-gray-600 mt-2">Dubinan East, Santiago City</p>
        <div class="mt-10">
            <h2 class="text-3xl font-semibold">Log In</h2>
            <form method="POST" action="login_process.php" class="mt-6 space-y-4">
                <div>
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="Email Address" 
                        class="w-full px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-red-600"
                        required
                    >
                </div>
                <div>
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Password" 
                        class="w-full px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-red-600"
                        required
                    >
                </div>
                <div>
                    <button type="submit" class="w-full px-4 py-2 text-white bg-red-600 rounded-full hover:bg-red-700">
                        Log In
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
