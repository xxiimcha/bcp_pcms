<?php
session_start();
header('Content-Type: application/json');

// Include DB config
require_once '../database/config.php';

// Fetch and sanitize input
$username = isset($_POST['username']) ? mysqli_real_escape_string($conn, trim($_POST['username'])) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
    exit;
}

// Hash the input password using SHA-256 to match DB format
$hashedPassword = hash('sha256', $password);

// Query to get user
$query = "SELECT * FROM users WHERE username = '$username' AND password = '$hashedPassword' AND status = 'active' LIMIT 1";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
}

mysqli_close($conn);
