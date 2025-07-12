<?php
require_once 'config.php';
require_login();

// Create the uploads directory if it doesn't exist
if (!is_dir(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0755, true);
}

// Get all uploaded images
$images = glob(UPLOADS_DIR . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP File Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">PHP File Manager</h1>
            <a href="logout.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Logout</a>
        </div>

        <!-- Upload Form -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-semibold mb-4">Upload Images</h2>
            <?php
            if (isset($_SESSION['upload_errors']) && !empty($_SESSION['upload_errors'])) {
                foreach ($_SESSION['upload_errors'] as $error) {
                    echo "<p class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>$error</p>";
                }
                unset($_SESSION['upload_errors']);
            } else if (isset($_SESSION['upload_success'])) {
                echo "<p class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4'>{$_SESSION['upload_success']}</p>";
                unset($_SESSION['upload_success']);
            }
            ?>
            <form action="upload.php" method="post" enctype="multipart/form-data" id="upload-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div id="drop-zone" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-500">
                    <p class="text-gray-500">Drag & drop files here or click to select</p>
                    <input type="file" name="files[]" id="file-input" class="hidden" multiple>
                </div>
                <div id="thumbnail-preview" class="mt-4 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4"></div>
                <button type="submit" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Upload</button>
            </form>
        </div>

        <!-- Image Gallery -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Uploaded Images</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php foreach ($images as $image): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                                <img src="<?php echo BASE_URL . $image; ?>" alt="" class="w-full h-32 object-cover">
                        <div class="p-4">
                            <div class="flex justify-between items-center">
                                                                <button onclick="copyLink('<?php echo BASE_URL . $image; ?>')" class="text-blue-500 hover:underline">Copy Link</button>
                                                                <a href="#" onclick="renameFile('<?php echo basename($image); ?>', '<?php echo $_SESSION['csrf_token']; ?>')" class="text-green-500 hover:underline">Rename</a>
                                <a href="delete.php?file=<?php echo basename($image); ?>&csrf_token=<?php echo $_SESSION['csrf_token']; ?>" onclick="return confirm('Are you sure you want to delete this image?')" class="text-red-500 hover:underline">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const thumbnailPreview = document.getElementById('thumbnail-preview');

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-blue-500');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-blue-500');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-500');
            fileInput.files = e.dataTransfer.files;
            handleFiles(fileInput.files);
        });

        fileInput.addEventListener('change', () => {
            handleFiles(fileInput.files);
        });

        function handleFiles(files) {
            thumbnailPreview.innerHTML = '';
            for (const file of files) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.classList.add('w-full', 'h-32', 'object-cover', 'rounded-lg');
                        thumbnailPreview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            }
        }

        function copyLink(link) {
            navigator.clipboard.writeText(link).then(() => {
                alert('Link copied to clipboard!');
            });
        }

                function renameFile(file, token) {
            const newName = prompt('Enter a new name for the file:', file);
            if (newName && newName !== file) {
                                window.location.href = `rename.php?file=${file}&new_name=${newName}&csrf_token=${token}`;
            }
        }
    </script>
</body>
</html>