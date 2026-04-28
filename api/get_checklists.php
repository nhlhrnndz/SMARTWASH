<?php
// ============================================================
//  SmartWash - Get Checklists (Supervisor)
//  File: api/get_checklists.php
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

// Only supervisors can view all checklists
if ($_SESSION['role'] !== 'supervisor') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$status = $_GET['status'] ?? 'all';

try {
    $pdo = getDB();
    
    // Build query
    $sql = "
        SELECT 
            c.*,
            r.name as restroom_name,
            u.full_name as staff_name,
            u2.full_name as reviewed_by_name
        FROM cleanliness_checklists c
        JOIN restrooms r ON c.restroom_id = r.id
        JOIN users u ON c.submitted_by = u.id
        LEFT JOIN users u2 ON c.reviewed_by = u2.id
    ";
    
    if ($status !== 'all') {
        $sql .= " WHERE c.status = ?";
        $stmt = $pdo->prepare($sql . " ORDER BY c.submitted_at DESC");
        $stmt->execute([$status]);
    } else {
        $stmt = $pdo->prepare($sql . " ORDER BY c.submitted_at DESC");
        $stmt->execute();
    }
    
    $checklists = $stmt->fetchAll();
    
    // Format ratings
    foreach ($checklists as &$c) {
        $total = 7;
        $completed = 0;
        if ($c['floor_clean']) $completed++;
        if ($c['toilets_clean']) $completed++;
        if ($c['sinks_clean']) $completed++;
        if ($c['soap_refilled']) $completed++;
        if ($c['trash_emptied']) $completed++;
        if ($c['mirrors_clean']) $completed++;
        if ($c['odor_free']) $completed++;
        
        $c['rating'] = round(($completed / $total) * 5, 1);
    }
    
    echo json_encode(['success' => true, 'data' => $checklists]);
    
} catch (PDOException $e) {
    error_log("Database error in get_checklists.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>