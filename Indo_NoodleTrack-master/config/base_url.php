<?php
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
    $path = str_replace('\\', '/', $path);
    $path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
    $path = str_replace('\\', '/', $path);
    $path = str_replace('C:/xampp/htdocs', '', $path);
    return $protocol . '://' . $host . '/noodlebaru/Indonoodletrack/Indo_NoodleTrack-master/';
}
?>
