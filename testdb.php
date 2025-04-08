<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "project_hub";
$port = 3308;

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("❌ Failed to connect: " . $conn->connect_error);
}
echo "✅ Connected successfully!";
?>
