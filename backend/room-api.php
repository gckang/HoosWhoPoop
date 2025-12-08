<?php
session_start();
ob_start();

ini_set('display_errors', 0);
ini_set('log_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
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

    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {

        case 'GET':
            if (isset($_GET['room_id'])) {
                $room_id = intval($_GET['room_id']);
                $members = getRoomMembers($db, $room_id);
                echo json_encode($members);
                break;
            }

            // otherwise → list user's rooms
            $rooms = getAllUserRooms($db, $current_user_id);
            echo json_encode($rooms);
            break;


        case 'POST':
            $data = json_decode(file_get_contents('php://input'));

            if (!isset($data->owner_id)) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'owner_id required.'
                ]);
                break;
            }

            $owner_id = intval($data->owner_id);
            $new_room = createRoom($db, $owner_id);

            if (!$new_room) {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Room insert failed.'
                ]);
                break;
            }

            echo json_encode([
                'status' => 'success',
                'room' => $new_room
            ]);
            break;



        case 'DELETE':
            $data = json_decode(file_get_contents("php://input"));

            if (!isset($data->room_id)) {
                http_response_code(400);
                echo json_encode([
                    "status" => "error",
                    "message" => "room_id is required."
                ]);
                exit();
            }

            // DELETE
            $room_id = intval($data->room_id);
            $deleted = deleteRoom($db, $current_user_id, $room_id);


            echo json_encode([
                "status" => $deleted ? "success" : "error",
                "message" => $deleted ? "Room deleted." : "Room not found or no permission."
            ]);
            break;


        default:
            http_response_code(405);
            echo json_encode([
                "status" => "error",
                "message" => "Method not allowed."
            ]);
            break;
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
