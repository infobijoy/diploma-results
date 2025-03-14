<?php
// Database connection details
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    // XAMPP Server
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "result";
} else {
    // Live Server
    $servername = "localhost"; // Usually remains the same
    $username = "bijoydev_result";
    $password = "bijoydev_result";
    $dbname = "bijoydev_result";
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>