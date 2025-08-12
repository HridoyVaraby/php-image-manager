<?php
// Basic configuration
define('UPLOADS_DIR', 'uploads/');
define('USERNAME', 'Varabit');
define('PASSWORD', '192918541525Hv@images'); // Change this!

define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/');

// File settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml']);

// Start the session
session_start();

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Function to check if the user is logged in
function is_logged_in() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

// Function to redirect to the login page if not logged in
function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
?>