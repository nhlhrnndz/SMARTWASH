<?php
// ============================================================
//  SmartWash — Session Guard
//  Include this at the TOP of every protected page
//  Usage:
//    require_once '../../auth/session.php';           (from pages/supervisor/)
//    require_once '../../auth/session.php';           (from pages/maintenance/)
//    requireRole(['supervisor','admin']);              (supervisor pages)
//    requireRole(['staff']);                           (maintenance pages)
// ============================================================
 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}
 
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . getLoginPath() . '?error=session');
        exit;
    }
}
 
function requireRole(array $roles): void {
    requireLogin();
    if (!in_array($_SESSION['role'] ?? '', $roles)) {
        $role = $_SESSION['role'] ?? '';
        if ($role === 'supervisor') {
            header('Location: ' . getRootPath() . 'pages/supervisor/dashboard.php');
        } else {
            header('Location: ' . getRootPath() . 'pages/maintenance/home.php');
        }
        exit;
    }
}
 
function getLoginPath(): string {
    // Works whether called from root or from pages/supervisor/ or pages/maintenance/
    $depth = substr_count(str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']),
                          str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\')));
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);
    $root   = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $rel    = ltrim(str_replace($root, '', $script), '/');
    $parts  = explode('/', $rel);
    $depth  = count($parts) - 1; // depth from root
    return str_repeat('../', $depth) . 'index.php';
}
 
function getRootPath(): string {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);
    $root   = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $rel    = ltrim(str_replace($root, '', $script), '/');
    $parts  = explode('/', $rel);
    $depth  = count($parts) - 1;
    return str_repeat('../', $depth);
}
 
function currentUser(): array {
    return [
        'id'        => $_SESSION['user_id']   ?? null,
        'username'  => $_SESSION['username']  ?? '',
        'full_name' => $_SESSION['full_name'] ?? '',
        'role'      => $_SESSION['role']      ?? '',
    ];
}