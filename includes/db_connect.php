<?php
// includes/db_connect.php

// 1. Set the default timezone for accurate blog post timestamps
date_default_timezone_set('Africa/Kigali');

// 2. Database credentials (update these when you move to a live server)
$host = 'localhost';
$dbname = 'malvyn_blog';
$db_user = 'root'; 
$db_pass = ''; 

try {
    // 3. Create the PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_user, $db_pass);
    
    // 4. Set PDO to throw exceptions on errors (great for debugging)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 5. Set the default fetch mode to associative array for cleaner code later
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    // If the connection fails, stop the script and show an error
    die("Database Connection Failed: " . $e->getMessage());
}
?>