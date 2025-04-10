<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include DB config
$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "project_hub";
$port = 3308; // or 3307, depending on what you saw in my.ini

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if (!isset($_SESSION['user_id'])) {
    die("User not logged in. Cannot upload project.");
}
$user_id = $_SESSION['user_id'];

// Sanitize and get POST data
$title = $_POST['project-title'];
$description = $_POST['project-description'];
$department = $_POST['department'];
$semester = $_POST['semester'];
$academic_year = $_POST['academic-year'];
$tags = $_POST['tags'];
$team_members = $_POST['team-members'];

// Create uploads folder if not exists
$uploadDir = "uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Upload image
$image_path = '';
if (isset($_FILES['project-thumbnail']) && $_FILES['project-thumbnail']['error'] === 0) {
    $imgName = time() . "_" . basename($_FILES["project-thumbnail"]["name"]);
    $targetImg = $uploadDir . $imgName;
    move_uploaded_file($_FILES["project-thumbnail"]["tmp_name"], $targetImg);
    $image_path = $targetImg;
}

// Upload multiple project files (zip, pdf, etc.)
$project_file_paths = [];
if (isset($_FILES['project-files'])) {
    foreach ($_FILES['project-files']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['project-files']['error'][$key] === 0) {
            $fileName = time() . "_" . basename($_FILES['project-files']['name'][$key]);
            $targetFile = $uploadDir . $fileName;
            move_uploaded_file($tmp_name, $targetFile);
            $project_file_paths[] = $targetFile;
        }
    }
}
$project_file_path = implode(',', $project_file_paths); // store all paths as comma-separated string

// Upload demo video
$demo_video_path = '';
if (isset($_FILES['demo-video']) && $_FILES['demo-video']['error'] === 0) {
    $videoName = time() . "_" . basename($_FILES["demo-video"]["name"]);
    $targetVideo = $uploadDir . $videoName;
    move_uploaded_file($_FILES["demo-video"]["tmp_name"], $targetVideo);
    $demo_video_path = $targetVideo;
}

// Upload research paper/document
$documentation_path = '';
if (isset($_FILES['research-paper']) && $_FILES['research-paper']['error'] === 0) {
    $docName = time() . "_" . basename($_FILES["research-paper"]["name"]);
    $targetDoc = $uploadDir . $docName;
    move_uploaded_file($_FILES["research-paper"]["tmp_name"], $targetDoc);
    $documentation_path = $targetDoc;
}

// Insert into database
$sql = "INSERT INTO projects (title, description, department, semester, academic_year, tags, team_members, image_path, project_file_path, demo_video_path, documentation_path,user_id)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssssi", $title, $description, $department, $semester, $academic_year, $tags, $team_members, $image_path, $project_file_path, $demo_video_path, $documentation_path,$user_id);

if ($stmt->execute()) {
    // Redirect to the profile page after successful upload
    header("Location: profile.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>




