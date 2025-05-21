<?php
include 'config.php';

if (isset($_POST['register'])) {
  $username = $conn->real_escape_string($_POST['username']);
  
  // Check if username exists
  $check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
  $check->bind_param("s", $username);
  $check->execute();
  $result = $check->get_result();
  
  if ($result->num_rows > 0) {
    $_SESSION['error'] = "Username already taken. Please choose another.";
    header("Location: ../index.php");
    exit;
  }
  
  // Hash password
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  // Create user
  $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
  $stmt->bind_param("ss", $username, $password);
  
  if ($stmt->execute()) {
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['username'] = $username;
    header("Location: ../dashboard.php");
  } else {
    $_SESSION['error'] = "Registration failed. Please try again.";
    header("Location: ../index.php");
  }
  exit;
}

if (isset($_POST['login'])) {
  $username = $conn->real_escape_string($_POST['username']);
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT user_id, username, password FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['user_id'];
      $_SESSION['username'] = $user['username'];
      header("Location: ../dashboard.php");
      exit;
    }
  }
  
  $_SESSION['error'] = "Invalid username or password.";
  header("Location: ../index.php");
  exit;
}
?>
