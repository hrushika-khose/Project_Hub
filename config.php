<?php
$host = "localhost";
$dbname = "project_hub";  // The name of the database you created in phpMyAdmin
$username = "root";
$password = "";  // Default password for XAMPP is empty

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
