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
    ob_end_flush(); // send buffered headers
    exit();
}

try {
    // --- CHECK AUTHENTICATION ---
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in. Please log in to access data.', 401);
    }

    // --- Database Connection ---
    require('connect-db.php');    // include
    require('habit-sql.php');

    // --- User ID ---
    $current_user_id = $_SESSION['user_id'];;

    // Get the HTTP request method
    $method = $_SERVER['REQUEST_METHOD'];

    // Handle the request based on the method
    switch ($method) {
        case 'GET':
            $habits = getAllHabitsForUser($db, $current_user_id);
            echo json_encode($habits);
            break;

        case 'POST':
            // Handle POST request
            $data = json_decode(file_get_contents('php://input'));

            // Basic validation: Check for 'category' (was 'goal_text' before)
            if (empty($data) || !isset($data->category) || trim($data->category) === '') {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Habit category is required.']);
                exit();
            }

            $category = trim($data->category);

            $new_habit = addHabitForUser($db, $current_user_id, $category);
            
            http_response_code(201); // Created
            echo json_encode([
                'status' => 'success',
                'message' => 'Habit added successfully.',
                'habit' => $new_habit // Send back the new habit data
            ]);
            break;

        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'));
            if (!isset($data->habit_id)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'habit_id is required']);
                exit();
            }

            $deleted = deleteHabitForUser($db, $current_user_id, $data->habit_id);

            echo json_encode([
                'status' => $deleted ? 'success' : 'error',
                'message' => $deleted ? 'Habit deleted' : 'Habit not found'
            ]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'));
            if (!isset($data->habit_id) || !isset($data->category)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'habit_id and category required']);
                exit();
            }

            $updated = editHabitForUser($db, $current_user_id, $data->habit_id, trim($data->category));

            echo json_encode([
                'status' => $updated ? 'success' : 'error',
                'message' => $updated ? 'Habit updated' : 'Habit not found'
            ]);
            break;

        default:
            http_response_code(405); // Method Not Allowed
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
            break;
    }
} catch (Throwable $e) {
    // --- GLOBAL ERROR HANDLER ---
    ob_clean(); // Clear the output buffer (deletes any HTML warnings)
    
    // Set HTTP status code (use 500 if code not specified)
    $code = $e->getCode();
    if ($code < 100 || $code > 599) {
        $code = 500;
    }
    http_response_code($code);

    // Send a *JSON* error
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);

} finally {
    // --- 8. SEND RESPONSE ---
    ob_end_flush(); // Send the final, clean output
}
?>