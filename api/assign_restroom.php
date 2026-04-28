<?php
// ============================================================
//  SmartWash - Assign Restroom to Staff
//  File: api/assign_restroom.php
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

if (!$input || !isset($input['user_id']) || !isset($input['restroom_id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields: user_id and restroom_id']);
    exit;
}

$user_id = (int)$input['user_id'];
$restroom_id = (int)$input['restroom_id'];

try {
    $pdo = getDB();
    
    // Check if user exists and is maintenance
    $stmt = $pdo->prepare("
        SELECT id, full_name FROM users WHERE id = ? AND role = 'maintenance' AND status = 'active'
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'User not found or not a maintenance staff']);
        exit;
    }
    
    // Check if restroom exists
    $stmt2 = $pdo->prepare("
        SELECT id, name FROM restrooms WHERE id = ? AND status = 'active'
    ");
    $stmt2->execute([$restroom_id]);
    $restroom = $stmt2->fetch();
    
    if (!$restroom) {
        echo json_encode(['success' => false, 'error' => 'Restroom not found']);
        exit;
    }
    
    // Check if assignment already exists
    $stmt3 = $pdo->prepare("
        SELECT id FROM staff_restroom_assignments WHERE user_id = ? AND restroom_id = ?
    ");
    $stmt3->execute([$user_id, $restroom_id]);
    
    if ($stmt3->fetch()) {
        echo json_encode(['success' => false, 'error' => 'This restroom is already assigned to this staff member']);
        exit;
    }
    
    // Create assignment
    $stmt4 = $pdo->prepare("
        INSERT INTO staff_restroom_assignments (user_id, restroom_id, assigned_at)
        VALUES (?, ?, NOW())
    ");
    $stmt4->execute([$user_id, $restroom_id]);
    
    // Create notification for the staff member
    $stmt5 = $pdo->prepare("
        INSERT INTO notifications (user_id, title, message, type, is_read)
        VALUES (?, 'New Restroom Assigned 📋', ?, 'system', 0)
    ");
    $message = "You have been assigned to {$restroom['name']}. Please check your assigned restrooms.";
    $stmt5->execute([$user_id, $message]);
    
    // Add to maintenance logs
    $stmt6 = $pdo->prepare("
        INSERT INTO maintenance_logs (restroom_id, user_id, action, notes)
        VALUES (?, ?, 'restroom_assigned', ?)
    ");
    $stmt6->execute([$restroom_id, $_SESSION['user_id'], "Assigned {$restroom['name']} to {$user['full_name']}"]);
    
    echo json_encode([
        'success' => true,
        'message' => "{$restroom['name']} has been assigned to {$user['full_name']}"
    ]);
    
} catch (PDOException $e) {
    error_log("Error in assign_restroom.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>