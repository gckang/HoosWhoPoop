<?php
session_start();
ob_start();

// --- Error & Output Buffering ---
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// --- CORS and HTTP Headers ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    ob_end_flush();
    exit();
}

// --- Global Try/Catch Block ---
try {
    // --- CHECK AUTHENTICATION ---
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in. Please log in to access data.', 401);
    }

    require('connect-db.php');    // include
    require('goal-sql.php');    

    $current_user_id = $_SESSION['user_id'];
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            $goals = getAllGoalsForUser($db, $current_user_id);
            echo json_encode($goals);
            break;

        case 'POST':
            /*TO DO : switch($action) which will be filter_events or post_events. implement filter_events */
            $data = json_decode(file_get_contents('php://input'));
            if (empty($data) || !isset($data->habit_id) || !isset($data->deadline) || !isset($data->goal_time)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields: habit_id, deadline, goal_time.']);
            } else {
                $new_goal = addGoalForUser(
                    $db, 
                    $current_user_id, 
                    $data->habit_id, 
                    $data->deadline, 
                    $data->goal_time
                );
                http_response_code(201);
                echo json_encode(['status' => 'success', 'goal' => $new_goal]);
            }
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'));
            if (!isset($data->goal_id) || !isset($data->deadline) || !isset($data->goal_time)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'gaol_id, deadline, and goal_time required']);
                exit();
            }

            $updated = editGoalForUser($db, $current_user_id, $data->goal_id, $data->habit_id, $data->deadline, $data->goal_time);

            echo json_encode([
                'status' => $updated ? 'success' : 'error',
                'message' => $updated ? 'Goal updated' : 'Goal not found'
            ]);
            break;

        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'));
            if (!isset($data->goal_id)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'goal_id is required']);
                exit();
            }

            $deleted = deleteGoalForUser($db, $current_user_id, $data->goal_id);

            echo json_encode([
                'status' => $deleted ? 'success' : 'error',
                'message' => $deleted ? 'Goal deleted' : 'Goal not found'
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
            break;
    }

} catch (Throwable $e) {
    // --- GLOBAL ERROR HANDLER ---
    ob_clean();
    $code = $e->getCode();
    if ($code < 100 || $code > 599) { $code = 500; }
    http_response_code($code);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
} finally {
    ob_end_flush();
}
?>