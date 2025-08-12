<?php
require_once 'config.php';
require_login();

if (isset($_GET['file']) && isset($_GET['new_name']) && isset($_GET['csrf_token'])) {
    // Verify CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $old_file = basename($_GET['file']);
    $new_name = basename($_GET['new_name']);

    // Sanitize the new filename and preserve the extension
    $extension = pathinfo($old_file, PATHINFO_EXTENSION);
    $filename_sans_extension = pathinfo($new_name, PATHINFO_FILENAME);
    $sanitized_new_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $filename_sans_extension) . '.' . $extension;

    $old_file_path = UPLOADS_DIR . $old_file;
    $new_file_path = UPLOADS_DIR . $sanitized_new_name;

    // Ensure the old file exists and the new name is not empty
    if (file_exists($old_file_path) && is_file($old_file_path) && !empty($sanitized_new_name)) {
        rename($old_file_path, $new_file_path);
    }
}

header('Location: index.php');
exit;
?>