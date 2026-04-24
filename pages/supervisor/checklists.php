<?php
// ============================================================
//  SmartWash - Checklists Page
//  File: pages/supervisor/checklists.php
// ============================================================

require_once '../../auth/session.php';
requireRole(['supervisor']);

$currentUser = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checklists — SmartWash</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans&display=swap" rel="stylesheet">
    <style>
        /* Quick styling - reuse from previous pages */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #FFFDEF; }
        .dashboard-wrapper { display: flex; }
        .sidebar { width: 280px; background: white; position: fixed; height: 100vh; padding: 1rem; }
        .main-content { flex: 1; margin-left: 280px; padding: 1.5rem; }
        .card { background: white; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; border: 1px solid #e0d8c8; }
        .checklist-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid #eee; }
        .btn-approve { background: #2e7d32; color: white; border: none; padding: 0.3rem 0.8rem; border-radius: 6px; cursor: pointer; }
        .btn-flag { background: #f57c00; color: white; border: none; padding: 0.3rem 0.8rem; border-radius: 6px; cursor: pointer; }
        .nav-link { display: block; padding: 0.75rem; color: #333; text-decoration: none; }
        .nav-link.active { background: #C50000; color: white; border-radius: 8px; }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar"><div style="padding: 1rem;"><h3>SmartWash</h3></div>
        <nav><a href="dashboard.php" class="nav-link">Dashboard</a><a href="live_monitoring.php" class="nav-link">Live Monitoring</a><a href="vape_incidents.php" class="nav-link">Vape Incidents</a><a href="air_quality.php" class="nav-link">Air Quality</a><a href="checklists.php" class="nav-link active">Checklists</a><a href="maintenance_log.php" class="nav-link">Maintenance Log</a><a href="alerts.php" class="nav-link">Alerts</a><a href="staff_management.php" class="nav-link">Staff Management</a><a href="settings.php" class="nav-link">Settings</a></nav>
    </aside>
    <main class="main-content">
        <h1>Cleanliness Checklists</h1>
        <p>Review and approve submissions from maintenance staff</p>
        
        <div class="card">
            <div class="checklist-item"><div><strong>GLR1 - Ground Left</strong><br><small>by Rey M. Santos • Jan 15, 2024</small></div><div><span>⭐ 5/5</span><button class="btn-approve" style="margin-left: 1rem;">✓ Approve</button><button class="btn-flag">⚠️ Flag</button></div></div>
            <div class="checklist-item"><div><strong>GLR2 - Ground Right</strong><br><small>by Maria L. Cruz • Jan 15, 2024</small></div><div><span>⭐ 3/5</span><button class="btn-approve">✓ Approve</button><button class="btn-flag">⚠️ Flag</button></div></div>
            <div class="checklist-item"><div><strong>1F-FR - 1st Floor Front</strong><br><small>by Rey M. Santos • Jan 15, 2024</small></div><div><span>⭐ 4/5</span><button class="btn-approve">✓ Approve</button><button class="btn-flag">⚠️ Flag</button></div></div>
        </div>
    </main>
</div>
</body>
</html>