<?php
// ============================================================
//  SmartWash - Get Notifications for User
//  File: api/get_notifications.php
//  Method: GET
// ============================================================

require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
$unread_only = isset($_GET['unread_only']) && $_GET['unread_only'] === 'true';

try {
    $pdo = getDB();
    
    if ($unread_only) {
        // Get only unread notifications (for badge count)
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as unread_count 
            FROM notifications 
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'unread_count' => $result['unread_count'] ?? 0
            ]
        ]);
    } else {
        // Get all notifications with unread count
        $stmt = $pdo->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$user_id, $limit]);
        $notifications = $stmt->fetchAll();
        
        // Get unread count
        $stmt2 = $pdo->prepare("
            SELECT COUNT(*) as unread_count 
            FROM notifications 
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt2->execute([$user_id]);
        $unread = $stmt2->fetch();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'notifications' => $notifications,
                'unread_count' => $unread['unread_count'] ?? 0
            ]
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Error in get_notifications.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>