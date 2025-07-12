<?php
require_once 'config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $files = $_FILES['files'];
    $upload_errors = [];

    foreach ($files['name'] as $key => $name) {
        $tmp_name = $files['tmp_name'][$key];
        $size = $files['size'][$key];
        $error = $files['error'][$key];

        if ($error !== UPLOAD_ERR_OK) {
            $upload_errors[] = "Error uploading file: $name";
            continue;
        }

        // File size validation
        if ($size > MAX_FILE_SIZE) {
            $upload_errors[] = "File is too large: $name";
            continue;
        }

        // MIME type validation
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($tmp_name);
        if (!in_array($mime_type, ALLOWED_MIME_TYPES, true)) {
            $upload_errors[] = "Invalid file type: $name";
            continue;
        }

        // File extension validation
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS, true)) {
            $upload_errors[] = "Invalid file extension: $name";
            continue;
        }

        // Sanitize filename and create a unique name
        $filename_sans_extension = pathinfo($name, PATHINFO_FILENAME);
        $sanitized_filename = preg_replace('/[^a-zA-Z0-9_-]/', '', $filename_sans_extension);
        $new_filename = uniqid() . '-' . $sanitized_filename . '.' . $extension;
        $destination = UPLOADS_DIR . $new_filename;
        
        // Additional security check for SVG files
        if ($extension === 'svg') {
            // Read the file content
            $svg_content = file_get_contents($tmp_name);
            
            // Check for potentially malicious content
            $dangerous_patterns = [
                '/<script[^>]*>/', // JavaScript
                '/javascript:/', // JavaScript protocol
                '/eval\s*\(/', // eval() function
                '/on\w+=["\']/', // event handlers
                '/xlink:href=["\'](?!#)/', // external links
                '/data:[^,]*base64/', // data URLs with base64
                '/<!ENTITY/', // XML entities
                '/<!DOCTYPE/' // DOCTYPE declarations
            ];
            
            foreach ($dangerous_patterns as $pattern) {
                if (preg_match($pattern, $svg_content)) {
                    $upload_errors[] = "Security violation detected in SVG file: $name";
                    continue 2; // Skip this file
                }
            }
            
            // Sanitize SVG content (basic sanitization)
            $svg_content = preg_replace($dangerous_patterns, '', $svg_content);
            
            // Write sanitized content to destination
            if (file_put_contents($destination, $svg_content) === false) {
                $upload_errors[] = "Failed to save sanitized SVG file: $name";
            }
        } else {
            // For non-SVG files, use the standard move_uploaded_file function
            if (!move_uploaded_file($tmp_name, $destination)) {
                $upload_errors[] = "Failed to move uploaded file: $name";
            }
        }
    }

    if (empty($upload_errors)) {
        $_SESSION['upload_success'] = 'Files uploaded successfully.';
    } else {
        $_SESSION['upload_errors'] = $upload_errors;
    }

    header('Location: index.php');
    exit;
}
?>