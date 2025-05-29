<?php
include '../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle card actions
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            $theme_id = (int)$_POST['theme_id'];
            $image_url = $conn->real_escape_string($_POST['image_url']);
            $correct = $conn->real_escape_string($_POST['correct_answer']);
            $wrong1 = $conn->real_escape_string($_POST['wrong_answer1']);
            $wrong2 = $conn->real_escape_string($_POST['wrong_answer2']);
            $difficulty = (int)$_POST['difficulty'];
            
            $stmt = $conn->prepare("INSERT INTO cards (theme_id, image_url, correct_answer, wrong_answer1, wrong_answer2, difficulty) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssi", $theme_id, $image_url, $correct, $wrong1, $wrong2, $difficulty);
            $stmt->execute();
            break;
            
        case 'edit':
            $card_id = (int)$_POST['card_id'];
            $image_url = $conn->real_escape_string($_POST['image_url']);
            $correct = $conn->real_escape_string($_POST['correct_answer']);
            $wrong1 = $conn->real_escape_string($_POST['wrong_answer1']);
            $wrong2 = $conn->real_escape_string($_POST['wrong_answer2']);
            $difficulty = (int)$_POST['difficulty'];
            
            $stmt = $conn->prepare("UPDATE cards SET image_url = ?, correct_answer = ?, wrong_answer1 = ?, wrong_answer2 = ?, difficulty = ? WHERE card_id = ?");
            $stmt->bind_param("ssssii", $image_url, $correct, $wrong1, $wrong2, $difficulty, $card_id);
            $stmt->execute();
            break;
            
        case 'delete':
            $card_id = (int)$_POST['card_id'];
            $stmt = $conn->prepare("DELETE FROM cards WHERE card_id = ?");
            $stmt->bind_param("i", $card_id);
            $stmt->execute();
            break;
    }
    header("Location: cards.php");
    exit;
}

// Get themes for dropdown
$themes = $conn->query("SELECT * FROM themes ORDER BY theme_name");

// Get cards list with theme names
$cards = $conn->query("
    SELECT c.*, t.theme_name 
    FROM cards c 
    JOIN themes t ON c.theme_id = t.theme_id 
    ORDER BY t.theme_name, c.difficulty
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cards - FlashMaster Admin</title>
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
        <h1>Manage Cards</h1>
        
        <!-- Add Card Form -->
        <section class="add-form">
            <h2>Add New Card</h2>
            <form action="" method="post" class="card-form">
                <input type="hidden" name="action" value="add">
                <div class="input-group">
                    <select name="theme_id" required>
                        <option value="">Select Theme</option>
                        <?php while ($theme = $themes->fetch_assoc()): ?>
                            <option value="<?= $theme['theme_id'] ?>">
                                <?= htmlspecialchars($theme['theme_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="input-group">
                    <input type="text" name="image_url" placeholder="Image URL" required>
                </div>
                <div class="input-group">
                    <input type="text" name="correct_answer" placeholder="Correct Answer" required>
                </div>
                <div class="input-group">
                    <input type="text" name="wrong_answer1" placeholder="Wrong Answer 1" required>
                </div>
                <div class="input-group">
                    <input type="text" name="wrong_answer2" placeholder="Wrong Answer 2" required>
                </div>
                <div class="input-group">
                    <select name="difficulty" required>
                        <option value="1">Easy</option>
                        <option value="2">Medium</option>
                        <option value="3">Hard</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Add Card</button>
            </form>
        </section>

        <!-- Cards List -->
        <section class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Theme</th>
                        <th>Image</th>
                        <th>Correct Answer</th>
                        <th>Wrong Answers</th>
                        <th>Difficulty</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($card = $cards->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($card['theme_name']) ?></td>
                            <td>
                                <?php 
                                $imageUrl = $card['image_url'];
                                // If the URL doesn't start with http/https, assume it's a relative path
                                if (!preg_match('/^https?:\/\//', $imageUrl)) {
                                    $imageUrl = '../' . ltrim($imageUrl, '/');
                                }
                                ?>
                                <img src="<?= htmlspecialchars($imageUrl) ?>" 
                                     alt="<?= htmlspecialchars($card['correct_answer']) ?>"
                                     style="max-width: 100px; max-height: 100px; object-fit: contain;">
                            </td>
                            <td><?= htmlspecialchars($card['correct_answer']) ?></td>
                            <td>
                                <?= htmlspecialchars($card['wrong_answer1']) ?><br>
                                <?= htmlspecialchars($card['wrong_answer2']) ?>
                            </td>
                            <td><?= ['Easy', 'Medium', 'Hard'][$card['difficulty']-1] ?></td>
                            <td class="action-cell">
                                <a href="get_card.php?card_id=<?= $card['card_id'] ?>" class="btn btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="" method="post" class="inline" onsubmit="return confirm('Delete this card?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="card_id" value="<?= $card['card_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>