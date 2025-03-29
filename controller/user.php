<?php
include '../database/config.php';

$action = $_GET['action'] ?? '';

switch ($action) {
  case 'add':
    addUser($conn);
    break;

  case 'edit':
    editUser($conn);
    break;

  default:
    echo "Invalid action.";
    break;
}

function addUser($conn) {
  $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $role = mysqli_real_escape_string($conn, $_POST['role']);

  // Default password: password123
  $password = hash('sha256', 'password123');

  $query = "INSERT INTO users (full_name, email, username, password, role) 
            VALUES ('$full_name', '$email', '$username', '$password', '$role')";

  if (mysqli_query($conn, $query)) {
    echo "User added successfully with default password.";
  } else {
    echo "Error: " . mysqli_error($conn);
  }
}

function editUser($conn) {
  $id = intval($_POST['id']);
  $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $role = mysqli_real_escape_string($conn, $_POST['role']);

  $query = "UPDATE users 
            SET full_name = '$full_name', 
                email = '$email', 
                username = '$username', 
                role = '$role' 
            WHERE id = $id";

  if (mysqli_query($conn, $query)) {
    echo "User updated successfully.";
  } else {
    echo "Error: " . mysqli_error($conn);
  }
}
