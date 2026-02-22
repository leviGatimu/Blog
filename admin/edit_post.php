<?php
// admin/edit_post.php
require '../includes/auth.php';
require '../includes/db_connect.php';

// 1. Check if we have a valid ID in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: No valid Post ID provided.");
}

$post_id = $_GET['id'];

// 2. Fetch the post from the database
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $post_id]);
$post = $stmt->fetch();

// 3. If the post doesn't exist, stop.
if (!$post) {
    die("Error: Post not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - Levi's Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        .dragover { border-color: #3b82f6; background-color: #eff6ff; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 shadow-sm fixed w-full top-0 z-50">
        <div class="flex items-center gap-4">
            <a href="index.php" class="text-gray-500 hover:text-blue-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-xl font-bold">Edit Post</h1>
        </div>
        <div>
            <button onclick="updatePost()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Update Post
            </button>
        </div>
    </header>

    <main class="pt-24 pb-12 px-8 max-w-7xl mx-auto">
        <form id="editForm" action="process_edit.php" method="POST" enctype="multipart/form-data" class="flex flex-col lg:flex-row gap-8">
            
            <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($post['featured_image']); ?>">

            <div class="flex-1 space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Post Title</label>
                    <input type="text" id="title" name="title" required 
                           value="<?php echo htmlspecialchars($post['title']); ?>"
                           class="w-full text-2xl font-bold px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    
                    <div class="mt-3 flex items-center gap-2 text-sm text-gray-500">
                        <span>URL: yoursite.com/blog/</span>
                        <input type="text" id="slug" name="slug" required 
                               value="<?php echo htmlspecialchars($post['slug']); ?>"
                               class="bg-transparent border-b border-dashed border-gray-300 focus:border-blue-500 outline-none w-1/2 text-gray-700 py-1">
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Content</label>
                    <textarea id="editor" name="content" class="w-full h-96"><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>
            </div>

            <div class="w-full lg:w-96 space-y-6">
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Publishing</h3>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="status" value="draft" <?php echo ($post['status'] == 'draft') ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700 font-medium">Save as Draft</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="status" value="published" <?php echo ($post['status'] == 'published') ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700 font-medium">Publish</span>
                        </label>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Featured Image</h3>
                    
                    <div id="drop-area" class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden">
                        <input type="file" id="imageInput" name="featured_image" accept="image/*" class="hidden">
                        
                        <div id="preview-container" class="<?php echo empty($post['featured_image']) ? 'hidden' : ''; ?> mb-2">
                            <img id="image-preview" src="<?php echo !empty($post['featured_image']) ? '../uploads/' . htmlspecialchars($post['featured_image']) : ''; ?>" alt="Preview" class="w-full h-auto rounded-lg shadow-sm">
                        </div>

                        <div id="upload-text" class="<?php echo !empty($post['featured_image']) ? 'mt-4' : ''; ?>">
                            <p class="text-sm text-gray-600 font-medium"><?php echo !empty($post['featured_image']) ? 'Click to replace image' : 'Click or drag image to upload'; ?></p>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </main>

    <script>
        tinymce.init({
            selector: '#editor',
            plugins: 'advlist autolink lists link image charmap preview anchor pagebreak code visualblocks visualchars wordcount',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image code',
            height: 500,
            menubar: false,
            branding: false,
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, sans-serif; font-size: 16px; line-height: 1.6; }'
        });

        // The auto-slug is intentionally removed here so you don't accidentally break URLs 
        // of already published posts just by editing the title! You can still edit the slug manually.

        function updatePost() {
            tinymce.triggerSave();
            const form = document.getElementById('editForm');
            if (form.reportValidity()) { 
                form.submit();
            }
        }

        // Image Drag & Drop Logic
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('imageInput');
        const previewContainer = document.getElementById('preview-container');
        const imagePreview = document.getElementById('image-preview');

        dropArea.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        dropArea.addEventListener('dragover', (e) => { e.preventDefault(); dropArea.classList.add('dragover'); });
        dropArea.addEventListener('dragleave', () => dropArea.classList.remove('dragover'));
        dropArea.addEventListener('drop', (e) => {
            e.preventDefault(); dropArea.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>