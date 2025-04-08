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


function saveFile($inputName) {
    global $uploadDir;
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === 0) {
        $fileTmp = $_FILES[$inputName]['tmp_name'];
        $fileName = basename($_FILES[$inputName]['name']);
        $targetPath = $uploadDir . uniqid() . '_' . $fileName;
        move_uploaded_file($fileTmp, $targetPath);
        return $targetPath;
    }
    return null;
}

// Save multiple files
function saveMultipleFiles($inputName) {
    global $uploadDir;
    $paths = [];
    if (!empty($_FILES[$inputName]['name'][0])) {
        foreach ($_FILES[$inputName]['name'] as $i => $name) {
            $tmpName = $_FILES[$inputName]['tmp_name'][$i];
            $newName = $uploadDir . uniqid() . '_' . basename($name);
            move_uploaded_file($tmpName, $newName);
            $paths[] = $newName;
        }
    }
    return $paths;
}

// Collect form data
$project = [
    "title" => $_POST["project-title"],
    "description" => $_POST["project-description"],
    "department" => $_POST["department"],
    "semester" => $_POST["semester"],
    "year" => $_POST["academic-year"],
    "tags" => $_POST["tags"],
    "team" => $_POST["team-members"],
    "thumbnail" => saveFile("project-thumbnail"),
    "project_files" => saveMultipleFiles("project-files"),
    "demo_video" => saveFile("demo-video"),
    "research_paper" => saveFile("research-paper"),
    "timestamp" => date("Y-m-d H:i:s")
];

// Save to JSON
$jsonFile = 'projects.json';
$projects = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
$projects[] = $project;
file_put_contents($jsonFile, json_encode($projects, JSON_PRETTY_PRINT));

// Redirect to projects page
header("Location: projects.php");
exit();
?>




