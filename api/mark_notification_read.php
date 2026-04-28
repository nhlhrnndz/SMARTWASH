<?php
// ============================================================
//  SmartWash - Mark Notification as Read
//  File: api/mark_notification_read.php
//  Method: POST
// ============================================================

require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['notification_id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing notification_id']);
    exit;
}

$notification_id = (int)$input['notification_id'];
$user_id = $_SESSION['user_id'];

try {
    $pdo = getDB();
    
    // Verify notification belongs to user
    $stmt_check = $pdo->prepare("
        SELECT id FROM notifications WHERE id = ? AND user_id = ?
    ");
    $stmt_check->execute([$notification_id, $user_id]);
    
    if (!$stmt_check->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Notification not found or access denied']);
        exit;
    }
    
    $stmt = $pdo->prepare("
        UPDATE notifications 
        SET is_read = 1 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$notification_id, $user_id]);
    
    echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
    
} catch (PDOException $e) {
    error_log("Error in mark_notification_read.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>