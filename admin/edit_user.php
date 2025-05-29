<?php
include '../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['user_id'])) {
    header("Location: users.php");
    exit;
}

$user_id = (int)$_GET['user_id'];

// Get user data
$stmt = $conn->prepare("
    SELECT u.*, 
           COUNT(DISTINCT up.card_id) as cards_completed,
           ROUND(AVG(CASE WHEN up.is_correct = 1 THEN 100 ELSE 0 END), 1) as accuracy
    FROM users u
    LEFT JOIN user_progress up ON u.user_id = up.user_id
    WHERE u.user_id = ?
    GROUP BY u.user_id
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: users.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - FlashMaster Admin</title>
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
            <a href="users.php" class="active"><i class="fas fa-users"></i> Users</a>
            <a href="themes.php"><i class="fas fa-layer-group"></i> Themes</a>
            <a href="cards.php"><i class="fas fa-cards"></i> Cards</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </header>

    <main class="container">
        <div class="page-header">
            <h1>Edit User: <?= htmlspecialchars($user['username']) ?></h1>
            <a href="users.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>

        <div class="edit-form">
            <form action="users.php" method="post" class="user-form">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                
                <div class="input-group">
                    <label>New Password (leave blank to keep current)</label>
                    <input type="password" name="password">
                </div>

                <div class="user-stats">
                    <h3>User Statistics</h3>
                    <div class="stat-grid">
                        <div class="stat-item">
                            <label>Cards Completed</label>
                            <span><?= $user['cards_completed'] ?></span>
                        </div>
                        <div class="stat-item">
                            <label>Accuracy</label>
                            <span><?= $user['accuracy'] ?>%</span>
                        </div>
                        <div class="stat-item">
                            <label>Join Date</label>
                            <span><?= date('M j, Y', strtotime($user['created_at'])) ?></span>
                        </div>
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
</body>
</html>