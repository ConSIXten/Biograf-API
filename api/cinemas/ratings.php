<?php
require("../../config/database.php");

$cinema_id = isset($_GET['cinema_id']) ? (int)$_GET['cinema_id'] : null;

if (!$cinema_id) {
    http_response_code(400);
    echo json_encode(["error" => "cinema_id is required"], JSON_PRETTY_PRINT);
    exit;
}

$sql = "SELECT r.*, u.name as user_name 
        FROM cinema_ratings r 
        LEFT JOIN users u ON r.user_id = u.id 
        WHERE r.cinema_id = :cinema_id 
        ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':cinema_id', $cinema_id, PDO::PARAM_INT);
$stmt->execute();
$ratings = $stmt->fetchAll(PDO::FETCH_ASSOC);

header("Content-Type: application/json; charset=utf-8");
echo json_encode([
    "success" => true,
    "data" => $ratings
], JSON_PRETTY_PRINT);
?>
