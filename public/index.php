<?php
require_once 'config.php';
require_login();

// Create the uploads directory if it doesn't exist
if (!is_dir(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0755, true);
}

// Get all uploaded images
$images = glob(UPLOADS_DIR . '*.{jpg,jpeg,png,gif,webp,svg}', GLOB_BRACE);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Varabit Simple Image Host</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link href="dist/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Varabit Simple Image Host</h1>
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
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                <?php foreach ($images as $image): ?>
                    <div class="relative group bg-white rounded-lg shadow-md overflow-hidden transform transition-transform hover:scale-105">
                        <?php $isSvg = pathinfo(basename($image), PATHINFO_EXTENSION) === 'svg'; ?>
                        <div class="relative">
                            <img src="<?php echo BASE_URL . $image; ?>" alt="<?php echo basename($image); ?>" title="<?php echo basename($image); ?>" class="w-full h-64 <?php echo $isSvg ? 'object-contain bg-gray-100' : 'object-cover'; ?>">
                            <?php if ($isSvg): ?>
                                <div class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">SVG</div>
                            <?php endif; ?>
                        </div>
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 flex flex-col justify-center items-center transition-opacity p-4">
                            <div class="text-center">
                                <button onclick="copyLink('<?php echo BASE_URL . $image; ?>')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-2">Copy Link</button>
                                <div class="flex justify-center space-x-2">
                                    <a href="#" onclick="renameFile('<?php echo basename($image); ?>', '<?php echo $_SESSION['csrf_token']; ?>')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded">Rename</a>
                                    <a href="delete.php?file=<?php echo basename($image); ?>&csrf_token=<?php echo $_SESSION['csrf_token']; ?>" onclick="return confirm('Are you sure you want to delete this image?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded">Delete</a>
                                </div>
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
                        img.classList.add('w-full', 'h-32', 'rounded-lg');
                        
                        // Special handling for SVG files
                        if (file.type === 'image/svg+xml') {
                            img.classList.add('object-contain');
                            img.classList.add('bg-gray-100');
                            // Add SVG indicator
                            const svgBadge = document.createElement('div');
                            svgBadge.textContent = 'SVG';
                            svgBadge.classList.add('absolute', 'top-1', 'right-1', 'bg-blue-500', 'text-white', 'text-xs', 'px-2', 'py-1', 'rounded');
                            const container = document.createElement('div');
                            container.classList.add('relative');
                            container.appendChild(img);
                            container.appendChild(svgBadge);
                            thumbnailPreview.appendChild(container);
                        } else {
                            img.classList.add('object-cover');
                            thumbnailPreview.appendChild(img);
                        }
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