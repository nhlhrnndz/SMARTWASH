<?php
// ============================================================
//  SmartWash - Get All Staff (for management)
//  File: api/get_approved_staff.php
//  Method: GET
//  Access: Supervisor/Admin only
// ============================================================

require_once '../config/db.php';
require_once '../auth/session.php';

requireRole(['supervisor', 'admin']);

header('Content-Type: application/json');

try {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("
        SELECT 
            id,
            full_name,
            username,
            role,
            status,
            created_at
        FROM users 
        WHERE role IN ('supervisor', 'staff')
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    
    $staff = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'staff' => $staff
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}