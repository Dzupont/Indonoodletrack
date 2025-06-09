<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin($admin = false) {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    return true;
}

function checkLoginAndRedirect($admin = false) {
    if (!requireLogin($admin)) {
        header('Location: ' . getBaseUrl() . 'views/auth/login.php');
        exit();
    }
}

function getCurrentUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

function url($path) {
    return getBaseUrl() . $path;
}
?>
