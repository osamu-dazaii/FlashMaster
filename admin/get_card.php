<?php
include '../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['card_id'])) {
    header("Location: cards.php");
    exit;
}

$card_id = (int)$_GET['card_id'];

// Get card data
$stmt = $conn->prepare("
    SELECT c.*, t.theme_name 
    FROM cards c
    JOIN themes t ON c.theme_id = t.theme_id 
    WHERE c.card_id = ?
");
$stmt->bind_param("i", $card_id);
$stmt->execute();
$card = $stmt->get_result()->fetch_assoc();

// Get themes for dropdown
$themes = $conn->query("SELECT * FROM themes ORDER BY theme_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Card - FlashMaster Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/gg.css">
</head>
<body>
    <header class="app-header">
        <div class="logo">
            <i class="fas fa-user-shield"></i>
            <h1>Admin Panel</h1>
        </div>
        <nav>
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="users.php"><i class="fas fa-users"></i> Users</a>
            <a href="themes.php"><i class="fas fa-layer-group"></i> Themes</a>
            <a href="cards.php" class="active"><i class="fas fa-cards"></i> Cards</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </header>

    <main class="container">
        <div class="page-header">
            <h1>Edit Card</h1>
            <a href="cards.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Cards
            </a>
        </div>

        <div class="edit-form">
            <form action="cards.php" method="post" class="card-form">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="card_id" value="<?= $card['card_id'] ?>">
                
                <div class="input-group">
                    <label>Theme</label>
                    <select name="theme_id" required>
                        <option value="">Select Theme</option>
                        <?php while ($theme = $themes->fetch_assoc()): ?>
                            <option value="<?= $theme['theme_id'] ?>" 
                                    <?= ($theme['theme_id'] == $card['theme_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($theme['theme_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="input-group">
                    <label>Image URL</label>
                    <input type="text" name="image_url" value="<?= htmlspecialchars($card['image_url']) ?>" required>
                </div>

                <div class="input-group">
                    <label>Correct Answer</label>
                    <input type="text" name="correct_answer" value="<?= htmlspecialchars($card['correct_answer']) ?>" required>
                </div>

                <div class="input-group">
                    <label>Wrong Answer 1</label>
                    <input type="text" name="wrong_answer1" value="<?= htmlspecialchars($card['wrong_answer1']) ?>" required>
                </div>

                <div class="input-group">
                    <label>Wrong Answer 2</label>
                    <input type="text" name="wrong_answer2" value="<?= htmlspecialchars($card['wrong_answer2']) ?>" required>
                </div>

                <div class="input-group">
                    <label>Difficulty</label>
                    <select name="difficulty" required>
                        <option value="1" <?= ($card['difficulty'] == 1) ? 'selected' : '' ?>>Easy</option>
                        <option value="2" <?= ($card['difficulty'] == 2) ? 'selected' : '' ?>>Medium</option>
                        <option value="3" <?= ($card['difficulty'] == 3) ? 'selected' : '' ?>>Hard</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>