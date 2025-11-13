<?php
include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    $tables = ['cinemas', 'users', 'showtimes', 'bookings'];
    $result = [];
    
    foreach ($tables as $table) {
        try {
            $query = "DESCRIBE " . $table;
            $stmt = $db->prepare($query);
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $result[$table] = $columns;
        } catch(PDOException $e) {
            $result[$table] = "Error: " . $e->getMessage();
        }
    }
    
    echo json_encode([
        "success" => true,
        "message" => "Database structure",
        "data" => $result
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
}
?>
