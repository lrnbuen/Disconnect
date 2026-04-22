<?php
$host = 'localhost';
$db   = 'disconnect';
$user = 'root';
$pass = '';

// connect to mysql database
$conn = new mysqli($host, $user, $pass, $db);

// stop the page if the connection fails
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>
