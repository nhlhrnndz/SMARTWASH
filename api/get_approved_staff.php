<?php
// ============================================================
//  SmartWash - Get Approved Staff (Maintenance only)
//  File: api/get_approved_staff.php
//  Method: GET
// ============================================================

error_reporting(0);
ini_set('display_errors', 0);

require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

function sendResponse($success, $data = null, $error = null) {
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'error' => $error
    ]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    sendResponse(false, null, 'Not logged in');
}

if ($_SESSION['role'] !== 'supervisor') {
    sendResponse(false, null, 'Unauthorized');
}

try {
    $pdo = getDB();
    
    // Get maintenance staff with their assigned restrooms
    $stmt = $pdo->prepare("
        SELECT 
            u.id, 
            u.full_name, 
            u.username, 
            u.role, 
            u.status, 
            DATE_FORMAT(u.created_at, '%Y-%m-%d') as joined_date,
            (
                SELECT GROUP_CONCAT(r.name SEPARATOR ', ')
                FROM staff_restroom_assignments sra
                JOIN restrooms r ON sra.restroom_id = r.id
                WHERE sra.user_id = u.id
            ) as assigned_restrooms
        FROM users u
        WHERE u.role = 'maintenance'
        ORDER BY u.created_at DESC
    ");
    $stmt->execute();
    
    $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    sendResponse(true, $staff);
    
} catch (Exception $e) {
    sendResponse(false, null, $e->getMessage());
}
?>