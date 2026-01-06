<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
// Load database connection


// Receive POST inputs safely
$fname = $_POST["fname"] ?? '';
$lname = $_POST["lname"] ?? '';
$phone = $_POST["phone"] ?? '';
$ccode = $_POST["phone_country_code"] ?? '';
$email = $_POST["email"] ?? '';
$address = $_POST["address"] ?? null;
$password = $_POST["password"] ?? '';
$role = $_POST["role"] ?? 'agent';   // Default role = agent

// Basic required field validation
if(!$fname && !$email && !$password){
    echo json_encode(["message" => "All fields are required"]);
    exit;
}

// Check if email already exists in database
$check = $db->has("users", ["email" => $email]);

if($check){
    echo json_encode(["message" => "Email already registered"]);
    exit;
}

// Hash password for secure storage
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Insert new user into the database
$insert = $db->insert("users", [
    "first_name" => $fname,
    "last_name" => $lname,
    "phone" => $phone,
    "phone_country_code" => $ccode,
    "email" => $email,
    "password" => $hashed,
    "role" => $role,
    "address" => $address,
    "created_at" => date('Y-m-d H:i:s'),
    "status" => "active"
]);

// Response after insert
if($insert){
    echo json_encode([
        "message" => "User registered successfully",
        "role" => $role
    ]);
}else{
    echo json_encode(["message" => "Registration failed"]);
}

?>
