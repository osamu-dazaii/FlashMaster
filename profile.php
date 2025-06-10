<?php
include 'includes/config.php';
include 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$user_stats = getUserStats($user_id);

// Get user's quiz history
$stmt = $conn->prepare("
    SELECT *
    FROM user_quiz_results
    WHERE user_id = ?
    ORDER BY completed_at DESC
    LIMIT 10
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$quiz_history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile - FlashMaster</title>
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
      <a href="profile.php" class="active"><i class="fas fa-user"></i> Profile</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      <a href="quiz_start.php"><i class="fas fa-question-circle"></i> Quiz</a>

    </nav>
  </header>

  <main class="container">
    <div class="profile-header">
      <div class="user-info">
        <div class="avatar">
          <i class="fas fa-user"></i>
        </div>
        <div class="user-details">
          <h2><?= htmlspecialchars($_SESSION['username']) ?></h2>
          <p>Member since <?= date('F Y') ?></p>
        </div>
      </div>
    </div>

    <div class="stats-overview">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
          <h3>Cards Mastered</h3>
          <p class="stat-value"><?= $user_stats['learned_cards'] ?></p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-bullseye"></i>
        </div>
        <div class="stat-info">
          <h3>Accuracy</h3>
          <p class="stat-value"><?= $user_stats['accuracy'] ?>%</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-layer-group"></i>
        </div>
        <div class="stat-info">
          <h3>Total Cards</h3>
          <p class="stat-value"><?= $user_stats['total_cards'] ?></p>
        </div>
      </div>
    </div>

    <section class="themes-progress">
      <h2 class="section-title"><i class="fas fa-chart-line"></i> Progress by Theme</h2>
      
      <div class="progress-list">
        <?php foreach ($user_stats['themes_progress'] as $theme): ?>
          <div class="theme-progress-item">
            <h3><?= htmlspecialchars($theme['theme_name']) ?></h3>
            <div class="progress-data">
              <div class="progress-bar">
                <div class="progress" style="width: <?= $theme['progress'] ?>%; background-color: <?= $theme['color_code'] ?>"></div>
              </div>
              <div class="progress-stats">
                <span><?= $theme['progress'] ?>%</span>
                <span><?= $theme['learned'] ?>/<?= $theme['total'] ?> cards</span>
              </div>
            </div>
            <a href="learn.php?theme_id=<?= $theme['theme_id'] ?>" class="btn btn-sm">
              <?= ($theme['progress'] == 100) ? 'Review Again' : 'Continue Learning' ?>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="quiz-history">
        <h2><i class="fas fa-history"></i> Recent Quizzes</h2>
        <?php if (empty($quiz_history)): ?>
            <p class="no-data">No quizzes taken yet.</p>
        <?php else: ?>
            <div class="history-cards">
                <?php foreach ($quiz_history as $quiz): ?>
                    <div class="history-card">
                        <div class="quiz-info">
                            <span class="difficulty <?= $quiz['difficulty'] == 1 ? 'easy' : 'hard' ?>">
                                <?= $quiz['difficulty'] == 1 ? 'Easy' : 'Hard' ?>
                            </span>
                            <span class="date">
                                <?= date('M j, Y g:i A', strtotime($quiz['completed_at'])) ?>
                            </span>
                        </div>
                        <div class="score">
                            <strong><?= $quiz['score'] ?>/<?= $quiz['total_questions'] ?></strong>
                            (<?= round(($quiz['score']/$quiz['total_questions'])*100) ?>%)
                        </div>
                        
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
  </main>

  <footer class="app-footer">
    <p>&copy; <?= date('Y') ?> FlashMaster - Learn with Flashcards</p>
  </footer>
</body>
</html>
