<?php
// Allow from any origin
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


require("../../config/database.php");

// Get posted data
$data = json_decode(file_get_contents("php://input"));

if (empty($data->name) || empty($data->email) || empty($data->password)) {
    http_response_code(400);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(["error" => "Name, email and password are required"], JSON_PRETTY_PRINT);
    exit;
}

// Check if email already exists
$check_sql = "SELECT id FROM users WHERE email = :email";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bindParam(':email', $data->email);
$check_stmt->execute();

if ($check_stmt->rowCount() > 0) {
    http_response_code(409);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(["error" => "Email already exists"], JSON_PRETTY_PRINT);
    exit;
}

// Hash password and insert user
$hashed_password = password_hash($data->password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':name', $data->name);
$stmt->bindParam(':email', $data->email);
$stmt->bindParam(':password', $hashed_password);
$stmt->execute();

http_response_code(201);
header("Content-Type: application/json; charset=utf-8");
echo json_encode([
    "success" => true,
    "message" => "User registered successfully",
    "user_id" => $conn->lastInsertId()
], JSON_PRETTY_PRINT);
?>