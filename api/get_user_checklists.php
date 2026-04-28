<?php
// ============================================================
//  SmartWash - Get User's Own Checklists (Maintenance)
//  File: api/get_user_checklists.php
//  Method: GET
// ============================================================

require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $_SESSION['user_id'];
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Verify user can only see their own submissions
if ($user_id != $_SESSION['user_id'] && $_SESSION['role'] !== 'supervisor') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $pdo = getDB();
    
    $sql = "
        SELECT 
            c.id,
            c.restroom_id,
            c.status,
            c.supervisor_rating,
            c.review_notes,
            c.submitted_at,
            c.reviewed_at,
            c.notes,
            c.floor_clean,
            c.toilets_clean,
            c.sinks_clean,
            c.mirrors_clean,
            c.soap_refilled,
            c.trash_emptied,
            c.odor_free,
            r.name as restroom_name,
            u.full_name as reviewed_by_name,
            -- Calculate auto rating (0-5 based on completed tasks)
            ROUND(
                (
                    (c.floor_clean + c.toilets_clean + c.sinks_clean + 
                     c.mirrors_clean + c.soap_refilled + c.trash_emptied + c.odor_free) / 7
                ) * 5, 1
            ) as auto_rating
        FROM cleanliness_checklists c
        JOIN restrooms r ON c.restroom_id = r.id
        LEFT JOIN users u ON c.reviewed_by = u.id
        WHERE c.submitted_by = ?
    ";
    
    if ($status !== 'all') {
        $sql .= " AND c.status = ?";
        $stmt = $pdo->prepare($sql . " ORDER BY c.submitted_at DESC");
        $stmt->execute([$user_id, $status]);
    } else {
        $stmt = $pdo->prepare($sql . " ORDER BY c.submitted_at DESC");
        $stmt->execute([$user_id]);
    }
    
    $checklists = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true, 
        'data' => $checklists,
        'count' => count($checklists)
    ]);
    
} catch (PDOException $e) {
    error_log("Error in get_user_checklists.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Database error occurred'
    ]);
}
?>