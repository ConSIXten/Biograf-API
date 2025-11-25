<?php
require("../../config/database.php");

// Get posted data
$data = json_decode(file_get_contents("php://input"));

if (empty($data->cinema_id) || empty($data->user_id) || empty($data->rating)) {
    http_response_code(400);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(["error" => "cinema_id, user_id, and rating are required"], JSON_PRETTY_PRINT);
    exit;
}

// Validate rating (must be 1-5)
if ($data->rating < 1 || $data->rating > 5) {
    http_response_code(400);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(["error" => "Rating must be between 1 and 5"], JSON_PRETTY_PRINT);
    exit;
}

// Check if user already rated this cinema
$check_sql = "SELECT id FROM cinema_ratings WHERE user_id = :user_id AND cinema_id = :cinema_id";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bindParam(':user_id', $data->user_id, PDO::PARAM_INT);
$check_stmt->bindParam(':cinema_id', $data->cinema_id, PDO::PARAM_INT);
$check_stmt->execute();

if ($check_stmt->rowCount() > 0) {
    // Update existing rating
    $sql = "UPDATE cinema_ratings SET rating = :rating, review = :review WHERE user_id = :user_id AND cinema_id = :cinema_id";
} else {
    // Insert new rating
    $sql = "INSERT INTO cinema_ratings (cinema_id, user_id, rating, review) VALUES (:cinema_id, :user_id, :rating, :review)";
}

$stmt = $conn->prepare($sql);
$stmt->bindParam(':cinema_id', $data->cinema_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $data->user_id, PDO::PARAM_INT);
$stmt->bindParam(':rating', $data->rating, PDO::PARAM_INT);
$review = isset($data->review) ? $data->review : null;
$stmt->bindParam(':review', $review);
$stmt->execute();

// Calculate average rating and update cinemas table
$avg_sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as rating_count FROM cinema_ratings WHERE cinema_id = :cinema_id";
$avg_stmt = $conn->prepare($avg_sql);
$avg_stmt->bindParam(':cinema_id', $data->cinema_id, PDO::PARAM_INT);
$avg_stmt->execute();
$avg_result = $avg_stmt->fetch(PDO::FETCH_ASSOC);

// Update cinema with new average
$update_sql = "UPDATE cinemas SET rating = :rating, rating_count = :rating_count WHERE id = :cinema_id";
$update_stmt = $conn->prepare($update_sql);
$avg_rating = round($avg_result['avg_rating'], 1);
$update_stmt->bindParam(':rating', $avg_rating);
$update_stmt->bindParam(':rating_count', $avg_result['rating_count'], PDO::PARAM_INT);
$update_stmt->bindParam(':cinema_id', $data->cinema_id, PDO::PARAM_INT);
$update_stmt->execute();

http_response_code(201);
header("Content-Type: application/json; charset=utf-8");
echo json_encode([
    "success" => true,
    "message" => "Rating submitted successfully",
    "cinema_rating" => $avg_rating,
    "rating_count" => (int)$avg_result['rating_count']
], JSON_PRETTY_PRINT);
?>
