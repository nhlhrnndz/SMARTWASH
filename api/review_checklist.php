<?php
// ============================================================
//  SmartWash - Review Checklist (Supervisor)
//  File: api/review_checklist.php
//  Method: POST
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
    
    // Get checklist details
    $stmt = $pdo->prepare("
        SELECT c.*, r.name as restroom_name, u.full_name as staff_name
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
    
    // Update checklist
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
    
    // Create notification for maintenance staff
    $title = ($action === 'approve') ? 'Checklist Approved ✅' : 'Checklist Flagged ⚠️';
    
    if ($action === 'approve') {
        $star_display = $rating ? str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) : '';
        $message = "Your checklist for {$checklist['restroom_name']} has been approved!\n";
        if ($rating) {
            $message .= "Supervisor Rating: {$rating}/5 {$star_display}\n";
        }
        $message .= "Great work!";
    } else {
        $message = "Your checklist for {$checklist['restroom_name']} has been flagged for review.\n";
        $message .= "Feedback: " . ($review_notes ?? 'Please re-inspect and complete all items properly.');
    }
    
    $stmt3 = $pdo->prepare("
        INSERT INTO notifications (user_id, title, message, type, is_read)
        VALUES (?, ?, ?, 'checklist_reviewed', 0)
    ");
    $stmt3->execute([$checklist['submitted_by'], $title, $message]);
    
    // Add to maintenance logs
    $stmt4 = $pdo->prepare("
        INSERT INTO maintenance_logs (restroom_id, user_id, action, notes)
        VALUES (?, ?, ?, ?)
    ");
    $action_log = ($action === 'approve') ? 'checklist_approved' : 'checklist_flagged';
    $log_notes = $review_notes ?: ($rating ? "Rating: {$rating}/5" : null);
    $stmt4->execute([$checklist['restroom_id'], $_SESSION['user_id'], $action_log, $log_notes]);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'message' => "Checklist {$new_status} successfully",
            'rating' => $rating,
            'staff_notified' => true
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in review_checklist.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>