<?php
header("Content-Type: application/json; charset=utf-8");
require("config/database.php");

try {
    // Check if table exists and get columns
    $sql = "DESCRIBE cinemas_media";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "success" => true,
        "table" => "cinemas_media",
        "columns" => $columns
    ], JSON_PRETTY_PRINT);
    
} catch(PDOException $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
