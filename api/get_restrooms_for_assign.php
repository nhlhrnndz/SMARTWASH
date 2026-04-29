<?php
// ============================================================
//  SmartWash - Get Restrooms for Assignment
//  File: api/get_restrooms_for_assign.php
//  Method: GET
// ============================================================

require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

// Check if user is logged in and is supervisor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("
        SELECT id, name, location, gender
        FROM restrooms
        WHERE status = 'active'
        ORDER BY name
    ");
    $stmt->execute();
    $restrooms = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $restrooms]);
    
} catch (PDOException $e) {
    error_log("Error in get_restrooms_for_assign.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>