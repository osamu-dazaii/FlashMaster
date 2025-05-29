<?php
include '../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle user actions
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            $username = $conn->real_escape_string($_POST['username']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            break;
            
        case 'edit':
            $user_id = (int)$_POST['user_id'];
            $username = $conn->real_escape_string($_POST['username']);
            
            if (!empty($_POST['password'])) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE user_id = ?");
                $stmt->bind_param("ssi", $username, $password, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET username = ? WHERE user_id = ?");
                $stmt->bind_param("si", $username, $user_id);
            }
            $stmt->execute();
            break;
            
        case 'delete':
            $user_id = (int)$_POST['user_id'];
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            break;
    }
    header("Location: users.php");
    exit;
}

// Get users list with stats
$users = $conn->query("
    SELECT u.*, 
           COUNT(DISTINCT up.card_id) as cards_completed,
           ROUND(AVG(CASE WHEN up.is_correct = 1 THEN 100 ELSE 0 END), 1) as accuracy
    FROM users u
    LEFT JOIN user_progress up ON u.user_id = up.user_id
    GROUP BY u.user_id
    ORDER BY u.username
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - FlashMaster Admin</title>
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
        <h1>Manage Users</h1>
        
        <!-- Add User Form -->
        <section class="add-form">
            <h2>Add New User</h2>
            <form action="" method="post" class="user-form">
                <input type="hidden" name="action" value="add">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary">Add User</button>
            </form>
        </section>

        <!-- Users List -->
        <section class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Cards Completed</th>
                        <th>Accuracy</th>
                        <th>Join Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= $user['cards_completed'] ?></td>
                            <td><?= $user['accuracy'] ?>%</td>
                            <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                            <td class="action-cell">
                                <a href="edit_user.php?user_id=<?= $user['user_id'] ?>" class="btn btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="" method="post" class="inline" onsubmit="return confirm('Delete this user?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
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

       
</body>
</html>