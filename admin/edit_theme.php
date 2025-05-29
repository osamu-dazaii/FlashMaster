<?php
include '../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['theme_id'])) {
    header("Location: themes.php");
    exit;
}

$theme_id = (int)$_GET['theme_id'];

// Get theme data
$stmt = $conn->prepare("
    SELECT t.*, COUNT(c.card_id) as total_cards 
    FROM themes t
    LEFT JOIN cards c ON t.theme_id = c.theme_id
    WHERE t.theme_id = ?
    GROUP BY t.theme_id
");
$stmt->bind_param("i", $theme_id);
$stmt->execute();
$theme = $stmt->get_result()->fetch_assoc();

if (!$theme) {
    header("Location: themes.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Theme - FlashMaster Admin</title>
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
            <a href="themes.php" class="active"><i class="fas fa-layer-group"></i> Themes</a>
            <a href="cards.php"><i class="fas fa-cards"></i> Cards</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </header>

    <main class="container">
        <div class="page-header">
            <h1>Edit Theme: <?= htmlspecialchars($theme['theme_name']) ?></h1>
            <a href="themes.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Themes
            </a>
        </div>

        <div class="edit-form">
            <form action="themes.php" method="post" class="theme-form">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="theme_id" value="<?= $theme['theme_id'] ?>">
                
                <div class="input-group">
                    <label>Theme Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($theme['theme_name']) ?>" required>
                </div>
                
                <div class="input-group">
                    <label>Theme Color</label>
                    <div class="color-input">
                        <input type="color" name="color_code" value="<?= $theme['color_code'] ?>" required>
                        <span class="color-value"><?= $theme['color_code'] ?></span>
                    </div>
                </div>

                <div class="theme-stats">
                    <h3>Theme Statistics</h3>
                    <div class="stat-item">
                        <label>Total Cards</label>
                        <span><?= $theme['total_cards'] ?></span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
    // Update color value display when color is changed
    document.querySelector('input[type="color"]').addEventListener('input', function(e) {
        document.querySelector('.color-value').textContent = e.target.value;
    });
    </script>
</body>
</html>