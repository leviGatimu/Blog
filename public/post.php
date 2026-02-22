<?php
// public/post.php
require '../includes/db_connect.php';

// 1. Check if a slug was provided in the URL
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header("Location: index.php");
    exit;
}

$slug = trim($_GET['slug']);

// 2. Fetch the post from the database (Make sure it is published!)
try {
    $stmt = $pdo->prepare("SELECT title, content, featured_image, created_at 
                           FROM posts 
                           WHERE slug = :slug AND status = 'published' 
                           LIMIT 1");
    $stmt->execute([':slug' => $slug]);
    $post = $stmt->fetch();

    // 3. If no post is found, show a clean 404 message
    if (!$post) {
        die("
            <div style='font-family: sans-serif; text-align: center; margin-top: 100px;'>
                <h1 style='font-size: 50px; color: #3b82f6;'>404</h1>
                <h2>Post Not Found</h2>
                <p>The article you are looking for doesn't exist or is still a draft.</p>
                <a href='index.php' style='color: #3b82f6; text-decoration: none;'>&larr; Back to Home</a>
            </div>
        ");
    }
} catch(PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Levi.dev</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased flex flex-col min-h-screen">

    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center gap-2">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    <a href="index.php" class="font-bold text-2xl tracking-tight text-gray-900">Levi<span class="text-blue-600">.dev</span></a>
                </div>
                <div>
                    <a href="index.php" class="text-gray-500 hover:text-blue-600 text-sm font-medium transition-colors flex items-center gap-1">
                        &larr; Back to Articles
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
        
        <article class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            
            <?php if (!empty($post['featured_image'])): ?>
                <div class="w-full h-[400px] overflow-hidden bg-gray-100 relative">
                    <img src="../uploads/<?php echo htmlspecialchars($post['featured_image']); ?>" 
                         alt="<?php echo htmlspecialchars($post['title']); ?>" 
                         class="w-full h-full object-cover">
                </div>
            <?php endif; ?>

            <div class="p-8 md:p-12 lg:p-16">
                
                <div class="flex items-center text-sm text-gray-500 mb-6 font-medium tracking-wide uppercase">
                    <time datetime="<?php echo $post['created_at']; ?>">
                        Published on <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                    </time>
                    <span class="mx-3">&bull;</span>
                    <span>By Levi</span>
                </div>

                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-8">
                    <?php echo htmlspecialchars($post['title']); ?>
                </h1>

                <div class="prose prose-lg prose-blue max-w-none text-gray-700">
                    <?php echo $post['content']; ?>
                </div>

            </div>
        </article>

    </main>

    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex justify-center">
            <p class="text-gray-500 text-sm text-center">
                &copy; <?php echo date('Y'); ?> Levi's Blog. All rights reserved.
            </p>
        </div>
    </footer>

</body>
</html>