<?php
header("Content-Type: application/json");

// Include DB, helpers
require "config/db.php";

// Get the requested path
$path = trim($_SERVER['REQUEST_URI'], '/');  

// Remove query string
$path = explode('?', $path)[0];
// -------------------------------------------
// Define routes manually in endpoint function
// -------------------------------------------
function endpoint($path){
    $path=explode("/",$path)[1];
    $newpath= "routes/".$path.".php";
    return $newpath;
 
}
$newpath=endpoint($path);
if (!$newpath || !file_exists($newpath)) {
    http_response_code(404);
    echo json_encode([
        "status" => false,
        "message" => "API endpoint not found"
    ]);
    exit;
}
require $newpath;
