<?php
header("Content-Type: application/json");
function response404() {
    http_response_code(404);
    echo json_encode([
        "status" => false,
        "message" => "API endpoint not found"
    ]);
    exit;
}

// Include DB, helpers
require "Config/db.php";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');
$segments = explode('/', $uri);
define('BASE_PATH', 'project');

if ($segments[0] !== BASE_PATH) {
    response404();
}

$resource = $segments[1] ?? null;

if (!$resource || !preg_match('/^[a-zA-Z0-9_-]+$/', $resource)) {
    response404();
}

$routeFile = __DIR__ . "/routes/{$resource}.php";

if (!file_exists($routeFile)) {
    response404();
}

require $routeFile;
