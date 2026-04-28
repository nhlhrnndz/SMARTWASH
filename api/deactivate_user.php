<?php
// ============================================================
//  SmartWash - Deactivate User
//  File: api/deactivate_user.php
//  Method: POST
// ============================================================

require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

// Check if user is logged in and is supervisor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing user_id']);
    exit;
}

$user_id = (int)$input['user_id'];
$system_restroom_id = 999; // System restroom ID

// Prevent supervisor from deactivating themselves
if ($user_id === $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'error' => 'You cannot deactivate your own account']);
    exit;
}

try {
    $pdo = getDB();
    
    // Get user details
    $stmt = $pdo->prepare("
        SELECT full_name, role, status FROM users WHERE id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }
    
    if ($user['status'] === 'inactive') {
        echo json_encode(['success' => false, 'error' => 'User is already deactivated']);
        exit;
    }
    
    // Deactivate user
    $stmt2 = $pdo->prepare("
        UPDATE users SET status = 'inactive' WHERE id = ?
    ");
    $stmt2->execute([$user_id]);
    
    // Create notification for the deactivated user
    $stmt3 = $pdo->prepare("
        INSERT INTO notifications (user_id, title, message, type, is_read)
        VALUES (?, 'Account Deactivated ⚠️', ?, 'system', 0)
    ");
    $message = "Your account has been deactivated. Please contact your supervisor for more information.";
    $stmt3->execute([$user_id, $message]);
    
    // Add to maintenance logs
    $stmt4 = $pdo->prepare("
        INSERT INTO maintenance_logs (restroom_id, user_id, action, notes)
        VALUES (?, ?, 'user_deactivated', ?)
    ");
    $stmt4->execute([$system_restroom_id, $_SESSION['user_id'], "Deactivated user: {$user['full_name']} ({$user['role']})"]);
    
    echo json_encode([
        'success' => true,
        'message' => "{$user['full_name']} has been deactivated"
    ]);
    
} catch (PDOException $e) {
    error_log("Error in deactivate_user.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>