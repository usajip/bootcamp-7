<?php
$host = 'localhost';
$username = 'root';
$password = 'new_password';
$database = 'bootcampb6';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// echo "Connected successfully";
?>