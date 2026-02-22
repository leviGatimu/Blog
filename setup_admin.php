<?php
// setup_admin.php
require 'includes/db_connect.php';

$admin_username = 'Malvyn'; // Your login name
$admin_password = 'MySuperSecretPassword123!'; // Change this to a strong password!

// Hash the password using PHP's built-in secure hashing algorithm
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

try {
    // Prepare the SQL statement to prevent injection
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    
    // Execute the statement with our variables
    $stmt->execute([
        ':username' => $admin_username,
        ':password' => $hashed_password
    ]);

    echo "Success! Admin user created securely. Please delete this file now for security.";

} catch(PDOException $e) {
    echo "Error creating user: " . $e->getMessage();
}
?>