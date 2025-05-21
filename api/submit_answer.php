<?php
include '../includes/config.php';
include '../includes/functions.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['error' => 'Not authenticated']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];
$card_id = $data['card_id'] ?? 0;
$answer = $data['answer'] ?? '';

if (!$card_id) {
  echo json_encode(['error' => 'Invalid card ID']);
  exit;
}

$correct_answer = getCorrectAnswer($card_id);
$is_correct = ($answer === $correct_answer) ? 1 : 0;

// Update progress
$stmt = $conn->prepare("
  INSERT INTO user_progress (user_id, card_id, is_correct, attempts)
  VALUES (?, ?, ?, 1)
  ON DUPLICATE KEY UPDATE 
    is_correct = VALUES(is_correct),
    attempts = attempts + 1,
    last_shown = CURRENT_TIMESTAMP
");
$stmt->bind_param("iii", $user_id, $card_id, $is_correct);
$stmt->execute();

echo json_encode([
  'is_correct' => $is_correct,
  'correct_answer' => $correct_answer
]);