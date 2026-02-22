<?php
// admin/process_edit.php
require '../includes/auth.php';
require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']);
    $content = $_POST['content'];
    $status = $_POST['status'];
    $existing_image = $_POST['existing_image']; // The image we already had

    if (empty($id) || empty($title) || empty($slug) || empty($content)) {
        die("Error: Required fields are missing.");
    }

    // 1. Assume we are keeping the existing image initially
    $featured_image = $existing_image;

    // 2. Check if a NEW image was uploaded
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $file_tmp_path = $_FILES['featured_image']['tmp_name'];
        $file_name = $_FILES['featured_image']['name'];
        $file_size = $_FILES['featured_image']['size'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($file_extension, $allowed_extensions) && $file_size < 5000000) {
            $new_file_name = uniqid('img_', true) . '.' . $file_extension;
            $destination = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp_path, $destination)) {
                $featured_image = $new_file_name; // Update to the new image name
                
                // Optional: Delete the old image from the server to save space
                if (!empty($existing_image) && file_exists($upload_dir . $existing_image)) {
                    unlink($upload_dir . $existing_image);
                }
            } else {
                die("Error moving the new uploaded file.");
            }
        } else {
            die("Error: Invalid file type or file too large.");
        }
    }

    // 3. Update the database
    try {
        $stmt = $pdo->prepare("UPDATE posts SET title = :title, slug = :slug, content = :content, featured_image = :image, status = :status WHERE id = :id");
        
        $stmt->execute([
            ':title' => $title,
            ':slug' => $slug,
            ':content' => $content,
            ':image' => $featured_image,
            ':status' => $status,
            ':id' => $id
        ]);

        header("Location: index.php?msg=post_updated");
        exit;

    } catch(PDOException $e) {
        if ($e->getCode() == 23000) {
            die("Error: The URL slug '$slug' is already used by another post.");
        } else {
            die("Database Error: " . $e->getMessage());
        }
    }
} else {
    header("Location: index.php");
    exit;
}
?>