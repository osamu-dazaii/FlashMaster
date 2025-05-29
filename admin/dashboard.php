<?php
include '../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get statistics
$stats = [
    'total_users' => $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0],
    'total_cards' => $conn->query("SELECT COUNT(*) FROM cards")->fetch_row()[0],
    'total_themes' => $conn->query("SELECT COUNT(*) FROM themes")->fetch_row()[0]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FlashMaster</title>
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
            <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="users.php"><i class="fas fa-users"></i> Users</a>
            <a href="themes.php"><i class="fas fa-layer-group"></i> Themes</a>
            <a href="cards.php"><i class="fas fa-cards"></i> Cards</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </header>

    <main class="container">
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Users</h3>
                    <p class="stat-value"><?= $stats['total_users'] ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Themes</h3>
                    <p class="stat-value"><?= $stats['total_themes'] ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-cards"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Cards</h3>
                    <p class="stat-value"><?= $stats['total_cards'] ?></p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <section class="quick-actions">
            <h2 class="section-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
            <div class="action-buttons">
                <a href="users.php?action=new" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Add New User
                </a>
                <a href="themes.php?action=new" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Theme
                </a>
                <a href="cards.php?action=new" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Card
                </a>
            </div>
        </section>
    </main>

    <footer class="app-footer">
        <p>&copy; <?= date('Y') ?> FlashMaster - Admin Panel</p>
    </footer>
</body>
</html>