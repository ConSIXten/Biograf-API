<?php
require("../../config/database.php");

// Roskilde Tekniske Skole coordinates
$roskilde_lat = 55.6415;
$roskilde_lng = 12.0803;

// Get all cinemas with their images and calculate distance
$sql = "SELECT c.*, 
        GROUP_CONCAT(
            CONCAT('{\"id\":', cm.id, ',\"url\":\"', cm.image_url, '\",\"type\":\"', cm.media_type, '\",\"caption\":\"', IFNULL(cm.caption, ''), '\",\"sort_order\":', cm.sort_order, '}')
            ORDER BY cm.sort_order SEPARATOR '|||'
        ) as media,
        ROUND(
            6371 * ACOS(
                COS(RADIANS(:lat)) * COS(RADIANS(c.latitude)) * 
                COS(RADIANS(c.longitude) - RADIANS(:lng)) + 
                SIN(RADIANS(:lat)) * SIN(RADIANS(c.latitude))
            ), 1
        ) as distance_km
        FROM cinemas c
        LEFT JOIN cinemas_media cm ON c.id = cm.cinema_id
        GROUP BY c.id
        ORDER BY distance_km ASC";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':lat', $roskilde_lat);
$stmt->bindParam(':lng', $roskilde_lng);
$stmt->execute();
$cinemas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Base URL for images (if storing relative paths)
$base_url = 'http://localhost:8888/biograf-api'; // Change this for production

// Process media field to convert to proper array
foreach ($cinemas as &$cinema) {
    if ($cinema['media']) {
        $mediaItems = explode('|||', $cinema['media']);
        $cinema['images'] = array_map(function($item) use ($base_url) {
            $image = json_decode($item, true);
            // If URL doesn't start with http, add base URL
            if ($image && isset($image['url']) && !preg_match('/^https?:\/\//', $image['url'])) {
                $image['url'] = $base_url . $image['url'];
            }
            return $image;
        }, $mediaItems);
    } else {
        $cinema['images'] = [];
    }
    unset($cinema['media']);
}

header("Content-Type: application/json; charset=utf-8");
echo json_encode([
    "success" => true,
    "data" => $cinemas
], JSON_PRETTY_PRINT);
?>