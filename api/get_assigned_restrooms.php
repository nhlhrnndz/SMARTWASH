<?php
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $_SESSION['user_id'];

try {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("
        SELECT r.*, 
               (SELECT soap_level FROM sensor_readings WHERE restroom_id = r.id ORDER BY recorded_at DESC LIMIT 1) as soap_level,
               (SELECT air_quality FROM sensor_readings WHERE restroom_id = r.id ORDER BY recorded_at DESC LIMIT 1) as air_quality,
               (SELECT COUNT(*) > 0 FROM alerts WHERE restroom_id = r.id AND status = 'active') as has_alert
        FROM restrooms r
        JOIN staff_restroom_assignments sra ON r.id = sra.restroom_id
        WHERE sra.user_id = ? AND r.status = 'active'
    ");
    $stmt->execute([$user_id]);
    $restrooms = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $restrooms]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>