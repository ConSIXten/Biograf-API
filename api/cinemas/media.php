<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $cinema_id = isset($_GET['cinema_id']) ? $_GET['cinema_id'] : null;
    
    if (!$cinema_id) {
        throw new Exception('cinema_id is required');
    }
    
    $query = "SELECT media_url, media_type, caption, sort_order 
              FROM cinemas_media 
              WHERE cinema_id = :cinema_id 
              ORDER BY sort_order ASC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':cinema_id', $cinema_id);
    $stmt->execute();
    
    $media = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $media
    ]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>