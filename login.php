<?php
session_start();

// Database config
$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "project_hub";
$port = 3308; // or 3307, depending on what you saw in my.ini

// Initialize response array for JSON output
$response = [
    'success' => false,
    'message' => '',
    'redirect' => '',
    'username' => ''
];

try {
    $conn = new mysqli($host, $user, $pass, $dbname, $port);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $usernameOrEmail = trim($_POST['username']);
        $passwordInput = $_POST['password'];
        
        // Input validation
        if (empty($usernameOrEmail)) {
            $response['message'] = "Invalid username";
            echo json_encode($response);
            exit;
        }
        
        if (empty($passwordInput)) {
            $response['message'] = "Invalid password";
            echo json_encode($response);
            exit;
        }

        // Prepare query
        $sql = "SELECT id, username, email, password FROM users WHERE email = ? OR username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        // Validate result
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($passwordInput, $user['password'])) {
                // Success case
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['loggedIn'] = true;
                
                $response['success'] = true;
                $response['message'] = "Login successful!";
                $response['redirect'] = 'index.html';
                $response['username'] = $user['username'];
            } else {
                // Wrong password
                $response['message'] = "Invalid password";
            }
        } else {
            // No user found
            $response['message'] = "Invalid username";
        }

        $stmt->close();
    }
} catch (Exception $e) {
    $response['message'] = "System error. Please try again later.";
    // Log the error (in a real system)
    // error_log($e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>