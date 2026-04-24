<?php
// ============================================================
//  SmartWash - Air Quality Page
//  File: pages/supervisor/air_quality.php
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
    <title>Air Quality — SmartWash | BatStateU ARASOF-Nasugbu</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Same base styles as vape_incidents.php */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --red-deep: #C50000;
            --cream: #FFFDEF;
            --white: #ffffff;
            --text-dark: #1a0000;
            --gray-border: #e0d8c8;
            --green-success: #2e7d32;
            --yellow-warning: #f9a825;
        }
        body { font-family: 'DM Sans', sans-serif; background: var(--cream); }
        .dashboard-wrapper { display: flex; }
        .sidebar { width: 280px; background: var(--white); position: fixed; height: 100vh; }
        .sidebar-header { padding: 1.8rem 1.5rem; text-align: center; border-bottom: 1px solid var(--gray-border); }
        .sidebar-logo { display: flex; align-items: center; justify-content: center; gap: 0.7rem; }
        .sidebar-icon { width: 40px; height: 40px; background: var(--red-deep); border-radius: 10px; }
        .sidebar-nav { padding: 1.5rem 1rem; }
        .nav-link { display: flex; align-items: center; gap: 0.8rem; padding: 0.75rem 1rem; border-radius: 10px; color: var(--text-dark); text-decoration: none; }
        .nav-link.active { background: var(--red-deep); color: white; }
        .main-content { flex: 1; margin-left: 280px; padding: 1.5rem 2rem; }
        .top-bar { background: var(--white); padding: 1rem 1.5rem; border-radius: 16px; margin-bottom: 2rem; display: flex; justify-content: space-between; }
        .page-title h1 { font-family: 'Playfair Display', serif; font-size: 1.8rem; }
        .user-avatar { width: 44px; height: 44px; background: var(--red-deep); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; }
        .card { background: var(--white); border-radius: 12px; border: 1px solid var(--gray-border); padding: 1.2rem; margin-bottom: 1.5rem; }
        .card h3 { margin-bottom: 1rem; }
        .aqi-scale { display: flex; margin-top: 1rem; border-radius: 8px; overflow: hidden; }
        .aqi-good { background: var(--green-success); color: white; padding: 0.5rem; text-align: center; flex: 1; font-size: 0.7rem; }
        .aqi-moderate { background: var(--yellow-warning); padding: 0.5rem; text-align: center; flex: 1; font-size: 0.7rem; }
        .aqi-unhealthy { background: #f57c00; color: white; padding: 0.5rem; text-align: center; flex: 1; font-size: 0.7rem; }
        .aqi-hazardous { background: var(--red-deep); color: white; padding: 0.5rem; text-align: center; flex: 1; font-size: 0.7rem; }
        .demo-badge { position: fixed; bottom: 20px; right: 20px; background: var(--yellow-warning); padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.7rem; }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header"><div class="sidebar-logo"><span>SmartWash</span></div></div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-link">Dashboard</a>
            <a href="live_monitoring.php" class="nav-link">Live Monitoring</a>
            <a href="vape_incidents.php" class="nav-link">Vape Incidents</a>
            <a href="air_quality.php" class="nav-link active">Air Quality</a>
            <a href="checklists.php" class="nav-link">Checklists</a>
            <a href="maintenance_log.php" class="nav-link">Maintenance Log</a>
            <a href="alerts.php" class="nav-link">Alerts</a>
            <a href="staff_management.php" class="nav-link">Staff Management</a>
            <a href="settings.php" class="nav-link">Settings</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="page-title"><h1>Air Quality Reports</h1><p>Historical AQI data and trends</p></div>
            <div class="user-menu"><div class="user-avatar"><?php echo strtoupper(substr($currentUser['full_name'] ?? 'S', 0, 2)); ?></div></div>
        </div>

        <div class="card">
            <h3>🌬️ 24-Hour Air Quality Trend</h3>
            <canvas id="aqiChart" style="height: 300px;"></canvas>
            <div class="aqi-scale"><div class="aqi-good">✅ Good (0-50)</div><div class="aqi-moderate">🟡 Moderate (51-100)</div><div class="aqi-unhealthy">🟠 Unhealthy (101-150)</div><div class="aqi-hazardous">🔴 Hazardous (150+)</div></div>
        </div>

        <div class="card"><h3>📊 Restroom Air Quality Summary</h3><div id="restroomAQI"></div></div>
    </main>
</div>

<script>
const ctx = document.getElementById('aqiChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: { labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'], datasets: [{ label: 'AQI', data: [28, 25, 45, 68, 72, 42], borderColor: '#C50000', fill: true, backgroundColor: 'rgba(197,0,0,0.1)' }] },
    options: { responsive: true, maintainAspectRatio: true }
});

const restrooms = ['GLR1: 32 (Good)', 'GLR2: 28 (Good)', '1F-FR: 55 (Moderate)', '2F-MR: 85 (Unhealthy)', '3F-CR: 18 (Good)'];
document.getElementById('restroomAQI').innerHTML = restrooms.map(r => `<div style="padding: 0.5rem; border-bottom: 1px solid #eee;">${r}</div>`).join('');
</script>
<div class="demo-badge">📈 AIR QUALITY | UI Preview</div>
</body>
</html>