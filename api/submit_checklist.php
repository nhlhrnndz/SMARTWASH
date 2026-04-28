<?php
// ============================================================
//  SmartWash - Submit Checklist (Maintenance)
//  File: api/submit_checklist.php
//  Method: POST
// ============================================================

require_once '../config/db.php';
session_start();

// Set header for JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

// Check if user is maintenance
if ($_SESSION['role'] !== 'maintenance') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized. Only maintenance staff can submit checklists.']);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed. Use POST.']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
    exit;
}

// Debug: Log received data
error_log("Checklist submission received: " . print_r($input, true));

// Required fields
if (!isset($input['restroom_id']) || !isset($input['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields: restroom_id and user_id']);
    exit;
}

// Verify the user is submitting for themselves
if ($input['user_id'] != $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'error' => 'You can only submit checklists for yourself']);
    exit;
}

try {
    $pdo = getDB();
    
    // Insert checklist
    $stmt = $pdo->prepare("
        INSERT INTO cleanliness_checklists (
            restroom_id, 
            submitted_by, 
            floor_clean, 
            toilets_clean, 
            sinks_clean,
            soap_refilled, 
            trash_emptied, 
            mirrors_clean, 
            odor_free, 
            notes, 
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending'
        )
    ");
    
    $stmt->execute([
        $input['restroom_id'],
        $input['user_id'],
        $input['floor_clean'] ?? 0,
        $input['toilets_clean'] ?? 0,
        $input['sinks_clean'] ?? 0,
        $input['soap_refilled'] ?? 0,
        $input['trash_emptied'] ?? 0,
        $input['mirrors_clean'] ?? 0,
        $input['odor_free'] ?? 0,
        $input['notes'] ?? null
    ]);
    
    $checklist_id = $pdo->lastInsertId();
    
    // Also add to maintenance_logs
    $stmt2 = $pdo->prepare("
        INSERT INTO maintenance_logs (restroom_id, user_id, action, notes)
        VALUES (?, ?, 'submitted_checklist', ?)
    ");
    $stmt2->execute([
        $input['restroom_id'],
        $input['user_id'],
        "Checklist submitted for review (ID: $checklist_id)"
    ]);
    
    // Get restroom name for notification
    $stmt3 = $pdo->prepare("SELECT name FROM restrooms WHERE id = ?");
    $stmt3->execute([$input['restroom_id']]);
    $restroom = $stmt3->fetch();
    
    // Get user full name for notification
    $stmt4 = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt4->execute([$_SESSION['user_id']]);
    $user = $stmt4->fetch();
    
    // Notify all supervisors about new checklist
    $stmt5 = $pdo->prepare("
        SELECT id FROM users WHERE role = 'supervisor' AND status = 'active'
    ");
    $stmt5->execute();
    $supervisors = $stmt5->fetchAll();
    
    $restroom_name = $restroom ? $restroom['name'] : 'Restroom #' . $input['restroom_id'];
    $staff_name = $user ? $user['full_name'] : $_SESSION['full_name'] ?? 'Staff';
    
    foreach ($supervisors as $supervisor) {
        $stmt6 = $pdo->prepare("
            INSERT INTO notifications (user_id, title, message, type, is_read)
            VALUES (?, 'New Checklist Submitted', ?, 'checklist_submitted', 0)
        ");
        $message = "New checklist submitted for {$restroom_name} by {$staff_name}";
        $stmt6->execute([$supervisor['id'], $message]);
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'checklist_id' => $checklist_id,
            'message' => 'Checklist submitted successfully! Waiting for supervisor approval.'
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in submit_checklist.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>