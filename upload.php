<?php
// Show errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include DB config
$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "project_hub";
$port = 3308; // or 3307, depending on what you saw in my.ini

$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Fetch form values
    $title = $_POST["project-title"] ?? '';
    $description = $_POST["project-description"] ?? '';
    $department = $_POST["department"] ?? '';
    $semester = $_POST["semester"] ?? '';
    $academic_year = $_POST["academic-year"] ?? '';
    $tags = $_POST["tags"] ?? '';
    $team_members = $_POST["team-members"] ?? '';

    // File upload directory
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    function saveFile($field) {
        global $uploadDir;
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            $fileName = time() . "_" . basename($_FILES[$field]['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetPath)) {
                return $targetPath;
            }
        }
        return null;
    }

    // Save files and get paths
    $image_path = saveFile("project-thumbnail");
    $project_file_path = saveFile("project-files");
    $demo_video_path = saveFile("demo-video");
    $documentation_path = saveFile("research-paper");

    // Insert query
    $stmt = $conn->prepare("INSERT INTO projects 
        (title, description, department, semester, academic_year, tags, team_members, image_path, project_file_path, demo_video_path, documentation_path) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param(
            "sssssssssss", 
            $title, $description, $department, $semester, $academic_year,
            $tags, $team_members, $image_path, $project_file_path, $demo_video_path, $documentation_path
        );

        if ($stmt->execute()) {
            echo "✅ Project uploaded successfully!";
        } else {
            echo "❌ Execution error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "❌ SQL prepare error: " . $conn->error;
    }

    $conn->close();
} else {
    echo "❌ Invalid request method.";
}


?>
