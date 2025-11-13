<?php
// Enable error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "PHP is working!<br>";
echo "PHP Version: " . phpversion() . "<br>";

// Test database connection
try {
    include_once 'config/database.php';
    echo "Database file included successfully<br>";
    
    $database = new Database();
    echo "Database object created<br>";
    
    $db = $database->getConnection();
    echo "Connection method called<br>";
    
    if ($db) {
        echo "<strong style='color:green;'>✓ Database connection successful!</strong><br>";
        
        // Test if tables exist
        $tables = ['cinemas', 'users', 'showtimes', 'bookings'];
        echo "<br><strong>Checking tables:</strong><br>";
        
        foreach ($tables as $table) {
            $query = "SELECT COUNT(*) as count FROM " . $table;
            $stmt = $db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "✓ Table '$table' exists - {$result['count']} rows<br>";
        }
        
    } else {
        echo "<strong style='color:red;'>✗ Database connection failed!</strong><br>";
    }
    
} catch (Exception $e) {
    echo "<strong style='color:red;'>ERROR:</strong> " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
