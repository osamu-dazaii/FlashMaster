<?php
include 'includes/config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['quiz'])) {
    header("Location: index.php");
    exit;
}

$quiz = $_SESSION['quiz'];
$correct_count = 0;
foreach ($quiz['answers'] as $answer) {
    if ($answer['correct']) $correct_count++;
}

// Save result to database
$stmt = $conn->prepare("
    INSERT INTO user_quiz_results (user_id, difficulty, score, total_questions) 
    VALUES (?, ?, ?, 20)
");
$stmt->bind_param("iii", $_SESSION['user_id'], $quiz['difficulty'], $correct_count);
$stmt->execute();

// Calculate percentage
$percentage = ($correct_count / 20) * 100;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results - FlashMaster</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/gg.css">
</head>
<body>
    <main class="container">
        <div class="quiz-results">
            <h1>Quiz Complete!</h1>
            
            <div class="score-card">
                <div class="score-circle" style="--percentage: <?= $percentage ?>">
                    <span class="score"><?= $correct_count ?>/20</span>
                    <span class="percentage"><?= round($percentage) ?>%</span>
                </div>
                <div class="difficulty-badge <?= $quiz['difficulty'] == 1 ? 'easy' : 'hard' ?>">
                    <?= $quiz['difficulty'] == 1 ? 'Easy' : 'Hard' ?>
                </div>
            </div>

            <div class="answers-review">
                <h2>Quiz Answers</h2>
                <div class="answers-list">
                    <?php foreach ($quiz['answers'] as $index => $answer): 
                        $card = $quiz['cards'][$index];
                    ?>
                        <div class="answer-item <?= $answer['correct'] ? 'correct' : 'incorrect' ?>">
                            <div class="answer-details">
                                <div class="theme-label">Theme: <?= htmlspecialchars($card['theme_name']) ?></div>
                                <div class="answer-text">
                                    <?= htmlspecialchars($card['correct_answer']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="quiz-actions">
                <a href="quiz_start.php" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Try Again
                </a>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </main>
</body>
</html>
<?php
// Clear quiz session
unset($_SESSION['quiz']);
?>
