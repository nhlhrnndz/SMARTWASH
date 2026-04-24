<?php
// ============================================================
//  SmartWash — Logout
//  File: auth/logout.php
// ============================================================
session_start();
session_unset();
session_destroy();
 
// Redirect back to login
header('Location: ../index.php?msg=logged_out');
exit;
 