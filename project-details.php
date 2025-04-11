<?php
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

// Get project ID from URL
if (!isset($_GET['id'])) {
    die("Invalid request. No project ID.");
}
$project_id = intval($_GET['id']);

// Fetch project info
$sql = "SELECT * FROM projects WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Project not found.");
}
$project = $result->fetch_assoc();

// Get current active tab
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'overview';

// Function to get file size in human readable format
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Function to determine file icon based on extension
function getFileIcon($filename) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    $icons = [
        'pdf' => 'fa fa-file-pdf',
        'doc' => 'fa fa-file-word',
        'docx' => 'fa fa-file-word',
        'ppt' => 'fa fa-file-powerpoint',
        'pptx' => 'fa fa-file-powerpoint',
        'txt' => 'fa fa-file-alt',
        'zip' => 'fa fa-file-archive',
        'rar' => 'fa fa-file-archive',
        'java' => 'fa fa-file-code',
        'py' => 'fa fa-file-code',
        'cpp' => 'fa fa-file-code',
        'h' => 'fa fa-file-code',
        'c' => 'fa fa-file-code',
        'php' => 'fa fa-file-code',
        'js' => 'fa fa-file-code',
        'html' => 'fa fa-file-code',
        'css' => 'fa fa-file-code',
        'jpg' => 'fa fa-file-image',
        'jpeg' => 'fa fa-file-image',
        'png' => 'fa fa-file-image',
        'gif' => 'fa fa-file-image',
        'mp4' => 'fa fa-file-video',
        'avi' => 'fa fa-file-video',
        'mov' => 'fa fa-file-video',
        'webm' => 'fa fa-file-video'
    ];
    
    return isset($icons[$extension]) ? $icons[$extension] : 'fa fa-file';
}

