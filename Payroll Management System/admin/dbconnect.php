<?php
// Database configuration
$host = 'localhost';      // Usually localhost
$user = 'root';           // Your DB username
$password = '';           // Your DB password
$database = 'payroll';    // Your DB name

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set charset to utf8mb4 for better character support
$conn->set_charset("utf8mb4");
?>
