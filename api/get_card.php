<?php
include '../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['error' => 'Not authenticated']);
  exit;
}

$theme_id = $_GET['theme_id'] ?? 0;
$user_id = $_SESSION['user_id'];

$query = "
  SELECT c.* 
  FROM cards c
  LEFT JOIN user_progress up 
    ON c.card_id = up.card_id AND up.user_id = ?
  WHERE c.theme_id = ? AND (up.is_correct IS NULL OR up.is_correct = 0)
  ORDER BY RAND() LIMIT 1
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $theme_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  echo json_encode($result->fetch_assoc());
} else {
  echo json_encode(['completed' => true]);
}
