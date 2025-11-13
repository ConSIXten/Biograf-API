<?php
require("../../config/database.php");

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

// Build query based on whether user_id is provided
if ($user_id) {
    $sql = "SELECT b.*, s.movie_id, s.movie_title, s.show_date, s.show_time, s.price,
                   c.name as cinema_name, c.address as cinema_address, c.city as cinema_city,
                   (SELECT image_url FROM cinemas_media WHERE cinema_id = c.id ORDER BY sort_order LIMIT 1) as cinema_image
            FROM bookings b 
            LEFT JOIN showtimes s ON b.showtime_id = s.id
            LEFT JOIN cinemas c ON s.cinema_id = c.id 
            WHERE b.user_id = :user_id 
            ORDER BY b.booking_date DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
} else {
    // Get all bookings if no user_id specified
    $sql = "SELECT b.*, s.movie_id, s.movie_title, s.show_date, s.show_time, s.price,
                   c.name as cinema_name, c.address as cinema_address, c.city as cinema_city,
                   (SELECT image_url FROM cinemas_media WHERE cinema_id = c.id ORDER BY sort_order LIMIT 1) as cinema_image,
                   u.name as user_name, u.email as user_email
            FROM bookings b 
            LEFT JOIN showtimes s ON b.showtime_id = s.id
            LEFT JOIN cinemas c ON s.cinema_id = c.id
            LEFT JOIN users u ON b.user_id = u.id 
            ORDER BY b.booking_date DESC";
    
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

header("Content-Type: application/json; charset=utf-8");
echo json_encode([
    "success" => true,
    "data" => $bookings
], JSON_PRETTY_PRINT);
?>