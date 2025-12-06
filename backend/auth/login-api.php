<?php
// --- START SESSION ---
// This *must* be the very first line of the file.
session_start();

// --- Error & Output Buffering ---
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ob_start();

// --- CORS and HTTP Headers ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true"); // <-- ADD THIS
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    ob_end_flush();
    exit();
}

// --- Global Try/Catch Block ---
try {
    if (!file_exists(dirname(__DIR__) . '/connect-db.php')) {
        die("FATAL: connect-db.php was not found! Path check failed.");
    }
    require('../connect-db.php');  // Provides $db
    require('login-sql.php');     // Provides auth functions

    $method = $_SERVER['REQUEST_METHOD'];

    if ($method !== 'POST') {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
        exit();
    }

    $data = json_decode(file_get_contents('php://input'));

    if (empty($data) || !isset($data->action) || !isset($data->username) || !isset($data->password)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing action, username, or password.']);
        exit();
    }

    $username = trim($data->username);
    $password = $data->password;
    $action = $data->action;

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Username and password cannot be empty.']);
        exit();
    }

    // --- Handle Action ---
    if ($action === 'register') {
        // --- REGISTRATION ---
        
        // Check if user already exists
        $existingUser = findUserByUsername($db, $username);
        if ($existingUser) {
            http_response_code(409); // Conflict
            echo json_encode(['status' => 'error', 'message' => 'Username already taken.']);
            exit();
        }

        // Hash the password
        // 
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Create user
        $newUser = createUser($db, $username, $hashed_password);
        
        http_response_code(201); // Created
        echo json_encode([
            'status' => 'success',
            'message' => 'User created successfully.',
            'user' => $newUser // Does not include password
        ]);

    } elseif ($action === 'login') {
        // --- LOGIN ---
        
        // Find user by username
        $user = findUserByUsername($db, $username);
        if (!$user) {
            http_response_code(404); // Not Found
            echo json_encode(['status' => 'error', 'message' => 'User not found.']);
            exit();
        }

        // Verify the password
        // 
        if (password_verify($password, $user['password'])) {
            // Password is correct!
            
            // --- SET SESSION VARIABLES ---
            // This is where we "log in" the user on the server
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful.',
                'user' => [
                    'user_id' => $user['user_id'],
                    'username' => $user['username']
                ]
            ]);
        } else {
            // Invalid password
            http_response_code(401); // Unauthorized
            echo json_encode(['status' => 'error', 'message' => 'Invalid password.']);
        }

    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
    }

} catch (Throwable $e) {
    // --- Global Error Catcher ---
    http_response_code(500); // Internal Server Error
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