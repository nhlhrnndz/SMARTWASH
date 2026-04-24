<?php
// ============================================================
//  SmartWash - Vape Incidents Page
//  File: pages/supervisor/vape_incidents.php
// ============================================================

require_once '../../auth/session.php';
requireRole(['supervisor']);

$currentUser = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vape Incidents — SmartWash | BatStateU ARASOF-Nasugbu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Copy the same base styles from dashboard.php */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --red-deep: #C50000;
            --red-vivid: #E70000;
            --cream: #FFFDEF;
            --light: #F1F1F1;
            --white: #ffffff;
            --text-dark: #1a0000;
            --text-mid: #4a1010;
            --shadow: rgba(197,0,0,0.18);
            --green-success: #2e7d32;
            --yellow-warning: #f9a825;
            --orange-critical: #f57c00;
            --gray-border: #e0d8c8;
        }
        body { font-family: 'DM Sans', sans-serif; background: var(--cream); }
        .dashboard-wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 280px; background: var(--white); position: fixed; height: 100vh; }
        .sidebar-header { padding: 1.8rem 1.5rem; border-bottom: 1px solid var(--gray-border); text-align: center; }
        .sidebar-logo { display: flex; align-items: center; justify-content: center; gap: 0.7rem; }
        .sidebar-icon { width: 40px; height: 40px; background: var(--red-deep); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .sidebar-icon svg { width: 22px; height: 22px; fill: white; }
        .sidebar-logo span { font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 900; }
        .sidebar-nav { padding: 1.5rem 1rem; }
        .nav-item { margin-bottom: 0.3rem; }
        .nav-link { display: flex; align-items: center; gap: 0.8rem; padding: 0.75rem 1rem; border-radius: 10px; color: var(--text-mid); text-decoration: none; font-weight: 500; }
        .nav-link.active { background: var(--red-deep); color: white; }
        .main-content { flex: 1; margin-left: 280px; padding: 1.5rem 2rem; }
        .top-bar { background: var(--white); padding: 1rem 1.5rem; border-radius: 16px; margin-bottom: 2rem; display: flex; justify-content: space-between; }
        .page-title h1 { font-family: 'Playfair Display', serif; font-size: 1.8rem; }
        .user-menu { display: flex; align-items: center; gap: 1rem; }
        .user-avatar { width: 44px; height: 44px; background: linear-gradient(135deg, var(--red-deep), var(--red-vivid)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; }
        
        /* Page Specific */
        .stats-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: var(--white); border-radius: 12px; padding: 1rem; border: 1px solid var(--gray-border); }
        .stat-value { font-size: 2rem; font-weight: 900; color: var(--red-deep); }
        .incident-table { background: var(--white); border-radius: 12px; border: 1px solid var(--gray-border); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--gray-border); }
        th { background: var(--light); }
        .badge-vape { background: rgba(197,0,0,0.15); color: var(--red-deep); padding: 0.25rem 0.6rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
        .badge-smoke { background: rgba(245,124,0,0.15); color: var(--orange-critical); padding: 0.25rem 0.6rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
        .demo-badge { position: fixed; bottom: 20px; right: 20px; background: var(--yellow-warning); padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.7rem; cursor: pointer; }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <div class="sidebar-icon"><svg viewBox="0 0 24 24"><path d="M12 2C12 2 6 9 6 14C6 17.3 8.7 20 12 20C15.3 20 18 17.3 18 14C18 9 12 2 12 2Z" fill="white"/></svg></div>
                <span>SmartWash</span>
            </div>
            <div class="sidebar-sub">BatStateU ARASOF-Nasugbu</div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-item"><a href="dashboard.php" class="nav-link">Dashboard</a></div>
            <div class="nav-item"><a href="live_monitoring.php" class="nav-link">Live Monitoring</a></div>
            <div class="nav-item"><a href="vape_incidents.php" class="nav-link active">Vape Incidents</a></div>
            <div class="nav-item"><a href="air_quality.php" class="nav-link">Air Quality</a></div>
            <div class="nav-item"><a href="checklists.php" class="nav-link">Checklists</a></div>
            <div class="nav-item"><a href="maintenance_log.php" class="nav-link">Maintenance Log</a></div>
            <div class="nav-item"><a href="alerts.php" class="nav-link">Alerts</a></div>
            <div class="nav-item"><a href="staff_management.php" class="nav-link">Staff Management</a></div>
            <div class="nav-item"><a href="settings.php" class="nav-link">Settings</a></div>
        </nav>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h1>Vape & Smoke Incidents</h1>
                <p>Track and monitor all detected incidents</p>
            </div>
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($currentUser['full_name'] ?? 'Supervisor'); ?></div>
                    <div class="user-role">Supervisor</div>
                </div>
                <div class="user-avatar"><?php echo strtoupper(substr($currentUser['full_name'] ?? 'S', 0, 2)); ?></div>
            </div>
        </div>

        <div class="stats-cards">
            <div class="stat-card"><div class="stat-value">12</div><div class="stat-label">Total Incidents</div></div>
            <div class="stat-card"><div class="stat-value">3</div><div class="stat-label">This Week</div></div>
            <div class="stat-card"><div class="stat-value">2</div><div class="stat-label">Active / Unresolved</div></div>
        </div>

        <div class="incident-table">
            <table>
                <thead><tr><th>Date & Time</th><th>Restroom</th><th>Type</th><th>Severity</th><th>Status</th></tr></thead>
                <tbody>
                    <tr><td>2024-01-15 10:15:00</td><td>GLR2 - Ground Right</td><td><span class="badge-vape">🚭 Vape</span></td><td>High</td><td>⚠️ Active</td></tr>
                    <tr><td>2024-01-15 09:45:00</td><td>3F-CR - 3rd Floor CR</td><td><span class="badge-smoke">🔥 Smoke</span></td><td>Critical</td><td>⚠️ Active</td></tr>
                    <tr><td>2024-01-14 16:20:00</td><td>2F-MR - 2nd Floor Main</td><td><span class="badge-vape">🚭 Vape</span></td><td>Medium</td><td>✅ Resolved</td></tr>
                    <tr><td>2024-01-14 14:30:00</td><td>GLR1 - Ground Left</td><td><span class="badge-vape">🚭 Vape</span></td><td>Low</td><td>✅ Resolved</td></tr>
                    <tr><td>2024-01-14 11:15:00</td><td>1F-RR - 1st Floor Rear</td><td><span class="badge-smoke">🔥 Smoke</span></td><td>High</td><td>✅ Resolved</td></tr>
                </tbody>
            </table>
        </div>
    </main>
</div>
<div class="demo-badge">📊 INCIDENT LOG | UI Preview</div>
</body>
</html>