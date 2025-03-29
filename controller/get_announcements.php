<?php
include '../database/config.php';

$query = "SELECT title, content, DATE_FORMAT(created_at, '%M %d, %Y') as date 
          FROM announcements 
          WHERE status = 'Published' 
          ORDER BY created_at DESC 
          LIMIT 5";

$result = mysqli_query($conn, $query);

$announcements = [];
while ($row = mysqli_fetch_assoc($result)) {
    $announcements[] = $row;
}

echo json_encode(["success" => true, "announcements" => $announcements]);
?>
