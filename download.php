<?php
// download.php - Handles file downloads with proper headers

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "project_hub";
$port = 3308;

$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if file path is provided
if (!isset($_GET['file'])) {
    die("No file specified");
}

$file_path = urldecode($_GET['file']);

// Security check - prevent directory traversal
$real_path = realpath($file_path);
if ($real_path === false || strpos($real_path, realpath('uploads')) !== 0) {
    die("Invalid file path");
}

// Check if the file exists
if (!file_exists($file_path)) {
    die("File not found");
}

// Get file info
$file_name = basename($file_path);
$file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
$file_size = filesize($file_path);

// Define MIME types for common file extensions
$mime_types = [
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'ppt' => 'application/vnd.ms-powerpoint',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'txt' => 'text/plain',
    'zip' => 'application/zip',
    'rar' => 'application/x-rar-compressed',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'mp4' => 'video/mp4',
    'avi' => 'video/x-msvideo',
    'mov' => 'video/quicktime',
    'webm' => 'video/webm',
    'php' => 'text/plain',
    'html' => 'text/html',
    'css' => 'text/css',
    'js' => 'application/javascript',
    'java' => 'text/plain',
    'py' => 'text/plain',
    'c' => 'text/plain',
    'cpp' => 'text/plain',
    'h' => 'text/plain',
    'ino' => 'text/plain'
];

// Set the appropriate content type
$content_type = isset($mime_types[$file_extension]) ? $mime_types[$file_extension] : 'application/octet-stream';

// Set headers for file download
header('Content-Description: File Transfer');
header('Content-Type: ' . $content_type);
header('Content-Disposition: attachment; filename="' . $file_name . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . $file_size);

// Clear output buffer
ob_clean();
flush();

// Read and output file
readfile($file_path);
exit;
?>