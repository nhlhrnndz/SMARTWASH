<?php
// ============================================================
//  SmartWash - Get Pending Registrations
//  File: api/get_pending_registrations.php
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
        SELECT id, full_name, username, role, requested_at, status
        FROM registration_requests
        WHERE status = 'pending'
        ORDER BY requested_at ASC
    ");
    $stmt->execute();
    $requests = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $requests]);
    
} catch (PDOException $e) {
    error_log("Error in get_pending_registrations.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>