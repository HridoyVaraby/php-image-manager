<?php
require_once 'config.php';
require_login();

if (isset($_GET['file']) && isset($_GET['csrf_token'])) {
    // Verify CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $file = basename($_GET['file']);
    $file_path = UPLOADS_DIR . $file;

    // Ensure the file exists and is within the uploads directory
    if (file_exists($file_path) && is_file($file_path)) {
        unlink($file_path);
    }
}

header('Location: index.php');
exit;
?>