// Process project files if they exist
$project_files = [];
if (!empty($project['project_file_path'])) {
    $file_paths = explode(',', $project['project_file_path']);
    foreach ($file_paths as $path) {
        if (!empty($path)) {
            $filename = basename($path);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $filesize = file_exists($path) ? filesize($path) : 0;
            
            $project_files[] = [
                'name' => $filename,
                'path' => $path,
                'extension' => $extension,
                'size' => $filesize,
                'icon' => getFileIcon($filename)
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['title']); ?> - Project Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        
        .project-header {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
        }
        
        .section {
            margin-bottom: 20px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .label {
            font-weight: bold;
            color: #3498db;
            margin-bottom: 5px;
        }
        
        .tabs {
            display: flex;
            margin-bottom: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .tab {
            padding: 15px 20px;
            cursor: pointer;
            flex-grow: 1;
            text-align: center;
            font-weight: bold;
            transition: background-color 0.3s;
            border-bottom: 3px solid transparent;
        }
        
        .tab.active {
            background-color: #f8f9fa;
            border-bottom: 3px solid #3498db;
            color: #3498db;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .file-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .file-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .file-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .file-icon {
            font-size: 40px;
            color: #3498db;
            margin-bottom: 10px;
        }
        
        .file-name {
            font-weight: bold;
            margin-bottom: 5px;
            word-break: break-all;
        }
        
        .file-info {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .file-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 15px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .media-container {
            margin-top: 20px;
        }
        
        .img-thumbnail {
            max-width: 300px;
            max-height: 300px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        video {
            max-width: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .tag {
            background-color: #e0f7fa;
            color: #0288d1;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        .team-section {
            white-space: pre-line;
        }
        
        @media (max-width: 768px) {
            .file-container {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
            
            .tab {
                padding: 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="project-header">
            <h1><?php echo htmlspecialchars($project['title']); ?></h1>
            
            <div class="section">
                <p class="label">Department:</p>
                <p><?php echo htmlspecialchars($project['department']); ?></p>
            </div>
            
            <div class="section">
                <p class="label">Semester:</p>
                <p><?php echo htmlspecialchars($project['semester']); ?></p>
            </div>
            
            <div class="section">
                <p class="label">Academic Year:</p>
                <p><?php echo htmlspecialchars($project['academic_year']); ?></p>
            </div>
            
            <div class="section">
                <p class="label">Tags:</p>
                <div>
                    <?php
                    if (!empty($project['tags'])) {
                        $tag_array = explode(',', $project['tags']);
                        foreach ($tag_array as $tag) {
                            echo '<span class="tag">' . htmlspecialchars(trim($tag)) . '</span>';
                        }
                    } else {
                        echo "<p>No tags available</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <div class="tabs">
            <div class="tab <?php echo $active_tab === 'overview' ? 'active' : ''; ?>" data-tab="overview">Overview</div>
            <div class="tab <?php echo $active_tab === 'files' ? 'active' : ''; ?>" data-tab="files">Files</div>
            <div class="tab <?php echo $active_tab === 'media' ? 'active' : ''; ?>" data-tab="media">Media</div>
            <div class="tab <?php echo $active_tab === 'team' ? 'active' : ''; ?>" data-tab="team">Team</div>
        </div>
        
        <!-- Overview Tab -->
        <div class="tab-content <?php echo $active_tab === 'overview' ? 'active' : ''; ?>" id="overview">
            <div class="section">
                <p class="label">Project Description:</p>
                <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
            </div>
        </div>
        
        <!-- Files Tab -->
        <div class="tab-content <?php echo $active_tab === 'files' ? 'active' : ''; ?>" id="files">
            <div class="section">
                <p class="label">Project Files:</p>
                
                <?php if (!empty($project_files)): ?>
                    <div class="file-container">
                        <?php foreach ($project_files as $file): ?>
                            <div class="file-card">
                                <div class="file-icon">
                                    <i class="<?php echo $file['icon']; ?>"></i>
                                </div>
                                <div class="file-name"><?php echo htmlspecialchars($file['name']); ?></div>
                                <div class="file-info">
                                    <?php echo strtoupper($file['extension']); ?> • <?php echo formatFileSize($file['size']); ?>
                                </div>
                                <div class="file-actions">
                                    <a href="<?php echo htmlspecialchars($file['path']); ?>" class="btn" download>
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <?php if (in_array($file['extension'], ['pdf', 'jpg', 'jpeg', 'png', 'gif'])): ?>
                                        <a href="<?php echo htmlspecialchars($file['path']); ?>" class="btn" target="_blank">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No project files available</p>
                <?php endif; ?>
                
                <?php if (!empty($project['documentation_path'])): ?>
                    <div class="section" style="margin-top: 20px;">
                        <p class="label">Research Paper / Documentation:</p>
                        <div class="file-container">
                            <div class="file-card">
                                <div class="file-icon">
                                    <i class="<?php echo getFileIcon($project['documentation_path']); ?>"></i>
                                </div>
                                <div class="file-name"><?php echo htmlspecialchars(basename($project['documentation_path'])); ?></div>
                                <div class="file-info">
                                    <?php 
                                    $ext = pathinfo($project['documentation_path'], PATHINFO_EXTENSION);
                                    $size = file_exists($project['documentation_path']) ? filesize($project['documentation_path']) : 0;
                                    echo strtoupper($ext) . ' • ' . formatFileSize($size);
                                    ?>
                                </div>
                                <div class="file-actions">
                                    <a href="<?php echo htmlspecialchars($project['documentation_path']); ?>" class="btn" download>
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <?php if ($ext === 'pdf'): ?>
                                        <a href="<?php echo htmlspecialchars($project['documentation_path']); ?>" class="btn" target="_blank">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Media Tab -->
        <div class="tab-content <?php echo $active_tab === 'media' ? 'active' : ''; ?>" id="media">
            <div class="section">
                <?php if (!empty($project['image_path'])): ?>
                    <div class="media-container">
                        <p class="label">Project Thumbnail:</p>
                        <img src="<?php echo htmlspecialchars($project['image_path']); ?>" alt="Project Thumbnail" class="img-thumbnail">
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($project['demo_video_path'])): ?>
                    <div class="media-container">
                        <p class="label">Demo Video:</p>
                        <video controls>
                            <source src="<?php echo htmlspecialchars($project['demo_video_path']); ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                <?php endif; ?>
                
                <?php 
                // Show image files from project files
                $image_files = array_filter($project_files, function($file) {
                    return in_array($file['extension'], ['jpg', 'jpeg', 'png', 'gif']);
                });
                
                if (!empty($image_files)): 
                ?>
                    <div class="media-container">
                        <p class="label">Additional Images:</p>
                        <div class="file-container">
                            <?php foreach ($image_files as $file): ?>
                                <div class="file-card">
                                    <img src="<?php echo htmlspecialchars($file['path']); ?>" alt="<?php echo htmlspecialchars($file['name']); ?>" class="img-thumbnail" style="height: 150px; object-fit: cover;">
                                    <div class="file-name"><?php echo htmlspecialchars($file['name']); ?></div>
                                    <div class="file-actions">
                                        <a href="<?php echo htmlspecialchars($file['path']); ?>" class="btn" target="_blank">
                                            <i class="fas fa-eye"></i> View Full Size
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Team Tab -->
        <div class="tab-content <?php echo $active_tab === 'team' ? 'active' : ''; ?>" id="team">
            <div class="section">
                <p class="label">Team Members:</p>
                <div class="team-section">
                    <?php echo nl2br(htmlspecialchars($project['team_members'])); ?>
                </div>
            </div>
        </div>
        
        <div class="container" style="text-align: center;">
            <a href="projects.php" class="back-link">← Back to Projects</a>
        </div>
    </div>
    
    <script>
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Update tab state in URL
                    const tabId = this.getAttribute('data-tab');
                    const url = new URL(window.location.href);
                    url.searchParams.set('tab', tabId);
                    history.pushState({}, '', url);
                    
                    // Remove active class from all tabs
                    document.querySelectorAll('.tab').forEach(t => {
                        t.classList.remove('active');
                    });
                    
                    // Remove active class from all tab contents
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    
                    // Add active class to clicked tab and corresponding content
                    this.classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>