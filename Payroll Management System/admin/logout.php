<?php
session_start();

// Clear all session data
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to login page or homepage after logout
header("Location: login.php");
exit();
