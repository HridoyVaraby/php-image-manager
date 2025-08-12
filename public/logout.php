<?php
require_once 'config.php';

// Unset all of the session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the login page
header('Location: login.php');
exit;
?>