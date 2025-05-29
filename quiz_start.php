<?php
include 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Clear any existing quiz session
unset($_SESSION['quiz']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Quiz - FlashMaster</title>
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
        <div class="quiz-start">
            <h1><i class="fas fa-question-circle"></i> Start New Quiz</h1>
            <p>Test your knowledge with 20 random questions!</p>

            <form action="quiz_game.php" method="post">
                <div class="difficulty-selection">
                    <h2>Select Difficulty</h2>
                    <div class="difficulty-options">
                        <button type="submit" name="difficulty" value="1" class="btn btn-lg btn-primary">
                            <i class="fas fa-star"></i>
                            Easy
                        </button>
                        <button type="submit" name="difficulty" value="2" class="btn btn-lg btn-danger">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            Hard
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</body>
</html>