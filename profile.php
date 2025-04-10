<?php
// Start session
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Include DB config
$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "project_hub";
$port = 3308;

$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch user information
$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Fetch user projects
$stmt_projects = $conn->prepare("SELECT * FROM projects WHERE user_id = ?");
$stmt_projects->bind_param("i", $user_id);
$stmt_projects->execute();
$result_projects = $stmt_projects->get_result();

// Count total projects
$project_count = $result_projects->num_rows;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - VPKBIET's Project Hub</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="js/script.js" defer></script>
    <script src="/js/profile.js" defer></script>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <header>
            <div class="logo">
                <h1>VPKBIET's Project Hub</h1>
            </div>
            <div class="search-bar">
                <input type="text" placeholder="Search projects...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
            <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="projects.php">Projects</a></li>
                    <li><a href="upload.html">Upload</a></li>
                    <li><a href="about.html">About</a></li>
                    <li id="profile-btn"><a href="profile.php" class="active">Profile</a></li>
                </ul>
            </nav>
        </header>

        <!-- Main Content -->
        <main>
            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-cover"></div>
                    <div class="profile-avatar">
                    <img src="images/profile_pic.jpeg" alt="Profile Avatar" 
                    style="display: block; margin-left: auto; margin-right: auto;width : 160px;height:150px;object-fit: cover; border-radius: %;">
                    </div>
                    <div class="profile-info">
                    <h2 class="profile-name"><?php echo htmlspecialchars($user['username']); ?></h2>
                        
                    <div class="profile-stats">
                            <div class="stat">
                                <div class="stat-value"><?php echo $project_count; ?></div>
                                <div class="stat-label">Projects</div>
                            </div>
                        
                            <!-- <div class="stat">
                                <div class="stat-value">120</div>
                                <div class="stat-label">Followers</div>
                            </div> -->
                        </div>
                        
                        <!-- <div class="profile-actions">
                            <button class="btn primary-btn follow-btn">Follow</button>
                        </div> -->
                    </div>
                </div>

                <div class="profile-nav">
                    <div class="profile-nav-item active" data-tab="projects">Projects</div>
                </div>

                <div class="profile-content">
                    <!-- Projects Section -->
                    <div class="profile-section active" id="projects">
                    <h3 class="section-title">My Projects</h3>
                        
                        <?php if ($project_count > 0): ?>
                            <div class="profile-projects">
                                <?php while ($project = $result_projects->fetch_assoc()): ?>
                        <!-- <div class="profile-projects"> -->
                        <div class="project-card">
                        <div class="project-image" style="background-image: url('<?php echo !empty($project['image_path']) ? htmlspecialchars($project['image_path']) : 'images/project-placeholder.jpg'; ?>');"></div>
                                <!-- <div class="project-details">
                                    <h4 class="project-title">Smart Irrigation System</h4>
                                    <div class="project-tags">
                                        <span class="project-tag">IoT</span>
                                        <span class="project-tag">Arduino</span>
                                        <span class="project-tag">Electronics</span>
                                    </div> -->
                                    <div class="project-details">
                                            <h4 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h4>
                                            <div class="project-tags">
                                                <?php 
                                                if (!empty($project['tags'])) {
                                                    $tags = explode(',', $project['tags']);
                                                    foreach ($tags as $tag) {
                                                        echo '<span class="project-tag">' . trim(htmlspecialchars($tag)) . '</span>';
                                                    }
                                                }
                                                ?>
                                            </div>

                                    <!-- <p class="project-desc">An automated system that monitors soil moisture and controls water flow to optimize irrigation for agricultural fields.</p>
                                    <div class="project-meta">
                                        <span>April 2025</span> -->
                                        <p class="project-desc"><?php echo substr(htmlspecialchars($project['description']), 0, 120) . '...'; ?></p>
                                            <div class="project-meta">
                                                <span><?php echo date('F Y', strtotime($project['created_at'])); ?></span>
                                        <!-- <div class="project-likes"><i class="fas fa-heart"></i> 24</div> -->
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                            <?php else: ?>
                            <div class="no-projects">
                                <p>You haven't uploaded any projects yet.</p>
                                <a href="upload.html" class="btn primary-btn">Upload Your First Project</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
        <footer>
            <div class="footer-content">
                <div class="footer-section">
                    <h3>VPKBIET's Project Hub</h3>
                    <p>A platform for students to showcase their semester projects</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.html">Home</a></li>
                        <li><a href="projects.php">Projects</a></li>
                        <li><a href="upload.html">Upload</a></li>
                        <li><a href="contact.html">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <p>Email: support@vpkbiet.edu</p>
                    <p>Phone: (123) 456-7890</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 VPKBIET's Project Hub. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>

<?php
$stmt_user->close();
$stmt_projects->close();
$conn->close();
?>