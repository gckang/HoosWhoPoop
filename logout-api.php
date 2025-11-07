<?php
// This script logs the user out by destroying the session.

session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

http_response_code(200);
echo json_encode(['status' => 'success', 'message' => 'Logged out successfully.']);
?>