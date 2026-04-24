<?php
// ============================================================
//  SmartWash - Settings Page
//  File: pages/supervisor/settings.php
// ============================================================

require_once '../../auth/session.php';
requireRole(['supervisor']);

$currentUser = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings — SmartWash</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #FFFDEF; }
        .dashboard-wrapper { display: flex; }
        .sidebar { width: 280px; background: white; position: fixed; height: 100vh; padding: 1rem; }
        .main-content { flex: 1; margin-left: 280px; padding: 1.5rem; }
        .card { background: white; border-radius: 12px; padding: 1.5rem; border: 1px solid #e0d8c8; margin-bottom: 1rem; }
        .setting-row { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #eee; }
        .nav-link { display: block; padding: 0.75rem; color: #333; text-decoration: none; }
        .nav-link.active { background: #C50000; color: white; border-radius: 8px; }
        button { background: #C50000; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar"><div style="padding: 1rem;"><h3>SmartWash</h3></div>
        <nav><a href="dashboard.php" class="nav-link">Dashboard</a><a href="live_monitoring.php" class="nav-link">Live Monitoring</a><a href="vape_incidents.php" class="nav-link">Vape Incidents</a><a href="air_quality.php" class="nav-link">Air Quality</a><a href="checklists.php" class="nav-link">Checklists</a><a href="maintenance_log.php" class="nav-link">Maintenance Log</a><a href="alerts.php" class="nav-link">Alerts</a><a href="staff_management.php" class="nav-link">Staff Management</a><a href="settings.php" class="nav-link active">Settings</a></nav>
    </aside>
    <main class="main-content">
        <h1>System Settings</h1>
        <p>Configure thresholds and system preferences</p>
        
        <div class="card">
            <h3>Alert Thresholds</h3>
            <div class="setting-row"><span>Soap Low Alert (%)</span><span><input type="number" value="20" style="width: 80px;"></span></div>
            <div class="setting-row"><span>Air Quality Warning (AQI)</span><span><input type="number" value="50" style="width: 80px;"></span></div>
            <div class="setting-row"><span>Air Quality Critical (AQI)</span><span><input type="number" value="100" style="width: 80px;"></span></div>
            <div class="setting-row"><span>Auto Air Freshener</span><span><label><input type="checkbox" checked> Enable</label></span></div>
        </div>
        
        <div class="card">
            <h3>Account Settings</h3>
            <div class="setting-row"><span>Change Password</span><button>Change</button></div>
            <div class="setting-row"><span>Email Notifications</span><span><label><input type="checkbox" checked> Receive alerts via email</label></span></div>
        </div>
        
        <div class="card">
            <h3>System Information</h3>
            <div class="setting-row"><span>Version</span><span>SmartWash v1.0</span></div>
            <div class="setting-row"><span>Last Sensor Update</span><span>Just now</span></div>
        </div>
    </main>
</div>
</body>
</html>