<?php
include '../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['error' => 'Not authenticated']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];
$theme_id = $data['theme_id'] ?? 0;

if (!$theme_id) {
  echo json_encode(['error' => 'Invalid theme ID']);
  exit;
}

// Delete progress for this theme's cards
$query = "DELETE up FROM user_progress up 
          JOIN cards c ON up.card_id = c.card_id 
          WHERE up.user_id = ? AND c.theme_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $theme_id);
$result = $stmt->execute();

echo json_encode(['success' => $result]);