<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Test API connectivity
echo json_encode([
    "success" => true,
    "message" => "API is reachable!",
    "timestamp" => date('Y-m-d H:i:s'),
    "server_info" => [
        "php_version" => phpversion(),
        "request_method" => $_SERVER['REQUEST_METHOD'],
        "request_uri" => $_SERVER['REQUEST_URI'],
        "http_origin" => $_SERVER['HTTP_ORIGIN'] ?? 'not set'
    ]
], JSON_PRETTY_PRINT);
?>
