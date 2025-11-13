<?php
// CORS Headers - allow all origins
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Use environment variables for production, fallback to local for development
$host = getenv('DB_HOST') ?: "localhost";
$db = getenv('DB_NAME') ?: "biograf_db";
$user = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASSWORD') ?: "root";
$port = getenv('DB_PORT') ?: "3306";

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $error) {
    http_response_code(500);
    die(json_encode(["error" => "Connection failed: " . $error->getMessage()]));
}
?>