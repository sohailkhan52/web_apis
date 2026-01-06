<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
$headers = getallheaders();
$auth = $headers['Authorization'] ?? '';
if (!preg_match("/Bearer\s(\S+)/", $auth, $matches)) {
    http_response_code(401);
    echo json_encode(["error" => "token missing"]);
    exit;
}
$token = $matches[1];

use Firebase\JWT\JWT;
use Firebase\JWT\Key;



if (!$token) {
    echo json_encode(["message" => "Token required"]);
    exit;
}

try {
    // Decode the token (Refresh token ideally)
    $decoded = JWT::decode($token, new Key($jwt_secret, 'HS256'));
    $user_id = $decoded->id;

    // Clear refresh token in the database
    $stmt = $db->update("users", ['reset_token' => NULL, 'reset_token_expires' => NULL], ['id' => $user_id]);

    // getting user name in the database
    $user = $db->get("users", ["first_name", "last_name"], ['id' => $user_id]);



    echo json_encode([
        "message" => "Logged out successfully",
        "user" => $user
    ]);

} catch (Exception $e) {


    echo json_encode([
        "message" => "Logout completed"

    ]);
}
?>