<?php
session_start();

// Destroy the session
session_destroy();

// Redirect to login page with success message
header('Location: ../../views/auth/login.php?logout=success');
exit();
