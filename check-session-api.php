<?php
// This API's only job is to check if a user is logged in
// and return their data.

session_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
} else {
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'user' => [
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username']
        ]
    ]);
}
?>