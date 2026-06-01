<?php
// =============================================
// db.php - Database Connection File
// This file connects PHP to MySQL database
// =============================================

// Database configuration
$host = "localhost";      // MySQL server (XAMPP default)
$user = "root";           // MySQL username (XAMPP default)
$password = "";           // MySQL password (XAMPP default is empty)
$database = "attendance_db"; // Our database name

// Create connection using mysqli
$conn = mysqli_connect($host, $user, $password, $database);

// Check if connection was successful
if (!$conn) {
    // Stop and show error if connection fails
    die(json_encode([
        "error" => "Connection failed: " . mysqli_connect_error()
    ]));
}

// Set character encoding to UTF-8 for proper text handling
mysqli_set_charset($conn, "utf8");
?>
