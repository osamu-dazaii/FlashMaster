<?php 
include 'includes/config.php';
include 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}

$user_stats = getUserStats($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FlashMaster - Dashboard</title>
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
      <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Home</a>
      <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
  </header>

  <main class="container">
    <div class="welcome-banner">
      <div class="user-greeting">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
        <p>Ready to learn something new today?</p>
      </div>
      <div class="stats-summary">
        <div class="stat">
          <i class="fas fa-check-circle"></i>
          <span><?= $user_stats['learned_cards'] ?></span>
          <p>Cards Mastered</p>
        </div>
        <div class="stat">
          <i class="fas fa-bullseye"></i>
          <span><?= $user_stats['accuracy'] ?>%</span>
          <p>Accuracy</p>
        </div>
      </div>
    </div>

    <section class="themes-section">
      <h2 class="section-title"><i class="fas fa-layer-group"></i> Choose a Theme</h2>
      
      <div class="themes-grid">
        <?php
        $result = $conn->query("SELECT * FROM themes ORDER BY theme_name");
        while ($theme = $result->fetch_assoc()):
          // Get progress for this theme
          $progress = 0;
          foreach ($user_stats['themes_progress'] as $tp) {
            if ($tp['theme_id'] == $theme['theme_id']) {
              $progress = $tp['progress'];
              break;
            }
          }
        ?>
          <a href="learn.php?theme_id=<?= $theme['theme_id'] ?>" class="theme-card" style="border-color: <?= $theme['color_code'] ?>">
            <div class="theme-icon" style="background-color: <?= $theme['color_code'] ?>">
              <i class="fas <?= $theme['theme_icon'] ?>"></i>
            </div>
            <div class="theme-info">
              <h3><?= htmlspecialchars($theme['theme_name']) ?></h3>
              <div class="progress-bar">
                <div class="progress" style="width: <?= $progress ?>%; background-color: <?= $theme['color_code'] ?>"></div>
              </div>
              <span class="progress-text"><?= $progress ?>% complete</span>
            </div>
          </a>
        <?php endwhile; ?>
      </div>
    </section>
  </main>

  <footer class="app-footer">
    <p>&copy; <?= date('Y') ?> FlashMaster - Learn with Flashcards</p>
  </footer>
</body>
</html>
