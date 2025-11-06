<?php
// --- CORS and HTTP Headers ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --- Database Connection ---
require('connect-db.php');    // include
require('habit-sql.php');

// --- Hardcoded User ID ---
// Your schema is designed for multiple users. Since we don't have a
// login system, we will hardcode all operations for user_id = 1.
$current_user_id = 1;

// Get the HTTP request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle the request based on the method
switch ($method) {
    case 'GET':
        $habits = getAllHabitsForUser($db, $current_user_id);
        echo json_encode($habits);
        break;

    case 'POST':
        // Handle POST request (Add a new habit for user 1)
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


    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
        break;
}
?>