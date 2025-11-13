<?php
require("../../config/database.php");

// Get posted data
$data = json_decode(file_get_contents("php://input"));

if (empty($data->email) || empty($data->password)) {
    http_response_code(400);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(["error" => "Email and password are required"], JSON_PRETTY_PRINT);
    exit;
}

$sql = "SELECT id, name, email, password FROM users WHERE email = :email LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':email', $data->email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($data->password, $user['password'])) {
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "data" => [
            "id" => (int)$user['id'],
            "name" => $user['name'],
            "email" => $user['email']
        ]
    ], JSON_PRETTY_PRINT);
} else {
    http_response_code(401);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(["error" => "Invalid email or password"], JSON_PRETTY_PRINT);
}
?>