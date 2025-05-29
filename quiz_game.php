<?php
include 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Initialize or continue quiz
if (!isset($_SESSION['quiz']) && isset($_POST['difficulty'])) {
    $difficulty = (int)$_POST['difficulty'];
    
    // Get 20 random cards of selected difficulty
    $stmt = $conn->prepare("
        SELECT c.*, t.theme_name 
        FROM cards c
        JOIN themes t ON c.theme_id = t.theme_id
        WHERE c.difficulty = ?
        ORDER BY RAND()
        LIMIT 20
    ");
    $stmt->bind_param("i", $difficulty);
    $stmt->execute();
    $cards = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Initialize quiz session
    $_SESSION['quiz'] = [
        'difficulty' => $difficulty,
        'current_question' => 0,
        'cards' => $cards,
        'answers' => [],
        'start_time' => time()
    ];
} elseif (!isset($_SESSION['quiz'])) {
    header("Location: quiz_start.php");
    exit;
}

// Handle answer submission
if (isset($_POST['answer'])) {
    $current = $_SESSION['quiz']['current_question'];
    $_SESSION['quiz']['answers'][$current] = [
        'card_id' => $_POST['card_id'],
        'selected' => $_POST['answer'],
        'correct' => $_POST['answer'] === $_POST['correct_answer']
    ];
    
    $_SESSION['quiz']['current_question']++;
    
    // If quiz is complete, redirect to results
    if ($_SESSION['quiz']['current_question'] >= 20) {
        header("Location: quiz_result.php");
        exit;
    }
}

$current = $_SESSION['quiz']['current_question'];
$card = $_SESSION['quiz']['cards'][$current];

// Prepare answer choices
$answers = [
    $card['correct_answer'],
    $card['wrong_answer1'],
    $card['wrong_answer2']
];
shuffle($answers);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - FlashMaster</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/gg.css">
</head>
<body>
    <main class="container">
        <div class="quiz-game">
            <div class="quiz-progress">
                <div class="progress-bar">
                    <div class="progress" style="width: <?= ($current / 20) * 100 ?>%"></div>
                </div>
                <span>Question <?= $current + 1 ?> of 20</span>
            </div>

            <div class="quiz-card">
                <div class="theme-tag" style="background-color: <?= $card['color_code'] ?? '#3498db' ?>">
                    <?= htmlspecialchars($card['theme_name']) ?>
                </div>
                
                <div class="card-image">
                    <img src="<?= htmlspecialchars($card['image_url']) ?>" 
                         alt="Quiz Image">
                </div>

                <form action="" method="post" class="answer-form">
                    <input type="hidden" name="card_id" value="<?= $card['card_id'] ?>">
                    <input type="hidden" name="correct_answer" value="<?= $card['correct_answer'] ?>">
                    
                    <?php foreach ($answers as $answer): ?>
                        <button type="submit" name="answer" value="<?= htmlspecialchars($answer) ?>" 
                                class="btn btn-lg btn-answer">
                            <?= htmlspecialchars($answer) ?>
                        </button>
                    <?php endforeach; ?>
                </form>
            </div>
        </div>
    </main>
</body>
</html>