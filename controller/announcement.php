<?php
include '../database/config.php';

$action = $_GET['action'] ?? '';

switch ($action) {
  case 'add':
    addAnnouncement($conn);
    break;

  case 'edit':
    editAnnouncement($conn);
    break;

  case 'toggle_status':
    toggleStatus($conn);
    break;

  default:
    echo "Invalid action.";
    break;
}

function addAnnouncement($conn) {
  $title = mysqli_real_escape_string($conn, $_POST['title']);
  $content = mysqli_real_escape_string($conn, $_POST['content']);
  $created_at = date('Y-m-d H:i:s');
  $status = 'Published'; // Default status

  $query = "INSERT INTO announcements (title, content, created_at, status)
            VALUES ('$title', '$content', '$created_at', '$status')";

  if (mysqli_query($conn, $query)) {
    echo "Announcement added successfully.";
  } else {
    echo "Error: " . mysqli_error($conn);
  }
}

function editAnnouncement($conn) {
  $id = intval($_POST['id']);
  $title = mysqli_real_escape_string($conn, $_POST['title']);
  $content = mysqli_real_escape_string($conn, $_POST['content']);

  $query = "UPDATE announcements 
            SET title = '$title', content = '$content' 
            WHERE id = $id";

  if (mysqli_query($conn, $query)) {
    echo "Announcement updated successfully.";
  } else {
    echo "Error: " . mysqli_error($conn);
  }
}

function toggleStatus($conn) {
  $id = intval($_POST['id']);
  $status = mysqli_real_escape_string($conn, $_POST['status']);

  $query = "UPDATE announcements SET status = '$status' WHERE id = $id";

  if (mysqli_query($conn, $query)) {
    echo "Status updated successfully.";
  } else {
    echo "Error: " . mysqli_error($conn);
  }
}
