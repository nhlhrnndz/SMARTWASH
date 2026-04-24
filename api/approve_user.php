<?php
// ============================================================
//  SmartWash - Approve/Reject User Registration
//  File: api/approve_user.php
//  Method: POST
//  Access: Supervisor/Admin only
// ============================================================

require_once '../config/db.php';
require_once '../auth/session.php';

// Only supervisors and admins can approve users
requireRole(['supervisor', 'admin']);

header('Content-Type: application/json');

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Invalid request data']);
    exit;
}

$requestId = $input['request_id'] ?? null;
$action = $input['action'] ?? null; // 'approve' or 'reject'
$reviewNotes = $input['review_notes'] ?? '';

if (!$requestId || !$action || !in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

try {
    $pdo = getDB();
    $pdo->beginTransaction();

    // Get the registration request
    $stmt = $pdo->prepare("SELECT * FROM registration_requests WHERE id = ? AND status = 'pending'");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch();

    if (!$request) {
        throw new Exception('Registration request not found or already processed');
    }

    $currentUserId = $_SESSION['user_id'];

    if ($action === 'approve') {
        // Check if username already exists in users table
        $checkUser = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $checkUser->execute([$request['username']]);
        
        if ($checkUser->fetch()) {
            throw new Exception('Username already exists in system');
        }

        // Insert into users table
$insertUser = $pdo->prepare("
    INSERT INTO users (full_name, username, password, role, status, created_at) 
    VALUES (?, ?, ?, ?, 'active', NOW())
");
$insertUser->execute([
    $request['full_name'],
    $request['username'],
    $request['password'],
    $request['role'] // 'supervisor' or 'maintenance'
]);

        $newUserId = $pdo->lastInsertId();

        // Update registration request status
        $updateRequest = $pdo->prepare("
            UPDATE registration_requests 
            SET status = 'approved', reviewed_by = ?, reviewed_at = NOW() 
            WHERE id = ?
        ");
        $updateRequest->execute([$currentUserId, $requestId]);

        // Create notification for the new user
        $notifStmt = $pdo->prepare("
            INSERT INTO notifications (user_id, title, message, type, created_at) 
            VALUES (?, 'Account Approved', 'Your account has been approved! You can now log in to SmartWash.', 'system', NOW())
        ");
        $notifStmt->execute([$newUserId]);

        $message = "User {$request['full_name']} has been approved successfully.";
        
    } else { // reject
        $updateRequest = $pdo->prepare("
            UPDATE registration_requests 
            SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW() 
            WHERE id = ?
        ");
        $updateRequest->execute([$currentUserId, $requestId]);
        
        $message = "Registration request has been rejected.";
    }

    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'action' => $action
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}