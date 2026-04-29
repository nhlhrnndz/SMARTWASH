<?php
// ============================================================
//  SmartWash - Get All Restrooms
//  File: api/get_all_restrooms.php
//  Method: GET
// ============================================================

require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

try {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("
        SELECT 
            r.id,
            r.name,
            r.location,
            r.gender,
            r.status,
            (SELECT soap_level FROM sensor_readings 
             WHERE restroom_id = r.id 
             ORDER BY recorded_at DESC LIMIT 1) as soap_level,
            (SELECT air_quality FROM sensor_readings 
             WHERE restroom_id = r.id 
             ORDER BY recorded_at DESC LIMIT 1) as air_quality,
            (SELECT COUNT(*) > 0 FROM alerts 
             WHERE restroom_id = r.id AND status = 'active') as has_alert
        FROM restrooms r
        WHERE r.status = 'active' AND r.id != 999
        ORDER BY r.name ASC
    ");
    $stmt->execute();
    
    $restrooms = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $restrooms,
        'total' => count($restrooms)
    ]);
    
} catch (PDOException $e) {
    error_log("Error in get_all_restrooms.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}
?>