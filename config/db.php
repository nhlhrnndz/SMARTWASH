<?php
// ============================================================
//  SmartWash — Database Configuration
//  File: config/db.php
// ============================================================
 
define('DB_HOST', 'localhost');
define('DB_NAME', 'smartwash_db');
define('DB_USER', 'root');       // Default XAMPP username
define('DB_PASS', '');           // Default XAMPP password (empty)
define('DB_CHARSET', 'utf8mb4');
 
function getDB(): PDO {
    static $pdo = null;
 
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST
             . ";dbname=" . DB_NAME
             . ";charset=" . DB_CHARSET;
 
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
 
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Show a clean error instead of exposing credentials
            http_response_code(500);
            die(json_encode([
                'success' => false,
                'message' => 'Database connection failed. Please contact the administrator.'
            ]));
        }
    }
 
    return $pdo;
}
 