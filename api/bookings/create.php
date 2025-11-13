<?php
require("../../config/database.php");

// Get posted data
$data = json_decode(file_get_contents("php://input"));

if (empty($data->user_id) || empty($data->showtime_id) || empty($data->seats) || empty($data->total_price)) {
    http_response_code(400);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(["error" => "user_id, showtime_id, seats, and total_price are required"], JSON_PRETTY_PRINT);
    exit;
}

$sql = "INSERT INTO bookings (user_id, showtime_id, seats, total_price, status) 
        VALUES (:user_id, :showtime_id, :seats, :total_price, 'confirmed')";
$stmt = $conn->prepare($sql);

$stmt->bindParam(':user_id', $data->user_id, PDO::PARAM_INT);
$stmt->bindParam(':showtime_id', $data->showtime_id, PDO::PARAM_INT);
$stmt->bindParam(':seats', $data->seats, PDO::PARAM_INT);
$stmt->bindParam(':total_price', $data->total_price);

$stmt->execute();
$booking_id = $conn->lastInsertId();

http_response_code(201);
header("Content-Type: application/json; charset=utf-8");
echo json_encode([
    "success" => true,
    "message" => "Booking created successfully",
    "booking_id" => (int)$booking_id
], JSON_PRETTY_PRINT);
?>