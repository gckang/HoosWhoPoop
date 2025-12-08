<?php
session_start();
ob_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    ob_end_flush();
    exit();
}

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not logged in.", 401);
    }

    $current_user_id = $_SESSION['user_id'];

    require_once('connect-db.php');
    require_once('room-sql.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'));

        if (!isset($data->room_id) || !isset($data->user_ids) || !is_array($data->user_ids)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'room_id and user_ids array required.'
            ]);
            exit();
        }

        $room_id = intval($data->room_id);
        $user_ids = array_map('intval', $data->user_ids);

        $added = inviteUsersToRoom($db, $current_user_id, $room_id, $user_ids);

        echo json_encode([
            'status' => $added ? 'success' : 'error',
            'message' => $added ? 'Users invited successfully.' : 'Failed to invite users. Check permissions or duplicates.'
        ]);
    } else {
        http_response_code(405);
        echo json_encode([
            'status' => 'error',
            'message' => 'Method not allowed.'
        ]);
    }

} catch (Throwable $e) {
    ob_clean();
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine()
    ]);
} finally {
    ob_end_flush();
}
?>
