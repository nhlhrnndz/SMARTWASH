<?php
// config/db.php - Database connection configuration
session_start();

$host = 'localhost';
$dbname = 'smartwash_db';
$username = 'root';  // XAMPP default username
$password = '';      // XAMPP default password (empty)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Optional: Set timezone
    $pdo->exec("SET time_zone = '+08:00'");
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to get database connection (alternative method)
function getDB() {
    global $pdo;
    return $pdo;
}
?>