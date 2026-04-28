<?php
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $_SESSION['user_id'];
$status = isset($_GET['status']) ? $_GET['status'] : 'pending';

try {
    $pdo = getDB();
    
    $sql = "
        SELECT c.*, r.name as restroom_name
        FROM cleanliness_checklists c
        JOIN restrooms r ON c.restroom_id = r.id
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
    echo json_encode(['success' => true, 'data' => $checklists]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>