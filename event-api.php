<?php
// --- Error & Output Buffering ---
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ob_start();

// --- CORS and HTTP Headers ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    ob_end_flush();
    exit();
}

// --- Global Try/Catch Block ---
try {
    require('connect-db.php');    // include
    require('event-sql.php');   

    $current_user_id = 1; // Hardcoded user ID
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            $events = getAllEventsForUser($db, $current_user_id);
            echo json_encode($events);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'));
            if (empty($data) || !isset($data->goal_id) || !isset($data->day) || !isset($data->start_time) || !isset($data->end_time)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Missing fields: goal_id, day, start_time, end_time.']);
            } else {
                $new_event = addEventForUser(
                    $db, 
                    $current_user_id, 
                    $data->goal_id,
                    $data->day,
                    $data->start_time,
                    $data->end_time
                );
                http_response_code(201);
                echo json_encode(['status' => 'success', 'event' => $new_event]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
            break;
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Server Error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
} finally {
    ob_end_flush();
}
?>