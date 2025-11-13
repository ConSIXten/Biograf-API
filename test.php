<?php
include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo json_encode([
        "success" => true,
        "message" => "Database connection successful!",
        "database" => "biograf_db"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
}
?>
