<?php
/**
 * MODULES API CONTROLLER
 * 
 * REST API for managing modules with CRUD operations
 * Supports: GET, POST, PUT, DELETE
 * Authentication: JWT Bearer Token
 * 
 * @version 1.0.0
 * @author Sohail khan
 */

// ====================
// CONFIGURATION
// ====================
define('API_NAME', 'Modules API');
define('API_VERSION', '1.0.0');
define('DEFAULT_RECORDS_LIMIT', 10);
// ====================
// HEADERS & CORS
// ====================
header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,OPTIONS");
header("Access-Control-Allow-Headers: Content-type,Authorization");
// --------------------
// Get Bearer token
// --------------------
$headers=getallheaders();
$auth=$headers['Authorization']??"";
if(!preg_match("/Bearer\s(\S+)/",$auth,$matches)){
    http_response_code(401);
    echo json_encode(["status"=>false,"Message"=>"token missing"]);exit;
}
$token=$matches[1];
//-----------------
//JWT libraries
//-----------------
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
// ----------
// Verify JWT
// ----------
try {
    $decoded=JWT::decode($token,new key($jwt_secret,"HS256"));

} catch (Exception $th) {
    http_response_code(401);
    echo json_encode(["status"=>false,"Message"=>"invalid or token expired"]);exit;
}
// --------------------
// Routing logic
// --------------------
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$methods=$_SERVER['REQUEST_METHOD'];
if(!$methods){
    http_response_code(404);
    echo json_encode([
        "status"=>false,
        "Message"=>"Method not found"
    ]);
    exit;
}
?>