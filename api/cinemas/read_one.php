<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require("../../config/database.php");

$cinema_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$cinema_id) {
    http_response_code(400);
    echo json_encode(["error" => "Cinema ID is required"], JSON_PRETTY_PRINT);
    exit;
}

// Get cinema with all its images
$sql = "SELECT c.* FROM cinemas c WHERE c.id = :id LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":id", $cinema_id, PDO::PARAM_INT);
$stmt->execute();
$cinema = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cinema) {
    http_response_code(404);
    echo json_encode(["error" => "Cinema not found"], JSON_PRETTY_PRINT);
    exit;
}

// Get all media for this cinema
$media_sql = "SELECT id, image_url, media_type, caption, sort_order 
              FROM cinemas_media 
              WHERE cinema_id = :cinema_id 
              ORDER BY sort_order";
$media_stmt = $conn->prepare($media_sql);
$media_stmt->bindParam(":cinema_id", $cinema_id, PDO::PARAM_INT);
$media_stmt->execute();
$images = $media_stmt->fetchAll(PDO::FETCH_ASSOC);

// Base URL for images
$base_url = 'http://localhost:8888/biograf-api';

// Add base URL to relative paths
foreach ($images as &$image) {
    if (!preg_match('/^https?:\/\//', $image['image_url'])) {
        $image['url'] = $base_url . $image['image_url'];
    } else {
        $image['url'] = $image['image_url'];
    }
    unset($image['image_url']); // Remove old key
}

$cinema['images'] = $images;

header("Content-Type: application/json; charset=utf-8");
echo json_encode([
    "success" => true,
    "data" => $cinema
], JSON_PRETTY_PRINT);
?>