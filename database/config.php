<?php
session_start();
$servername = "localhost";
$username = "root";  // Change this if needed
$password = "";  // Change this if needed
$dbname = "bcp_pcms";

// Create a MySQL connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
