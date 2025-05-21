<?php 
include 'includes/config.php'; 
if (isset($_SESSION['user_id'])) {
  header("Location: dashboard.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FlashMaster - Learn with Flashcards</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="assets/css/gg.css">
</head>
<body>
  <div class="landing-page">
    <div class="app-branding">
      <div class="logo">
        <i class="fas fa-brain"></i>
        <h1>FlashMaster</h1>
      </div>
      <p class="tagline">Learn anything, anytime, anywhere!</p>
    </div>
    
    <div class="auth-container">
      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
          <?= $_SESSION['error']; ?>
          <?php unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>
      
      <div class="tabs">
        <button class="tab-btn active" data-tab="login">Login</button>
        <button class="tab-btn" data-tab="register">Register</button>
      </div>
      
      <div class="tab-content active" id="login">
        <form action="includes/auth.php" method="post">
          <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Username" required>
          </div>
          <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
          </div>
          <button type="submit" name="login" class="btn btn-primary">
            <i class="fas fa-sign-in-alt"></i> Login
          </button>
        </form>
      </div>
      
      <div class="tab-content" id="register">
        <form action="includes/auth.php" method="post">
          <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Choose Username" required>
          </div>
          <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Create Password" required minlength="6">
          </div>
          <button type="submit" name="register" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Create Account
          </button>
        </form>
      </div>
    </div>
    
    <div class="features">
      <div class="feature">
        <i class="fas fa-th"></i>
        <h3>Multiple Themes</h3>
        <p>Learn with categorized flashcards</p>
      </div>
      <div class="feature">
        <i class="fas fa-chart-line"></i>
        <h3>Track Progress</h3>
        <p>See your learning improvement</p>
      </div>
      <div class="feature">
        <i class="fas fa-medal"></i>
        <h3>Master Topics</h3>
        <p>Get better with each session</p>
      </div>
    </div>
  </div>

  <script>
    // Tab switching logic
    document.querySelectorAll('.tab-btn').forEach(button => {
      button.addEventListener('click', () => {
        // Remove active class from all buttons and content
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        // Add active class to clicked button
        button.classList.add('active');
        
        // Show corresponding content
        document.getElementById(button.dataset.tab).classList.add('active');
      });
    });
  </script>
</body>
</html>
