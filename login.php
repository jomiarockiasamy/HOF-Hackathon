<?php
session_start(); // Always start session at top

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'];
$password = $input['password'];

// Connect to database
$host = 'localhost';
$dbusername = 'root';
$dbpassword = '';
$dbname = 'user_info';

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Find user by username
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // If you use hashed passwords:
    // if (password_verify($password, $user['password'])) {
    
    if ($password === $user['password']) {
        $_SESSION['username'] = $username;
        echo json_encode(["success" => true]);
    } else {
        http_response_code(401); // Unauthorized
        echo json_encode(["success" => false, "message" => "Incorrect password"]);
    }
} else {
    http_response_code(404); // Not found
    echo json_encode(["success" => false, "message" => "User not found"]);
}

$stmt->close();
$conn->close();
?>