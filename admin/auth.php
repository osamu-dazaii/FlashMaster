<?php
// filepath: c:\Users\Elite\Desktop\logiciels\New folder\htdocs\flashcard_app\admin\auth.php
include '../includes/config.php';

if (isset($_POST['admin_login'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT admin_id, username, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: dashboard.php");
            exit;
        }
    }
    
    $_SESSION['error'] = "Invalid username or password";
    header("Location: login.php");
    exit;
}