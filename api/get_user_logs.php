<?php
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $_SESSION['user_id'];
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;

try {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("
        SELECT ml.*, r.name as restroom_name
        FROM maintenance_logs ml
        JOIN restrooms r ON ml.restroom_id = r.id
        WHERE ml.user_id = ?
        ORDER BY ml.performed_at DESC
        LIMIT ?
    ");
    $stmt->execute([$user_id, $limit]);
    $logs = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $logs]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>