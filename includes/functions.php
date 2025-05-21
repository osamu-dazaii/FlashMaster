<?php
// Get user statistics
function getUserStats($user_id) {
  global $conn;
  
  $stats = [
    'total_cards' => 0,
    'learned_cards' => 0,
    'accuracy' => 0,
    'themes_progress' => []
  ];
  
  // Get total cards completed
  $query = "SELECT 
      COUNT(DISTINCT c.card_id) as total_cards,
      SUM(CASE WHEN up.is_correct = 1 THEN 1 ELSE 0 END) as learned_cards,
      ROUND(AVG(CASE WHEN up.is_correct = 1 THEN 100 ELSE 0 END), 1) as accuracy
    FROM cards c
    LEFT JOIN user_progress up ON c.card_id = up.card_id AND up.user_id = ?";
  
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($row = $result->fetch_assoc()) {
    $stats['total_cards'] = $row['total_cards'];
    $stats['learned_cards'] = $row['learned_cards'];
    $stats['accuracy'] = $row['accuracy'];
  }
  
  // Get progress by theme
  $query = "SELECT 
      t.theme_id,
      t.theme_name,
      t.color_code,
      COUNT(DISTINCT c.card_id) as theme_total,
      SUM(CASE WHEN up.is_correct = 1 THEN 1 ELSE 0 END) as theme_learned
    FROM themes t
    JOIN cards c ON t.theme_id = c.theme_id
    LEFT JOIN user_progress up ON c.card_id = up.card_id AND up.user_id = ?
    GROUP BY t.theme_id";
  
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  
  while ($row = $result->fetch_assoc()) {
    $progress = ($row['theme_total'] > 0) ? 
      round(($row['theme_learned'] / $row['theme_total']) * 100) : 0;
    
    $stats['themes_progress'][] = [
      'theme_id' => $row['theme_id'],
      'theme_name' => $row['theme_name'],
      'color_code' => $row['color_code'],
      'progress' => $progress,
      'learned' => $row['theme_learned'],
      'total' => $row['theme_total']
    ];
  }
  
  return $stats;
}

// Get correct answer for a card
function getCorrectAnswer($card_id) {
  global $conn;
  $stmt = $conn->prepare("SELECT correct_answer FROM cards WHERE card_id = ?");
  $stmt->bind_param("i", $card_id);
  $stmt->execute();
  $result = $stmt->get_result();
  return $result->fetch_assoc()['correct_answer'];
}

// Update theme progress for session display
function getThemeProgress($user_id, $theme_id) {
  global $conn;
  
  $query = "SELECT 
      COUNT(DISTINCT c.card_id) as total_cards,
      SUM(CASE WHEN up.is_correct = 1 THEN 1 ELSE 0 END) as learned_cards
    FROM cards c
    LEFT JOIN user_progress up ON c.card_id = up.card_id AND up.user_id = ?
    WHERE c.theme_id = ?";
  
  $stmt = $conn->prepare($query);
  $stmt->bind_param("ii", $user_id, $theme_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  
  $progress = [
    'total' => $row['total_cards'],
    'learned' => $row['learned_cards'],
    'percentage' => ($row['total_cards'] > 0) ? 
      round(($row['learned_cards'] / $row['total_cards']) * 100) : 0
  ];
  
  return $progress;
}
?>
