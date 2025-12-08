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

        // ----------------------------------------------------
        // GET: All rooms user is in
        // ----------------------------------------------------
        case 'GET':
            $rooms = getAllUserRooms($db, $current_user_id);
            echo json_encode($rooms);
            break;

        // ----------------------------------------------------
        // POST: Create room (no room_name)
        // ----------------------------------------------------
        case 'POST':
            try {
                $room = createRoom($db, $current_user_id);

                if (!$room) {
                    http_response_code(500);
                    echo json_encode([
                        "status" => "error",
                        "message" => "Room creation failed."
                    ]);
                    exit();
                }

                http_response_code(201);
                echo json_encode([
                    "status" => "success",
                    "message" => "Room created successfully.",
                    "room" => $room
                ]);

            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    "status" => "error",
                    "message" => "Database error while creating room.",
                    "details" => $e->getMessage()
                ]);
            }
            break;

        // ----------------------------------------------------
        // DELETE: Delete a room (owner only)
        // ----------------------------------------------------
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
