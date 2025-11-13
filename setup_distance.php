<?php
require("config/database.php");

try {
    // Add latitude and longitude columns
    $sql1 = "ALTER TABLE `cinemas` ADD COLUMN `latitude` DECIMAL(10, 8) DEFAULT NULL";
    $conn->exec($sql1);
    echo "✅ Added latitude column\n";
    
    $sql2 = "ALTER TABLE `cinemas` ADD COLUMN `longitude` DECIMAL(11, 8) DEFAULT NULL";
    $conn->exec($sql2);
    echo "✅ Added longitude column\n";
    
    echo "\n✅ Success! Now you can add coordinates to your cinemas.\n";
    echo "\nTo add coordinates, update each cinema:\n";
    echo "UPDATE cinemas SET latitude = 55.6761, longitude = 12.5683 WHERE id = 1;\n";
    
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "ℹ️  Columns already exist!\n";
    } else {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}
?>
