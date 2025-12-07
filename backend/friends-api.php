<?php
session_start();
ob_start();

ini_set('display_errors', 0);
ini_set('log_errors', 1);

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

try {
    // --- AUTH CHECK ---
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in. Please log in to access data.', 401);
    }

    // --- DB INCLUDES ---
    require_once('connect-db.php');
    require_once('friends-sql.php');   

    // --- CURRENT USER ---
    $current_user_id = $_SESSION['user_id'];

    // --- HTTP METHOD ---
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {

        // ----------------------------------------------------
        // GET: Retrieve all friends
        // ----------------------------------------------------
        case 'GET':
            $friends = getAllUserFriends($db, $current_user_id);
            echo json_encode($friends);
            break;

        // ----------------------------------------------------
        // POST: Add new friend
        // ----------------------------------------------------
     case 'POST':
    $data = json_decode(file_get_contents('php://input'));

    if (empty($data) || !isset($data->friend_id) || trim($data->friend_id) === '') {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'friend_id is required.'
        ]);
        exit();
    }

    $friend_id = intval(trim($data->friend_id));

    if ($friend_id === $current_user_id) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'You cannot add yourself as a friend.'
        ]);
        exit();
    }

    try {
        $new_friend = addFriend($db, $current_user_id, $friend_id);

        if (!$new_friend) {
            http_response_code(409); // Conflict
            echo json_encode([
                'status' => 'error',
                'message' => 'Friend not added. Already friends or insert failed.'
            ]);
            exit();
        }

        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Friend added successfully.',
            'friend' => $new_friend
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error while adding friend.',
            'details' => $e->getMessage()
        ]);
    }
    break;


        // ----------------------------------------------------
        // DELETE: Remove a friend
        // ----------------------------------------------------
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'));

            if (!isset($data->friend_id)) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'friend_id is  d.'
                ]);
                exit();
            }

            $friend_id = intval($data->friend_id);

            $deleted = deleteFriend($db, $current_user_id, $friend_id);

            echo json_encode([
                'status' => $deleted ? 'success' : 'error',
                'message' => $deleted ? 'Friend removed.' : 'Friend not found.'
            ]);
            break;

        // ----------------------------------------------------
        // Unsupported method
        // ----------------------------------------------------
        default:
            http_response_code(405);
            echo json_encode([
                'status' => 'error',
                'message' => 'Method not allowed.'
            ]);
            break;
    }

} catch (Throwable $e) {

    ob_clean();

    $status = $e->getCode();
    // if ($status < 100 || $status > 599) {
    //     $status = 500;
    // }
    http_response_code($status);

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
