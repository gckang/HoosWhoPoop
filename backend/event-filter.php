<?php
require('connect-db.php');
session_start();

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status'=>'error','message'=>'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$goal_id = $data['goal_id'] ?? null;
$day = $data['day'] ?? null;

$sql = "SELECT e.event_id, e.goal_id, e.day, e.start_time, e.end_time, h.category
        FROM event e
        JOIN goal g ON e.goal_id = g.goal_id AND e.user_id = g.user_id
        JOIN habit h ON g.habit_id = h.habit_id AND g.user_id = h.user_id
        WHERE e.user_id = :user_id";

$params = ['user_id' => $user_id];

if ($goal_id) {
    $sql .= " AND e.goal_id = :goal_id";
    $params['goal_id'] = $goal_id;
}

if ($day) {
    $sql .= " AND e.day = :day";
    $params['day'] = $day;
}

$stmt = $db->prepare($sql);
$stmt->execute($params);

echo json_encode([
    'status' => 'success',
    'events' => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
