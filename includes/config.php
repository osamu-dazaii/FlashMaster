<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$database = "flashcard_app";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Set default timezone
date_default_timezone_set('UTC');
?>
