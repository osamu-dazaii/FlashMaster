<?php
include '../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle theme actions
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            $name = $conn->real_escape_string($_POST['name']);
            $color = $conn->real_escape_string($_POST['color_code']);
            $stmt = $conn->prepare("INSERT INTO themes (theme_name, color_code) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $color);
            $stmt->execute();
            break;
            
        case 'edit':
            $id = (int)$_POST['theme_id'];
            $name = $conn->real_escape_string($_POST['name']);
            $color = $conn->real_escape_string($_POST['color_code']);
            $stmt = $conn->prepare("UPDATE themes SET theme_name = ?, color_code = ? WHERE theme_id = ?");
            $stmt->bind_param("ssi", $name, $color, $id);
            $stmt->execute();
            break;
            
        case 'delete':
            $id = (int)$_POST['theme_id'];
            $stmt = $conn->prepare("DELETE FROM themes WHERE theme_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            break;
    }
    header("Location: themes.php");
    exit;
}

// Get themes list
$themes = $conn->query("SELECT * FROM themes ORDER BY theme_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Themes - FlashMaster Admin</title>
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
        <h1>Manage Themes</h1>
        
        <!-- Add Theme Form -->
        <section class="add-form">
            <h2>Add New Theme</h2>
            <form action="" method="post">
                <input type="hidden" name="action" value="add">
                <div class="input-group">
                    <input type="text" name="name" placeholder="Theme Name" required>
                    <input type="color" name="color_code" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Theme</button>
            </form>
        </section>

        <!-- Themes List -->
        <section class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Theme</th>
                        <th>Color</th>
                        <th>Cards</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($theme = $themes->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($theme['theme_name']) ?></td>
                            <td>
                                <span class="color-preview" style="background-color: <?= $theme['color_code'] ?>"></span>
                                <?= $theme['color_code'] ?>
                            </td>
                            <td>
                                <?php
                                $stmt = $conn->prepare("SELECT COUNT(*) FROM cards WHERE theme_id = ?");
                                $stmt->bind_param("i", $theme['theme_id']);
                                $stmt->execute();
                                echo $stmt->get_result()->fetch_row()[0];
                                ?>
                            </td>
                            <td class="action-cell">
                                <a href="edit_theme.php?theme_id=<?= $theme['theme_id'] ?>" class="btn btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="" method="post" class="inline" onsubmit="return confirm('Delete this theme?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="theme_id" value="<?= $theme['theme_id'] ?>">
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