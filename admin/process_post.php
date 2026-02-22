<?php
// admin/process_post.php
session_start();
require '../includes/auth.php'; // Ensure only you can run this script
require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Grab the text data
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']);
    $content = $_POST['content']; // This contains the rich HTML from TinyMCE
    $status = $_POST['status'];

    // Basic validation
    if (empty($title) || empty($slug) || empty($content)) {
        die("Error: Title, slug, and content are required.");
    }

    // 2. Handle the Image Upload
    $featured_image = null; // Default if no image is uploaded

    // Check if an image was uploaded without errors
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        
        // Auto-create the uploads folder if you haven't made it yet
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_tmp_path = $_FILES['featured_image']['tmp_name'];
        $file_name = $_FILES['featured_image']['name'];
        $file_size = $_FILES['featured_image']['size'];
        
        // Get the file extension
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Allowed extensions for security (no .php or .exe files allowed!)
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($file_extension, $allowed_extensions)) {
            // Ensure the file isn't too large (Max 5MB)
            if ($file_size < 5000000) {
                // Create a unique filename (e.g., img_64b1f2.jpg)
                $new_file_name = uniqid('img_', true) . '.' . $file_extension;
                $destination = $upload_dir . $new_file_name;

                // Move the file from the server's temporary folder to your uploads folder
                if (move_uploaded_file($file_tmp_path, $destination)) {
                    $featured_image = $new_file_name;
                } else {
                    die("Error moving the uploaded file.");
                }
            } else {
                die("Error: File size exceeds the 5MB limit.");
            }
        } else {
            die("Error: Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.");
        }
    }

    // 3. Save everything to the Database safely
    try {
        $stmt = $pdo->prepare("INSERT INTO posts (title, slug, content, featured_image, status) 
                               VALUES (:title, :slug, :content, :image, :status)");
        
        $stmt->execute([
            ':title' => $title,
            ':slug' => $slug,
            ':content' => $content, // Storing the HTML directly
            ':image' => $featured_image,
            ':status' => $status
        ]);

        // 4. Success! Redirect back to the dashboard
        header("Location: index.php?msg=post_created");
        exit;

    } catch(PDOException $e) {
        // If the slug already exists, the database will throw an error code 23000
        if ($e->getCode() == 23000) {
            die("Error: The URL slug '$slug' is already in use. Please choose another title.");
        } else {
            die("Database Error: " . $e->getMessage());
        }
    }
} else {
    // Kick out anyone trying to load this file directly in their browser
    header("Location: create_post.php");
    exit;
}
?>