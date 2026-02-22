<?php
// admin/index.php
require '../includes/auth.php'; // This locks down the page!
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">My Admin Panel</h1>
        <a href="logout.php" class="text-red-500 hover:text-red-700 font-medium">Logout</a>
    </nav>

    <div class="max-w-7xl mx-auto px-6 py-10">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Hello, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>! 👋</h2>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <p class="text-gray-600">You are successfully logged in. This is where we will build the cool features.</p>
        </div>
    </div>
</body>
</html>