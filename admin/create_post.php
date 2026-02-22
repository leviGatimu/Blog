<?php
// admin/create_post.php
require '../includes/auth.php';
require '../includes/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write New Post - Levi's Admin.</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.tiny.cloud/1/i69c95x51v30gabihq8civm4i39tlacgcg5g09s6iolbbunq/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
    <style>
        /* Custom styling to make the drag-drop area look active */
        .dragover { border-color: #3b82f6; background-color: #eff6ff; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 shadow-sm fixed w-full top-0 z-50">
        <div class="flex items-center gap-4">
            <a href="index.php" class="text-gray-500 hover:text-blue-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-xl font-bold">Write New Post</h1>
        </div>
        <div>
            <button onclick="document.getElementById('postForm').submit();" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Save Post
            </button>
        </div>
    </header>

    <main class="pt-24 pb-12 px-8 max-w-7xl mx-auto">
        <form id="postForm" action="process_post.php" method="POST" enctype="multipart/form-data" class="flex flex-col lg:flex-row gap-8">
            
            <div class="flex-1 space-y-6">
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Post Title</label>
                    <input type="text" id="title" name="title" required 
                           placeholder="e.g., Building My First FPV Drone..." 
                           class="w-full text-2xl font-bold px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    
                    <div class="mt-3 flex items-center gap-2 text-sm text-gray-500">
                        <span>URL: yoursite.com/blog/</span>
                        <input type="text" id="slug" name="slug" required 
                               class="bg-transparent border-b border-dashed border-gray-300 focus:border-blue-500 outline-none w-1/2 text-gray-700 py-1"
                               placeholder="auto-generated-slug">
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Content</label>
                    <textarea id="editor" name="content" class="w-full h-96"></textarea>
                </div>
            </div>

            <div class="w-full lg:w-96 space-y-6">
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Publishing</h3>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="status" value="draft" checked class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700 font-medium">Save as Draft</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="status" value="published" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700 font-medium">Publish Immediately</span>
                        </label>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Featured Image</h3>
                    
                    <div id="drop-area" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:bg-gray-50 transition">
                        <input type="file" id="imageInput" name="featured_image" accept="image/*" class="hidden">
                        
                        <div id="preview-container" class="hidden mb-4">
                            <img id="image-preview" src="" alt="Preview" class="w-full h-auto rounded-lg shadow-sm">
                        </div>

                        <div id="upload-text">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-sm text-gray-600 font-medium">Click or drag image to upload</p>
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 5MB</p>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </main>

    <script>
        // 1. Initialize TinyMCE Editor
        tinymce.init({
            selector: '#editor',
            plugins: 'advlist autolink lists link image charmap preview anchor pagebreak code visualblocks visualchars wordcount',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image code',
            height: 500,
            menubar: false,
            branding: false,
            // This allows the editor to look very clean
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 16px; line-height: 1.6; }'
        });

        // 2. Auto-generate Slug from Title
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');

        titleInput.addEventListener('input', function() {
            const title = this.value;
            const slug = title.toLowerCase()
                              .replace(/[^a-z0-9\s-]/g, '') // Remove special chars
                              .replace(/\s+/g, '-')         // Replace spaces with hyphens
                              .replace(/-+/g, '-');         // Remove multiple consecutive hyphens
            slugInput.value = slug;
        });

        // 3. Featured Image Drag & Drop Preview
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('imageInput');
        const previewContainer = document.getElementById('preview-container');
        const imagePreview = document.getElementById('image-preview');
        const uploadText = document.getElementById('upload-text');

        // Clicking the box opens the file dialog
        dropArea.addEventListener('click', () => fileInput.click());

        // Handle the file selection
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    uploadText.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        // Drag and drop effects
        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('dragover');
        });
        dropArea.addEventListener('dragleave', () => dropArea.classList.remove('dragover'));
        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('dragover');
            
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                // Trigger the change event manually
                const event = new Event('change');
                fileInput.dispatchEvent(event);
            }
        });
    </script>
</body>
</html>