<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");



use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Get POST inputs safely
$email = $_POST["email"] ?? '';
$password = $_POST["password"] ?? '';

// Validate required fields
if(!$email && !$password){
    echo json_encode(["message" => "Email & Password required"]);
    exit;
}

// Fetch user by email using Medoo
$user = $db->get("users", "*", ["email" => $email]);

// Check if user exists
if(!$user){
    echo json_encode(["message" => "User not found"]);
    exit;
}

// Verify hashed password
if(!password_verify($password, $user['password'])){
    echo json_encode(["message" => "Incorrect password"]);
    exit;
}

// Create Access Token payload (valid for 1 hour)
$access_payload = [
    "id" => $user['id'],
    "email" => $user['email'],
    "name" => $user['first_name'],
    "role" => $user['role'],
    "exp" => time() + 3600 // 1 hour
];

// Encode JWT access token
$access_token = JWT::encode($access_payload, $jwt_secret, 'HS256');

// Token expiry and last login timestamps
$expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
$lastlogin = date('Y-m-d H:i:s');

// Update user record with last login + stored token
$insert = $db->UPDATE("users", [
    "last_login" => $lastlogin,
    "reset_token" => $access_token,
    "reset_token_expires" => $expiry
], [
    "email" => $email
]);

// Response on success
if($insert){
    echo json_encode([
        "message" => "Login successful",
        "access_token" => $access_token,
        "expires_in" => "1 hour",
        "user" => $user
    ]);
}

?>
