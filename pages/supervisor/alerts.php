<?php
// ============================================================
//  SmartWash - Alerts Page
//  File: pages/supervisor/alerts.php
// ============================================================

require_once '../../auth/session.php';
requireRole(['supervisor']);

$currentUser = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alerts — SmartWash</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #FFFDEF; }
        .dashboard-wrapper { display: flex; }
        .sidebar { width: 280px; background: white; position: fixed; height: 100vh; padding: 1rem; }
        .main-content { flex: 1; margin-left: 280px; padding: 1.5rem; }
        .card { background: white; border-radius: 12px; padding: 1rem; border: 1px solid #e0d8c8; }
        .alert-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid #eee; }
        .critical { color: #C50000; font-weight: bold; }
        .warning { color: #f57c00; }
        .btn-resolve { background: #2e7d32; color: white; border: none; padding: 0.3rem 0.8rem; border-radius: 6px; cursor: pointer; }
        .nav-link { display: block; padding: 0.75rem; color: #333; text-decoration: none; }
        .nav-link.active { background: #C50000; color: white; border-radius: 8px; }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar"><div style="padding: 1rem;"><h3>SmartWash</h3></div>
        <nav><a href="dashboard.php" class="nav-link">Dashboard</a><a href="live_monitoring.php" class="nav-link">Live Monitoring</a><a href="vape_incidents.php" class="nav-link">Vape Incidents</a><a href="air_quality.php" class="nav-link">Air Quality</a><a href="checklists.php" class="nav-link">Checklists</a><a href="maintenance_log.php" class="nav-link">Maintenance Log</a><a href="alerts.php" class="nav-link active">Alerts</a><a href="staff_management.php" class="nav-link">Staff Management</a><a href="settings.php" class="nav-link">Settings</a></nav>
    </aside>
    <main class="main-content">
        <h1>System Alerts</h1>
        <p>Real-time alerts requiring attention</p>
        
        <div class="card">
            <div class="alert-item"><div><span class="critical">🔴 CRITICAL</span><br><strong>Vape Detected</strong><br><small>GLR2 - Ground Right • 10:15 AM</small></div><button class="btn-resolve">Mark Resolved</button></div>
            <div class="alert-item"><div><span class="critical">🔴 CRITICAL</span><br><strong>Smoke Detected</strong><br><small>3F-CR - 3rd Floor CR • 09:45 AM</small></div><button class="btn-resolve">Mark Resolved</button></div>
            <div class="alert-item"><div><span class="warning">🟡 WARNING</span><br><strong>Soap Level Low</strong><br><small>1F-FR - 1st Floor Front • 09:30 AM</small></div><button class="btn-resolve">Mark Resolved</button></div>
            <div class="alert-item"><div><span class="warning">🟡 WARNING</span><br><strong>Soap Level Low</strong><br><small>GLR1 - Ground Left • 08:20 AM</small></div><button class="btn-resolve">Mark Resolved</button></div>
        </div>
    </main>
</div>
</body>
</html>