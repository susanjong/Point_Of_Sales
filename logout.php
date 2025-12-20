<?php
require_once 'config.php';

if (isLoggedIn()) {
    // Log activity
    logActivity($_SESSION['user_id'], $_SESSION['username'], 'logout', 'User logged out');
    
    // Destroy session
    session_destroy();
}

header("Location: login.php");
exit();
?>