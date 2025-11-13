<?php
require("../../config/database.php");

// Get query parameters
$cinema_id = isset($_GET['cinema_id']) ? (int)$_GET['cinema_id'] : null;
$movie_id = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : null;
$date = isset($_GET['date']) ? $_GET['date'] : null;

$sql = "SELECT s.*, 
               c.name as cinema_name, 
               c.address as cinema_address, 
               c.city as cinema_city, 
               c.rating as cinema_rating,
               (SELECT image_url FROM cinemas_media WHERE cinema_id = c.id ORDER BY sort_order LIMIT 1) as cinema_image
        FROM showtimes s 
        LEFT JOIN cinemas c ON s.cinema_id = c.id 
        WHERE 1=1";

$params = [];

if ($cinema_id) {
    $sql .= " AND s.cinema_id = :cinema_id";
    $params[':cinema_id'] = $cinema_id;
}

if ($movie_id) {
    $sql .= " AND s.movie_id = :movie_id";
    $params[':movie_id'] = $movie_id;
}

if ($date) {
    $sql .= " AND s.show_date = :date";
    $params[':date'] = $date;
}

$sql .= " ORDER BY s.show_date, s.show_time";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$showtimes = $stmt->fetchAll(PDO::FETCH_ASSOC);

header("Content-Type: application/json; charset=utf-8");
echo json_encode([
    "success" => true,
    "data" => $showtimes
], JSON_PRETTY_PRINT);
?>