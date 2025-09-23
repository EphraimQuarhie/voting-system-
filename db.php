<?php
// db.php - Database connection for Voting System
$host = 'localhost';
$db   = 'voting_system';
$user = 'root'; // Change if your MySQL username is different
$pass = '';    // Change if your MySQL password is set

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>
