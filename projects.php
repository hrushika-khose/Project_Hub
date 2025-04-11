
<?php
 // Include database connection
 error_reporting(E_ALL);
 ini_set('display_errors', 1);
 
 // Include DB config
 $host = "127.0.0.1";
 $user = "root";
 $pass = "";
 $dbname = "project_hub";
 $port = 3308; // or 3307, depending on what you saw in my.ini
 
 $conn = new mysqli($host, $user, $pass, $dbname, $port);
// Get filter parameters if they exist
$department = isset($_GET['department']) ? $_GET['department'] : '';
$semester = isset($_GET['semester']) ? $_GET['semester'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build the SQL query with potential filters
$sql = "SELECT * FROM projects WHERE 1=1";

if (!empty($department)) {
    $sql .= " AND department = '$department'";
}

if (!empty($semester)) {
    $sql .= " AND semester = '$semester'";
}

if (!empty($year)) {
    $sql .= " AND academic_year = '$year'";
}

if (!empty($search)) {
    $sql .= " AND (title LIKE '%$search%' OR description LIKE '%$search%' OR tags LIKE '%$search%')";
}

// Order by newest first
$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Projects - VPKBIET's Project Hub</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/projects.css">
    <script src="js/projects.js" defer></script>
    <script src="js/script.js" defer></script>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <header>
            <div class="logo">
                <h1>VPKBIET's Project Hub</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="projects.php" class="active">Projects</a></li>
                    <li><a href="upload.html">Upload</a></li>
                    <li><a href="about.html">About</a></li>
                    <div id="login-btn"><a href="login.html">Login</a></div>

                </ul>
            </nav>
        </header>

        <!-- Main Content -->
        <main>
            <section class="projects-header">
                <h2>Browse Projects</h2>
                <p>Discover innovative projects from your peers and seniors</p>
            </section>

            <!-- Filter Section -->
            <section class="filter-section">
                <form action="projects.php" method="get" class="filter-form">
                    <div class="filter-controls">
                        <div class="filter-group">
                            <label for="department-filter">Department</label>
                            <select id="department-filter" name="department">
                                <option value="">All Departments</option>
                                <option value="computer-science" <?php if($department == 'computer-science') echo 'selected'; ?>>Computer Science</option>
                                <option value="electronics" <?php if($department == 'electronics') echo 'selected'; ?>>Electronics</option>
                                <option value="mechanical" <?php if($department == 'mechanical') echo 'selected'; ?>>Mechanical</option>
                                <option value="chemical" <?php if($department == 'chemical') echo 'selected'; ?>>Chemical</option>
                                <option value="civil" <?php if($department == 'civil') echo 'selected'; ?>>Civil</option>
                                <option value="electrical" <?php if($department == 'electrical') echo 'selected'; ?>>Electrical</option>
                                <option value="biotechnology" <?php if($department == 'biotechnology') echo 'selected'; ?>>Biotechnology</option>
                                <option value="other" <?php if($department == 'other') echo 'selected'; ?>>Other</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="semester-filter">Semester</label>
                            <select id="semester-filter" name="semester">
                                <option value="">All Semesters</option>
                                <?php for($i=1; $i<=8; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php if($semester == $i) echo 'selected'; ?>><?php echo $i; ?>th Semester</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="year-filter">Academic Year</label>
                            <select id="year-filter" name="year">
                                <option value="">All Years</option>
                                <?php for($year=2025; $year>=2021; $year--): ?>
                                    <option value="<?php echo $year; ?>" <?php if(isset($_GET['year']) && $_GET['year'] == $year) echo 'selected'; ?>><?php echo $year; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="filter-actions">
                            <button type="submit" class="btn primary-btn">Apply Filters</button>
                            <a href="projects.php" class="btn reset-btn">Reset</a>
                        </div>
                    </div>
                </form>
            </section>

            <!-- Projects Grid -->
            <section class="projects-grid">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                ?>
                <div class="project-card">
                    <div class="project-image">
                        <?php if (!empty($row['image_path'])): ?>
                            <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        <?php else: ?>
                            <img src="images/placeholder-project.jpg" alt="Project Placeholder">
                        <?php endif; ?>
                    </div>
                    <div class="project-content">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <div class="project-meta">
                            <span class="department"><?php echo ucfirst(str_replace('-', ' ', $row['department'])); ?></span>
                            <span class="semester">Semester <?php echo $row['semester']; ?></span>
                            <span class="year"><?php echo $row['academic_year']; ?></span>
                        </div>
                        <p class="project-description">
                            <?php echo substr(htmlspecialchars($row['description']), 0, 120) . '...'; ?>
                        </p>
                        <div class="project-tags">
                            <?php 
                            if(!empty($row['tags'])) {
                                $tagArray = explode(',', $row['tags']);
                                foreach($tagArray as $tag) {
                                    echo '<span class="tag">' . trim(htmlspecialchars($tag)) . '</span>';
                                }
                            }
                            ?>
                        </div>
                        <a href="project-details.php?id=<?php echo $row['id']; ?>" class="btn secondary-btn">View Details</a>
                    </div>
                </div>
                <?php
                    }
                } else {
                ?>
                <div class="no-projects">
                    <h3>No projects found</h3>
                    <p>No projects match your current filters. Try adjusting your search criteria or <a href="upload.html">upload a new project</a>.</p>
                </div>
                <?php
                }
                ?>
            </section>
        </main>

        <!-- Footer -->
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
                    <p>Email: support@vpkbietprojhub.edu</p>
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