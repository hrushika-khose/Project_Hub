<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password_input = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password_input, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            header("Location: index.html"); // Redirect to home
        } else {
            echo "Invalid username or password.";
        }
    } else {
        echo "Login error.";
    }
}
?>
