<?php
// admin/delete_post.php
require '../includes/auth.php'; // Protect this script!
require '../includes/db_connect.php';

// Check if an ID was passed in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $post_id = $_GET['id'];

    try {
        // 1. First, fetch the post so we know the name of the image we need to delete
        $stmt = $pdo->prepare("SELECT featured_image FROM posts WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $post_id]);
        $post = $stmt->fetch();

        if ($post) {
            // 2. Delete the image file from the server if it exists
            if (!empty($post['featured_image'])) {
                $image_path = '../uploads/' . $post['featured_image'];
                if (file_exists($image_path)) {
                    unlink($image_path); // This is the PHP command to delete a file
                }
            }

            // 3. Delete the post from the database
            $delete_stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
            $delete_stmt->execute([':id' => $post_id]);

            // 4. Redirect back to manage posts with a success message
            header("Location: manage_posts.php?msg=deleted");
            exit;
        } else {
            die("Error: Post not found.");
        }

    } catch(PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
} else {
    // If no ID was provided, just send them back
    header("Location: manage_posts.php");
    exit;
}
?>