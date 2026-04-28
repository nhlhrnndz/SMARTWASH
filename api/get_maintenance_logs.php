<?php
// ============================================================
//  SmartWash - Get Maintenance Logs
//  File: api/get_maintenance_logs.php
//  Method: GET
// ============================================================

require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

try {
    $pdo = getDB();
    
    if ($role === 'supervisor') {
        // Supervisor sees all logs
        $stmt = $pdo->prepare("
            SELECT 
                ml.*,
                r.name as restroom_name,
                u.full_name as staff_name
            FROM maintenance_logs ml
            JOIN restrooms r ON ml.restroom_id = r.id
            JOIN users u ON ml.user_id = u.id
            ORDER BY ml.performed_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
    } else {
        // Maintenance sees only their own logs
        $stmt = $pdo->prepare("
            SELECT 
                ml.*,
                r.name as restroom_name,
                u.full_name as staff_name
            FROM maintenance_logs ml
            JOIN restrooms r ON ml.restroom_id = r.id
            JOIN users u ON ml.user_id = u.id
            WHERE ml.user_id = ?
            ORDER BY ml.performed_at DESC
            LIMIT ?
        ");
        $stmt->execute([$user_id, $limit]);
    }
    
    $logs = $stmt->fetchAll();
    
    // Format for display
    foreach ($logs as &$log) {
        $log['formatted_date'] = date('M d, Y h:i A', strtotime($log['performed_at']));
        $log['action_icon'] = getActionIcon($log['action']);
    }
    
    echo json_encode([
        'success' => true,
        'data' => $logs
    ]);
    
} catch (PDOException $e) {
    error_log("Error in get_maintenance_logs.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}

function getActionIcon($action) {
    $icons = [
        'submitted_checklist' => '📋',
        'checklist_approved' => '✅',
        'checklist_flagged' => '⚠️',
        'soap_refilled' => '🧴',
        'cleaned' => '🧹',
        'system_auto' => '🤖'
    ];
    return $icons[$action] ?? '📝';
}
?>