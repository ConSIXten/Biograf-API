<?php
// Main entry point for the API
require_once 'middleware/cors.php';

// Simple routing (you can expand this)
$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Remove query string and base path
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace('/biograf-api', '', $path);

// Basic routing
switch ($path) {
    case '/':
    case '/index.php':
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Biograf API is running",
            "version" => "1.0.0",
            "timestamp" => date('Y-m-d H:i:s')
        ]);
        break;
    
    default:
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Endpoint not found"
        ]);
        break;
}
?>
