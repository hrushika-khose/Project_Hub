<?php
session_start();

// ✅ Include database config
// include 'config.php';
$host = "127.0.0.1";
 $user = "root";
 $pass = "";
 $dbname = "project_hub";
 $port = 3308; // or 3307, depending on what you saw in my.ini
 
 $conn = new mysqli($host, $user, $pass, $dbname,$port);
// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameOrEmail = trim($_POST['username']);
    $passwordInput = $_POST['password'];

    // ✅ Prepare query
    $sql = "SELECT id, username, email, password FROM users WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    // ✅ Validate result
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($passwordInput, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            echo "✅ Login successful! Welcome, " . $user['username'] . ". <a href='about.html'>Go to About Page</a>";
            // Or you can use: header("Location: index.html"); exit;
        } else {
            echo "❌ Incorrect password. <a href='login.html'>Try again</a>";
        }
    } else {
        echo "⚠ No account found with that username or email. <a href='register.html'>Register here</a>";
    }

    $stmt->close();
    $conn->close();
}
?>
