<?php
// public/index.php
require '../includes/db_connect.php';

// Fetch ONLY published posts, ordered by the newest first
try {
    $stmt = $pdo->query("SELECT title, slug, content, featured_image, created_at 
                         FROM posts 
                         WHERE status = 'published' 
                         ORDER BY created_at DESC");
    $posts = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Error loading posts: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Levi's Tech & Dev Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                    <a href="../admin/index.php" class="text-gray-500 hover:text-blue-600 text-sm font-medium transition-colors">Admin Login</a>
                </div>
            </div>
        </div>
    </nav>

    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <h1 class="text-5xl font-extrabold text-gray-900 tracking-tight mb-4">
                Exploring Code, Tech & <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Aviation</span>.
            </h1>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                Welcome to my personal corner of the internet where I share my projects, tutorials, and thoughts on software development and beyond.
            </p>
        </div>
    </header>

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
        
        <?php if (empty($posts)): ?>
            <div class="text-center py-20">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2"></path></svg>
                <h2 class="text-2xl font-bold text-gray-900">No posts yet!</h2>
                <p class="text-gray-500 mt-2">Check back soon for new content.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <?php foreach ($posts as $post): ?>
                    <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:-translate-y-1 hover:shadow-xl transition-all duration-300 flex flex-col">
                        
                        <a href="post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="block overflow-hidden aspect-video bg-gray-100">
                            <?php if (!empty($post['featured_image'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            <?php endif; ?>
                        </a>

                        <div class="p-6 flex flex-col flex-grow">
                            <div class="flex items-center text-sm text-gray-500 mb-3 gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <time datetime="<?php echo $post['created_at']; ?>">
                                    <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                                </time>
                            </div>
                            
                            <a href="post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="block mt-2">
                                <h3 class="text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors line-clamp-2">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </h3>
                            </a>
                            
                            <p class="mt-3 text-gray-600 line-clamp-3 flex-grow">
                                <?php 
                                    // Strip the TinyMCE HTML tags so the excerpt is just plain text
                                    $plain_text = strip_tags($post['content']);
                                    // Truncate to 120 characters and add an ellipsis
                                    echo strlen($plain_text) > 120 ? substr($plain_text, 0, 120) . '...' : $plain_text;
                                ?>
                            </p>

                            <div class="mt-6 pt-4 border-t border-gray-100">
                                <a href="post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="text-blue-600 font-semibold hover:text-blue-800 flex items-center gap-1 transition-colors">
                                    Read Article 
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
                
            </div>
        <?php endif; ?>

    </main>

    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex justify-center">
            <p class="text-gray-500 text-sm text-center">
                &copy; <?php echo date('Y'); ?> Levi's Blog. All rights reserved. Built from scratch with PHP.
            </p>
        </div>
    </footer>

</body>
</html>