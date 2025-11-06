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
require_once 'connect-db.php';

// --- Hardcoded User ID ---
// Your schema is designed for multiple users. Since we don't have a
// login system, we will hardcode all operations for user_id = 1.
$current_user_id = 1;

// Get the HTTP request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle the request based on the method
switch ($method) {
    case 'GET':
        // Handle GET request (Fetch all habits for user 1)
        try {
            // Select habits only for our hardcoded user
            $stmt = $db->prepare("SELECT user_id, habit_id, category FROM habit WHERE user_id = :user_id ORDER BY habit_id DESC");
            $stmt->bindValue(':user_id', $current_user_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $habits = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($habits);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch habits: ' . $e->getMessage()]);
        }
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

        try {
            // ** IMPORTANT LOGIC FOR YOUR SCHEMA **
            // Your `habit` table's `habit_id` is not AUTO_INCREMENT.
            // We must find the next available habit_id for this user.
            
            $stmt_max = $db->prepare("SELECT IFNULL(MAX(habit_id), 0) + 1 AS next_id FROM habit WHERE user_id = :user_id");
            $stmt_max->bindValue(':user_id', $current_user_id, PDO::PARAM_INT);
            $stmt_max->execute();
            $row = $stmt_max->fetch(PDO::FETCH_ASSOC);
            $next_habit_id = $row['next_id'];

            // Now, insert the new habit with the calculated habit_id
            $stmt_insert = $db->prepare(
                "INSERT INTO habit (user_id, habit_id, category) VALUES (:user_id, :habit_id, :category)"
            );
            
            $stmt_insert->bindValue(':user_id', $current_user_id, PDO::PARAM_INT);
            $stmt_insert->bindValue(':habit_id', $next_habit_id, PDO::PARAM_INT);
            $stmt_insert->bindValue(':category', $category, PDO::PARAM_STR);
            
            if ($stmt_insert->execute()) {
                http_response_code(201); // Created
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Habit added successfully.',
                    'user_id' => $current_user_id,
                    'habit_id' => $next_habit_id
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to execute insert statement.']);
            }

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
        break;
}
?>