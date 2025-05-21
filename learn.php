<?php
include 'includes/config.php';
include 'includes/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['theme_id'])) {
  header("Location: dashboard.php");
  exit;
}

$theme_id = $_GET['theme_id'];
$user_id = $_SESSION['user_id'];

// Get theme info
$stmt = $conn->prepare("SELECT * FROM themes WHERE theme_id = ?");
$stmt->bind_param("i", $theme_id);
$stmt->execute();
$theme = $stmt->get_result()->fetch_assoc();

// Get progress
$progress = getThemeProgress($user_id, $theme_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Learn <?= htmlspecialchars($theme['theme_name']) ?> - FlashMaster</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="assets/css/gg.css">
</head>
<body>
  <header class="app-header">
    <div class="logo">
      <i class="fas fa-brain"></i>
      <h1>FlashMaster</h1>
    </div>
    <nav>
      <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
      <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
  </header>

  <main class="container">
    <div class="learning-header">
      <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Themes</a>
      <h2 class="theme-title">
        <i class="fas <?= $theme['theme_icon'] ?>"></i>
        <?= htmlspecialchars($theme['theme_name']) ?>
      </h2>
    </div>

    <div class="progress-container">
      <div class="progress-stats">
        <span><?= $progress['learned'] ?> of <?= $progress['total'] ?> cards mastered</span>
        <span><?= $progress['percentage'] ?>% complete</span>
      </div>
      <div class="progress-bar">
        <div class="progress" style="width: <?= $progress['percentage'] ?>%; background-color: <?= $theme['color_code'] ?>"></div>
      </div>
    </div>

    <div id="learning-area">
      <div class="loader">
        <i class="fas fa-spinner fa-spin"></i>
        <span>Loading cards...</span>
      </div>
    </div>

    <div id="completion-message" style="display: none;">
      <div class="completion-card">
        <div class="confetti-animation"></div>
        <i class="fas fa-trophy"></i>
        <h3>Congratulations!</h3>
        <p>You've mastered all the cards in this theme.</p>
        <div class="action-buttons">
          <a href="dashboard.php" class="btn btn-primary">
            <i class="fas fa-home"></i> Return to Dashboard
          </a>
          <button id="reset-theme" class="btn btn-secondary">
            <i class="fas fa-redo"></i> Reset Progress & Practice Again
          </button>
        </div>
      </div>
    </div>
  </main>

  <footer class="app-footer">
    <p>&copy; <?= date('Y') ?> FlashMaster - Learn with Flashcards</p>
  </footer>

  <script src="assets/js/confetti.min.js"></script>
  <script>
    // Store theme ID for API calls
    const themeId = <?= $theme_id ?>;
    
    // Initialize learning system
    document.addEventListener('DOMContentLoaded', () => {
      fetchCard();
      
      // Reset theme progress button
      document.getElementById('reset-theme')?.addEventListener('click', () => {
        if (confirm('Are you sure you want to reset your progress for this theme?')) {
          fetch('api/reset_progress.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ theme_id: themeId })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              window.location.reload();
            }
          });
        }
      });
    });
  </script>
  <script src="assets/js/script.js"></script>
</body>
</html>
