<?php
// ============================================================
//  SmartWash - Get Checklist Details
//  File: api/get_checklist_details.php
//  Method: GET
// ============================================================

require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$checklist_id = (int)$_GET['id'];

try {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            r.name as restroom_name,
            u.full_name as staff_name,
            u2.full_name as reviewed_by_name
        FROM cleanliness_checklists c
        JOIN restrooms r ON c.restroom_id = r.id
        JOIN users u ON c.submitted_by = u.id
        LEFT JOIN users u2 ON c.reviewed_by = u2.id
        WHERE c.id = ?
    ");
    $stmt->execute([$checklist_id]);
    $checklist = $stmt->fetch();
    
    if (!$checklist) {
        echo json_encode(['success' => false, 'error' => 'Checklist not found']);
        exit;
    }
    
    // Calculate rating
    $total = 7;
    $completed = 0;
    if ($checklist['floor_clean']) $completed++;
    if ($checklist['toilets_clean']) $completed++;
    if ($checklist['sinks_clean']) $completed++;
    if ($checklist['soap_refilled']) $completed++;
    if ($checklist['trash_emptied']) $completed++;
    if ($checklist['mirrors_clean']) $completed++;
    if ($checklist['odor_free']) $completed++;
    
    $checklist['rating'] = round(($completed / $total) * 5, 1);
    
    echo json_encode(['success' => true, 'data' => $checklist]);
    
} catch (PDOException $e) {
    error_log("Database error in get_checklist_details.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>