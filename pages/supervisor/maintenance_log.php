<?php
// ============================================================
//  SmartWash - Maintenance Log Page
//  File: pages/supervisor/maintenance_log.php
// ============================================================

require_once '../../auth/session.php';
requireRole(['supervisor']);

$currentUser = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Log — SmartWash</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #FFFDEF; }
        .dashboard-wrapper { display: flex; }
        .sidebar { width: 280px; background: white; position: fixed; height: 100vh; padding: 1rem; }
        .main-content { flex: 1; margin-left: 280px; padding: 1.5rem; }
        .card { background: white; border-radius: 12px; padding: 1rem; border: 1px solid #e0d8c8; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f5f5f5; }
        .nav-link { display: block; padding: 0.75rem; color: #333; text-decoration: none; }
        .nav-link.active { background: #C50000; color: white; border-radius: 8px; }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar"><div style="padding: 1rem;"><h3>SmartWash</h3></div>
        <nav><a href="dashboard.php" class="nav-link">Dashboard</a><a href="live_monitoring.php" class="nav-link">Live Monitoring</a><a href="vape_incidents.php" class="nav-link">Vape Incidents</a><a href="air_quality.php" class="nav-link">Air Quality</a><a href="checklists.php" class="nav-link">Checklists</a><a href="maintenance_log.php" class="nav-link active">Maintenance Log</a><a href="alerts.php" class="nav-link">Alerts</a><a href="staff_management.php" class="nav-link">Staff Management</a><a href="settings.php" class="nav-link">Settings</a></nav>
    </aside>
    <main class="main-content">
        <h1>Maintenance Log</h1>
        <p>Complete history of all maintenance actions</p>
        
        <div class="card">
            <table>
                <thead><tr><th>Date & Time</th><th>Restroom</th><th>Action</th><th>Performed By</th></tr></thead>
                <tbody>
                    <tr><td>2024-01-15 09:15</td><td>1F-FR - 1st Floor Front</td><td>Soap refilled</td><td>Rey M. Santos</td></tr>
                    <tr><td>2024-01-15 08:45</td><td>2F-MR - 2nd Floor Main</td><td>Air freshener triggered</td><td>System Auto</td></tr>
                    <tr><td>2024-01-15 08:00</td><td>3F-CR - 3rd Floor CR</td><td>Cleaned and sanitized</td><td>Maria L. Cruz</td></tr>
                    <tr><td>2024-01-14 16:30</td><td>GLR2 - Ground Right</td><td>Vape incident reported</td><td>Sensor</td></tr>
                    <tr><td>2024-01-14 14:20</td><td>GLR1 - Ground Left</td><td>Soap refilled</td><td>Rey M. Santos</td></tr>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>