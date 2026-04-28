<?php
// ============================================================
//  SmartWash - Submit Checklist (Maintenance)
//  File: api/submit_checklist.php
//  Method: POST
//  Flow: Inserts checklist + maintenance_log + notifications to supervisors
// ============================================================

require_once '../config/db.php';
session_start();

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
    
    // Calculate rating score (0-5 based on completed tasks)
    $total_items = 7;
    $completed = 0;
    if ($input['floor_clean'] ?? 0) $completed++;
    if ($input['toilets_clean'] ?? 0) $completed++;
    if ($input['sinks_clean'] ?? 0) $completed++;
    if ($input['mirrors_clean'] ?? 0) $completed++;
    if ($input['soap_refilled'] ?? 0) $completed++;
    if ($input['trash_emptied'] ?? 0) $completed++;
    if ($input['odor_free'] ?? 0) $completed++;
    
    $rating_score = round(($completed / $total_items) * 5, 1);
    
    // ============================================
    // 1. INSERT INTO cleanliness_checklists
    // ============================================
    $stmt = $pdo->prepare("
        INSERT INTO cleanliness_checklists (
            restroom_id, submitted_by, floor_clean, toilets_clean, sinks_clean,
            soap_refilled, trash_emptied, mirrors_clean, odor_free, notes, status
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
    
    // Get restroom name
    $stmt_restroom = $pdo->prepare("SELECT name FROM restrooms WHERE id = ?");
    $stmt_restroom->execute([$input['restroom_id']]);
    $restroom = $stmt_restroom->fetch();
    $restroom_name = $restroom ? $restroom['name'] : 'Restroom #' . $input['restroom_id'];
    
    // Get staff name
    $stmt_staff = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt_staff->execute([$input['user_id']]);
    $staff = $stmt_staff->fetch();
    $staff_name = $staff ? $staff['full_name'] : $_SESSION['full_name'] ?? 'Maintenance Staff';
    
    // ============================================
    // 2. INSERT INTO maintenance_logs
    // ============================================
    $stmt_log = $pdo->prepare("
        INSERT INTO maintenance_logs (restroom_id, user_id, action, notes)
        VALUES (?, ?, 'submitted_checklist', ?)
    ");
    $log_notes = "Submitted checklist for {$restroom_name} | Score: {$rating_score}/5";
    $stmt_log->execute([$input['restroom_id'], $input['user_id'], $log_notes]);
    
    // ============================================
    // 3. INSERT INTO notifications (for all supervisors)
    // ============================================
    $stmt_supervisors = $pdo->prepare("
        SELECT id FROM users WHERE role = 'supervisor' AND status = 'active'
    ");
    $stmt_supervisors->execute();
    $supervisors = $stmt_supervisors->fetchAll();
    
    $notification_title = '📋 New Checklist Submitted';
    $notification_message = "{$staff_name} submitted a checklist for {$restroom_name} (Rating: {$rating_score}/5)";
    
    $stmt_notify = $pdo->prepare("
        INSERT INTO notifications (user_id, title, message, type, is_read)
        VALUES (?, ?, ?, 'checklist_submitted', 0)
    ");
    
    foreach ($supervisors as $supervisor) {
        $stmt_notify->execute([$supervisor['id'], $notification_title, $notification_message]);
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'checklist_id' => $checklist_id,
            'rating_score' => $rating_score,
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