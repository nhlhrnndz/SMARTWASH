<?php
// ============================================================
//  SmartWash - Approve or Reject User Registration
//  File: api/approve_user.php
//  Method: POST
// ============================================================

require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

// System restroom ID for logging system actions (must exist in restrooms table)
define('SYSTEM_RESTROOM_ID', 999);

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

if (!$input || !isset($input['request_id']) || !isset($input['action'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$request_id = (int)$input['request_id'];
$action = $input['action']; // 'approve' or 'reject'
$assigned_restrooms = $input['assigned_restrooms'] ?? [];

try {
    $pdo = getDB();
    
    // Get the registration request
    $stmt = $pdo->prepare("
        SELECT * FROM registration_requests WHERE id = ? AND status = 'pending'
    ");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch();
    
    if (!$request) {
        echo json_encode(['success' => false, 'error' => 'Registration request not found or already processed']);
        exit;
    }
    
    if ($action === 'approve') {
        // Start transaction
        $pdo->beginTransaction();
        
        // Insert into users table
        $stmt2 = $pdo->prepare("
            INSERT INTO users (full_name, username, password, role, status, created_at)
            VALUES (?, ?, ?, ?, 'active', NOW())
        ");
        $stmt2->execute([
            $request['full_name'],
            $request['username'],
            $request['password'],
            $request['role']
        ]);
        
        $new_user_id = $pdo->lastInsertId();
        
        // If the user is maintenance, assign restrooms
        if ($request['role'] === 'maintenance' && !empty($assigned_restrooms)) {
            $stmt3 = $pdo->prepare("
                INSERT INTO staff_restroom_assignments (user_id, restroom_id, assigned_at)
                VALUES (?, ?, NOW())
            ");
            
            foreach ($assigned_restrooms as $restroom_id) {
                $stmt3->execute([$new_user_id, $restroom_id]);
            }
        }
        
        // Update registration request status
        $stmt4 = $pdo->prepare("
            UPDATE registration_requests 
            SET status = 'approved', reviewed_by = ?, reviewed_at = NOW()
            WHERE id = ?
        ");
        $stmt4->execute([$_SESSION['user_id'], $request_id]);
        
        // Create notification for the new user
        $stmt5 = $pdo->prepare("
            INSERT INTO notifications (user_id, title, message, type, is_read)
            VALUES (?, 'Account Approved ✅', ?, 'system', 0)
        ");
        
        if ($request['role'] === 'maintenance' && !empty($assigned_restrooms)) {
            $restroom_names = getRestroomNames($pdo, $assigned_restrooms);
            $message = "Your account has been approved! You are assigned to: " . implode(', ', $restroom_names) . ". You can now log in to the SmartWash system.";
        } else {
            $message = "Your account has been approved! You can now log in to the SmartWash system.";
        }
        $stmt5->execute([$new_user_id, $message]);
        
        // Add to maintenance logs - using SYSTEM_RESTROOM_ID for system actions
        $stmt6 = $pdo->prepare("
            INSERT INTO maintenance_logs (restroom_id, user_id, action, notes)
            VALUES (?, ?, 'user_approved', ?)
        ");
        $assignment_note = $request['role'] === 'maintenance' && !empty($assigned_restrooms) 
            ? "Assigned restrooms: " . implode(', ', $assigned_restrooms) 
            : "No restrooms assigned";
        $stmt6->execute([SYSTEM_RESTROOM_ID, $_SESSION['user_id'], "Approved user: {$request['full_name']} as {$request['role']}. $assignment_note"]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => "User {$request['full_name']} has been approved successfully."
        ]);
        
    } elseif ($action === 'reject') {
        // Start transaction for reject
        $pdo->beginTransaction();
        
        // Update registration request status
        $stmt3 = $pdo->prepare("
            UPDATE registration_requests 
            SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW()
            WHERE id = ?
        ");
        $stmt3->execute([$_SESSION['user_id'], $request_id]);
        
        // Add to maintenance logs - using SYSTEM_RESTROOM_ID for system actions
        $stmt5 = $pdo->prepare("
            INSERT INTO maintenance_logs (restroom_id, user_id, action, notes)
            VALUES (?, ?, 'user_rejected', ?)
        ");
        $stmt5->execute([SYSTEM_RESTROOM_ID, $_SESSION['user_id'], "Rejected user: {$request['full_name']} ({$request['role']})"]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => "Registration request for {$request['full_name']} has been rejected."
        ]);
    }
    
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error in approve_user.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}

/**
 * Get restroom names by their IDs
 */
function getRestroomNames($pdo, $restroom_ids) {
    if (empty($restroom_ids)) return [];
    
    $placeholders = implode(',', array_fill(0, count($restroom_ids), '?'));
    $stmt = $pdo->prepare("SELECT name FROM restrooms WHERE id IN ($placeholders)");
    $stmt->execute($restroom_ids);
    $restrooms = $stmt->fetchAll();
    
    return array_column($restrooms, 'name');
}
?>