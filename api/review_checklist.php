<?php
// ============================================================
//  SmartWash - Review Checklist (Supervisor)
//  File: api/review_checklist.php
//  Method: POST
//  Flow: Updates checklist + maintenance_log + notification to maintenance
// ============================================================

require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

// Only supervisors can review checklists
if ($_SESSION['role'] !== 'supervisor') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized. Only supervisors can review checklists.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['checklist_id']) || !isset($input['action'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$checklist_id = (int)$input['checklist_id'];
$action = $input['action'];
$rating = isset($input['rating']) ? (int)$input['rating'] : null;
$review_notes = $input['review_notes'] ?? null;

if ($rating !== null && ($rating < 1 || $rating > 5)) {
    echo json_encode(['success' => false, 'error' => 'Rating must be between 1 and 5']);
    exit;
}

try {
    $pdo = getDB();
    
    // ============================================
    // Get checklist details first
    // ============================================
    $stmt = $pdo->prepare("
        SELECT c.*, r.name as restroom_name, u.full_name as staff_name, u.id as staff_id
        FROM cleanliness_checklists c
        JOIN restrooms r ON c.restroom_id = r.id
        JOIN users u ON c.submitted_by = u.id
        WHERE c.id = ?
    ");
    $stmt->execute([$checklist_id]);
    $checklist = $stmt->fetch();
    
    if (!$checklist) {
        echo json_encode(['success' => false, 'error' => 'Checklist not found']);
        exit;
    }
    
    if ($checklist['status'] !== 'pending') {
        echo json_encode(['success' => false, 'error' => 'This checklist has already been reviewed']);
        exit;
    }
    
    // Calculate auto rating from checklist items
    $total_items = 7;
    $completed = 0;
    if ($checklist['floor_clean']) $completed++;
    if ($checklist['toilets_clean']) $completed++;
    if ($checklist['sinks_clean']) $completed++;
    if ($checklist['mirrors_clean']) $completed++;
    if ($checklist['soap_refilled']) $completed++;
    if ($checklist['trash_emptied']) $completed++;
    if ($checklist['odor_free']) $completed++;
    $auto_rating = round(($completed / $total_items) * 5, 1);
    
    // ============================================
    // 1. UPDATE cleanliness_checklists
    // ============================================
    $new_status = ($action === 'approve') ? 'approved' : 'flagged';
    $stmt2 = $pdo->prepare("
        UPDATE cleanliness_checklists 
        SET status = ?, 
            reviewed_by = ?, 
            reviewed_at = NOW(), 
            review_notes = ?,
            supervisor_rating = ?
        WHERE id = ?
    ");
    $stmt2->execute([$new_status, $_SESSION['user_id'], $review_notes, $rating, $checklist_id]);
    
    // Get supervisor name
    $stmt_supervisor = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt_supervisor->execute([$_SESSION['user_id']]);
    $supervisor = $stmt_supervisor->fetch();
    $supervisor_name = $supervisor ? $supervisor['full_name'] : 'Supervisor';
    
    // ============================================
    // 2. INSERT INTO maintenance_logs
    // ============================================
    $action_log = ($action === 'approve') ? 'checklist_approved' : 'checklist_flagged';
    
    if ($action === 'approve') {
        $log_notes = "Approved checklist for {$checklist['restroom_name']} | Auto Rating: {$auto_rating}/5 | Supervisor Rating: {$rating}/5";
        if ($review_notes) {
            $log_notes .= " | Notes: {$review_notes}";
        }
    } else {
        $log_notes = "Flagged checklist for {$checklist['restroom_name']} for review | Reason: " . ($review_notes ?? 'No reason provided');
    }
    
    $stmt_log = $pdo->prepare("
        INSERT INTO maintenance_logs (restroom_id, user_id, action, notes)
        VALUES (?, ?, ?, ?)
    ");
    $stmt_log->execute([$checklist['restroom_id'], $_SESSION['user_id'], $action_log, $log_notes]);
    
    // ============================================
    // 3. INSERT INTO notifications (for maintenance staff)
    // ============================================
    $star_display = $rating ? str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) : '';
    
    if ($action === 'approve') {
        $notification_title = '✅ Checklist Approved';
        $notification_message = "Good news! Your checklist for {$checklist['restroom_name']} has been APPROVED by {$supervisor_name}.\n";
        $notification_message .= "📊 Auto Rating: {$auto_rating}/5 | 👑 Supervisor Rating: {$rating}/5 {$star_display}";
        if ($review_notes) {
            $notification_message .= "\n📝 Feedback: {$review_notes}";
        }
        $notification_message .= "\n\nGreat work! Keep it up! 👍";
    } else {
        $notification_title = '⚠️ Checklist Flagged';
        $notification_message = "Action Required: Your checklist for {$checklist['restroom_name']} has been FLAGGED by {$supervisor_name}.\n";
        $notification_message .= "📝 Reason: " . ($review_notes ?? 'Please re-inspect and complete all items properly.');
        $notification_message .= "\n\nPlease review and re-submit the checklist.";
    }
    
    $stmt_notify = $pdo->prepare("
        INSERT INTO notifications (user_id, title, message, type, is_read)
        VALUES (?, ?, ?, 'checklist_reviewed', 0)
    ");
    $stmt_notify->execute([$checklist['staff_id'], $notification_title, $notification_message]);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'message' => "Checklist {$new_status} successfully",
            'action' => $action,
            'rating' => $rating,
            'auto_rating' => $auto_rating,
            'staff_notified' => true
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in review_checklist.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>