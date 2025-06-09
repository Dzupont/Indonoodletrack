<?php
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname(dirname(__FILE__)); // Go up two levels from config directory
    $path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
    $path = str_replace('\\', '/', $path); // Replace double backslashes with forward slashes
    $path = str_replace('C:/xampp/htdocs', '', $path);
    $path = trim($path, '/'); // Remove leading/trailing slashes
    
    return $protocol . '://' . $host . '/' . $path . '/';
}
?>
