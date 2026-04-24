<?php
// ============================================================
//  SmartWash - Live Monitoring Page
//  File: pages/supervisor/live_monitoring.php
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
    <title>Live Monitoring — SmartWash | BatStateU ARASOF-Nasugbu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
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
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--text-dark);
        }

        .dashboard-wrapper { display: flex; min-height: 100vh; }
        .sidebar {
            width: 280px;
            background: var(--white);
            box-shadow: 2px 0 20px rgba(0,0,0,0.05);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }
        .sidebar-header {
            padding: 1.8rem 1.5rem;
            border-bottom: 1px solid var(--gray-border);
            text-align: center;
        }
        .sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.7rem;
            margin-bottom: 0.5rem;
        }
        .sidebar-icon {
            width: 40px;
            height: 40px;
            background: var(--red-deep);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sidebar-icon svg { width: 22px; height: 22px; }
        .sidebar-icon svg path { fill: var(--white); }
        .sidebar-logo span {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 900;
            color: var(--text-dark);
        }
        .sidebar-sub {
            font-size: 0.7rem;
            color: var(--text-mid);
            opacity: 0.7;
        }
        .sidebar-nav { padding: 1.5rem 1rem; }
        .nav-item { margin-bottom: 0.3rem; }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            color: var(--text-mid);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.88rem;
            transition: all 0.2s;
        }
        .nav-link svg { width: 20px; height: 20px; color: #aaa; }
        .nav-link:hover { background: rgba(197,0,0,0.05); color: var(--red-deep); }
        .nav-link.active { background: var(--red-deep); color: var(--white); }
        .nav-link.active svg { color: var(--white); }

        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 1.5rem 2rem;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: var(--white);
            padding: 1rem 1.5rem;
            border-radius: 16px;
        }
        .page-title h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-dark);
        }
        .page-title p {
            font-size: 0.8rem;
            color: var(--text-mid);
            opacity: 0.7;
        }
        .user-menu { display: flex; align-items: center; gap: 1rem; }
        .user-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--red-deep), var(--red-vivid));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 700;
        }
        .user-info { text-align: right; }
        .user-name { font-weight: 700; font-size: 0.88rem; }
        .user-role { font-size: 0.7rem; opacity: 0.6; }

        /* Live Monitoring Specific Styles */
        .restroom-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        .restroom-card {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid var(--gray-border);
            overflow: hidden;
            transition: transform 0.2s;
        }
        .restroom-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px var(--shadow); }
        .restroom-header {
            background: var(--light);
            padding: 1rem 1.2rem;
            border-bottom: 1px solid var(--gray-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .restroom-name {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1rem;
        }
        .status-badge {
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .status-good { background: var(--green-success); color: white; }
        .status-warning { background: var(--yellow-warning); color: var(--text-dark); }
        .status-critical { background: var(--red-deep); color: white; }
        .restroom-body { padding: 1.2rem; }
        .sensor-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.6rem 0;
            border-bottom: 1px solid var(--gray-border);
        }
        .sensor-label { font-size: 0.75rem; font-weight: 600; color: var(--text-mid); }
        .sensor-value { font-size: 0.85rem; font-weight: 700; }
        .sensor-value.low { color: var(--orange-critical); }
        .sensor-value.warning { color: var(--yellow-warning); }
        .progress-bar {
            background: var(--light);
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
            margin-top: 0.3rem;
        }
        .progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s;
        }
        .fill-good { background: var(--green-success); }
        .fill-warning { background: var(--yellow-warning); }
        .fill-critical { background: var(--red-deep); }
        .alert-icon { font-size: 1.2rem; }
        .last-updated {
            font-size: 0.65rem;
            color: var(--text-mid);
            text-align: right;
            margin-top: 1rem;
            opacity: 0.6;
        }
        .demo-badge {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--yellow-warning);
            color: var(--text-dark);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <div class="sidebar-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 2C12 2 6 9 6 14C6 17.3 8.7 20 12 20C15.3 20 18 17.3 18 14C18 9 12 2 12 2Z" fill="#C50000"/>
                        <path d="M9 13.5C9.5 12.5 10.7 12 12 12C13.3 12 14.5 12.5 15 13.5" stroke="#fff" stroke-width="1.4" stroke-linecap="round"/>
                        <circle cx="12" cy="18" r="1" fill="#fff"/>
                    </svg>
                </div>
                <span>SmartWash</span>
            </div>
            <div class="sidebar-sub">BatStateU ARASOF-Nasugbu</div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-item"><a href="dashboard.php" class="nav-link">Dashboard</a></div>
            <div class="nav-item"><a href="live_monitoring.php" class="nav-link active">Live Monitoring</a></div>
            <div class="nav-item"><a href="vape_incidents.php" class="nav-link">Vape Incidents</a></div>
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
                <h1>Live Monitoring</h1>
                <p>Real-time sensor data from all restroom facilities</p>
            </div>
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($currentUser['full_name'] ?? 'Supervisor'); ?></div>
                    <div class="user-role">Supervisor</div>
                </div>
                <div class="user-avatar"><?php echo strtoupper(substr($currentUser['full_name'] ?? 'S', 0, 2)); ?></div>
            </div>
        </div>

        <div class="restroom-grid" id="restroomGrid">
            <!-- Live data will load here -->
            <div class="loading" style="text-align: center; padding: 3rem;">Loading restroom data...</div>
        </div>
        <div class="last-updated" id="lastUpdated">Last updated: Just now</div>
    </main>
</div>

<div class="demo-badge">📡 LIVE MONITORING | UI Preview (Data will auto-refresh when sensors are connected)</div>

<script>
// Simulated restroom data for UI preview
const restrooms = [
    { id: 1, name: 'GLR1 - Ground Left', soap: 85, air: 32, vape: false, smoke: false, last_seen: 'Just now' },
    { id: 2, name: 'GLR2 - Ground Right', soap: 45, air: 28, vape: true, smoke: false, last_seen: 'Just now' },
    { id: 3, name: '1F-FR - 1st Floor Front', soap: 12, air: 55, vape: false, smoke: false, last_seen: '2 min ago' },
    { id: 4, name: '1F-RR - 1st Floor Rear', soap: 92, air: 25, vape: false, smoke: false, last_seen: '1 min ago' },
    { id: 5, name: '2F-MR - 2nd Floor Main', soap: 67, air: 85, vape: false, smoke: false, last_seen: 'Just now' },
    { id: 6, name: '2F-LR - 2nd Floor Left', soap: 78, air: 42, vape: false, smoke: false, last_seen: '3 min ago' },
    { id: 7, name: '3F-CR - 3rd Floor CR', soap: 95, air: 18, vape: false, smoke: true, last_seen: 'Just now' },
    { id: 8, name: '3F-NR - 3rd Floor North', soap: 88, air: 35, vape: false, smoke: false, last_seen: '2 min ago' }
];

function getStatusClass(soap, air, vape, smoke) {
    if (vape || smoke) return 'critical';
    if (soap < 20 || air > 70) return 'critical';
    if (soap < 50 || air > 40) return 'warning';
    return 'good';
}

function getStatusText(soap, air, vape, smoke) {
    if (vape) return '🚭 VAPE DETECTED';
    if (smoke) return '🔥 SMOKE DETECTED';
    if (soap < 20) return '⚠️ SOAP CRITICAL';
    if (air > 70) return '⚠️ AIR CRITICAL';
    if (soap < 50) return '⚠️ Soap Low';
    if (air > 40) return '⚠️ Air Fair';
    return '✅ All Good';
}

function getFillClass(soap, air, vape, smoke) {
    if (vape || smoke) return 'fill-critical';
    if (soap < 20 || air > 70) return 'fill-critical';
    if (soap < 50 || air > 40) return 'fill-warning';
    return 'fill-good';
}

function renderRestrooms() {
    const grid = document.getElementById('restroomGrid');
    grid.innerHTML = restrooms.map(r => {
        const statusClass = getStatusClass(r.soap, r.air, r.vape, r.smoke);
        const statusText = getStatusText(r.soap, r.air, r.vape, r.smoke);
        const fillClass = getFillClass(r.soap, r.air, r.vape, r.smoke);
        
        return `
            <div class="restroom-card">
                <div class="restroom-header">
                    <span class="restroom-name">${r.name}</span>
                    <span class="status-badge status-${statusClass === 'good' ? 'good' : (statusClass === 'warning' ? 'warning' : 'critical')}">${statusText}</span>
                </div>
                <div class="restroom-body">
                    <div class="sensor-row">
                        <span class="sensor-label">🧴 Soap Level</span>
                        <span class="sensor-value ${r.soap < 20 ? 'low' : (r.soap < 50 ? 'warning' : '')}">${r.soap}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill ${fillClass}" style="width: ${r.soap}%"></div>
                    </div>
                    
                    <div class="sensor-row">
                        <span class="sensor-label">🌬️ Air Quality (AQI)</span>
                        <span class="sensor-value ${r.air > 70 ? 'low' : (r.air > 40 ? 'warning' : '')}">${r.air}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill ${r.air > 70 ? 'fill-critical' : (r.air > 40 ? 'fill-warning' : 'fill-good')}" style="width: ${Math.min(r.air, 100)}%"></div>
                    </div>
                    
                    ${r.vape ? `<div class="sensor-row"><span class="sensor-label">🚭 Vape Detection</span><span class="alert-icon">🔴 ACTIVE</span></div>` : ''}
                    ${r.smoke ? `<div class="sensor-row"><span class="sensor-label">🔥 Smoke Detection</span><span class="alert-icon">🔴 ACTIVE</span></div>` : ''}
                </div>
            </div>
        `;
    }).join('');
    
    document.getElementById('lastUpdated').innerHTML = `Last updated: ${new Date().toLocaleTimeString()}`;
}

renderRestrooms();

// Simulate auto-refresh every 10 seconds (for demo)
setInterval(() => {
    // Randomly update some values for demo
    restrooms.forEach(r => {
        r.soap = Math.max(0, Math.min(100, r.soap + (Math.random() - 0.5) * 5));
        r.air = Math.max(0, Math.min(150, r.air + (Math.random() - 0.5) * 3));
        r.vape = Math.random() < 0.05 ? !r.vape : r.vape;
        r.smoke = Math.random() < 0.03 ? !r.smoke : r.smoke;
    });
    renderRestrooms();
}, 10000);
</script>
</body>
</html>