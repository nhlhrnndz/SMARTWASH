<?php
// ============================================================
//  SmartWash - Live Monitoring Page
//  File: pages/supervisor/live_monitoring.php
// ============================================================

require_once '../../auth/session.php';
requireRole(['supervisor']);

$currentUser = currentUser();
$fullName = $currentUser['full_name'] ?? 'Supervisor';
$initials = '';
$nameParts = explode(' ', $fullName);
foreach ($nameParts as $part) {
    if (strlen($part) > 0 && strlen($initials) < 2) {
        $initials .= strtoupper($part[0]);
    }
}
if (strlen($initials) < 2 && strlen($fullName) > 0) {
    $initials = strtoupper(substr($fullName, 0, 2));
}
$current_page = basename($_SERVER['PHP_SELF']);
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
            --blue-info: #1565C0;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        .dashboard-wrapper { display: flex; min-height: 100vh; }
        
        /* Sidebar */
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
        .nav-link svg { width: 20px; height: 20px; color: #aaa; transition: all 0.2s; }
        .nav-link:hover { background: rgba(197,0,0,0.05); color: var(--red-deep); }
        .nav-link:hover svg { color: var(--red-deep); }
        .nav-link.active { background: var(--red-deep); color: var(--white); }
        .nav-link.active svg { color: var(--white); }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 1.5rem 2rem;
        }
        
        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: var(--white);
            padding: 1rem 1.5rem;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
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
        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .logout-btn {
            background: none;
            border: 1px solid var(--gray-border);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-mid);
            text-decoration: none;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .logout-btn:hover {
            background: var(--red-deep);
            border-color: var(--red-deep);
            color: var(--white);
        }
        .logout-btn:hover svg {
            stroke: var(--white);
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
            font-size: 1.1rem;
        }
        .user-info { text-align: right; }
        .user-name { font-weight: 700; font-size: 0.88rem; }
        .user-role { font-size: 0.7rem; opacity: 0.6; }

        /* Refresh Controls */
        .refresh-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .auto-refresh-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
        }
        .auto-refresh-toggle input {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }
        .refresh-btn {
            background: var(--red-deep);
            color: var(--white);
            border: none;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .refresh-btn:hover {
            background: var(--red-vivid);
            transform: scale(1.02);
        }
        .last-updated {
            font-size: 0.7rem;
            color: var(--text-mid);
            opacity: 0.6;
            text-align: right;
            margin-top: 1rem;
        }

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
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .restroom-card:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 8px 24px var(--shadow);
        }
        .restroom-header {
            background: var(--light);
            padding: 1rem 1.2rem;
            border-bottom: 1px solid var(--gray-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .restroom-name {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1rem;
        }
        .restroom-location {
            font-size: 0.7rem;
            color: var(--text-mid);
            opacity: 0.7;
            margin-top: 0.2rem;
        }
        .status-badge {
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
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
        .sensor-row:last-child { border-bottom: none; }
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
            margin-bottom: 0.8rem;
        }
        .progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s;
        }
        .fill-good { background: var(--green-success); }
        .fill-warning { background: var(--yellow-warning); }
        .fill-critical { background: var(--red-deep); }
        
        .alert-icon { 
            font-size: 0.8rem; 
            font-weight: 600;
            color: var(--red-deep);
        }
        
        .loading {
            text-align: center;
            padding: 3rem;
            color: var(--text-mid);
        }
        
        .stats-summary {
            background: var(--white);
            border-radius: 12px;
            padding: 0.8rem 1.2rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--gray-border);
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
        }
        .stat-value {
            font-weight: 700;
            color: var(--red-deep);
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
            z-index: 1000;
            cursor: pointer;
        }
        
        /* Gender Icon */
        .gender-icon {
            font-size: 0.9rem;
            margin-right: 0.3rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .main-content { margin-left: 0; }
            .restroom-grid { grid-template-columns: 1fr; }
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
                        <path d="M10.5 16C11 15.4 11.5 15 12 15C12.5 15 13 15.4 13.5 16" stroke="#fff" stroke-width="1.4" stroke-linecap="round"/>
                        <circle cx="12" cy="18" r="1" fill="#fff"/>
                    </svg>
                </div>
                <span>SmartWash</span>
            </div>
            <div class="sidebar-sub">BatStateU ARASOF-Nasugbu</div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-item"><a href="dashboard.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>Dashboard</a></div>
            <div class="nav-item"><a href="live_monitoring.php" class="nav-link active"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>Live Monitoring</a></div>
            <div class="nav-item"><a href="vape_incidents.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v4a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Vape Incidents</a></div>
            <div class="nav-item"><a href="air_quality.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 4a1 1 0 011-1h8a1 1 0 011 1v1a1 1 0 01-1 1H6a1 1 0 01-1-1V4zM4 9a1 1 0 011-1h10a1 1 0 011 1v1a1 1 0 01-1 1H5a1 1 0 01-1-1V9zM6 14a1 1 0 100-2h8a1 1 0 100 2H6z" clip-rule="evenodd"/></svg>Air Quality</a></div>
            <div class="nav-item"><a href="checklists.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Checklists</a></div>
            <div class="nav-item"><a href="maintenance_log.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>Maintenance Log</a></div>
            <div class="nav-item"><a href="alerts.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v4a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Alerts</a></div>
            <div class="nav-item"><a href="staff_management.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>Staff Management</a></div>
            <div class="nav-item"><a href="settings.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>Settings</a></div>
        </nav>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h1>Live Monitoring</h1>
                <p>Real-time sensor data from all restroom facilities</p>
            </div>
            <div class="top-bar-right">
                <form method="POST" action="../../auth/logout.php" style="margin:0;">
                    <button type="submit" class="logout-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Logout
                    </button>
                </form>
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-name"><?php echo htmlspecialchars($fullName); ?></div>
                        <div class="user-role">Supervisor</div>
                    </div>
                    <div class="user-avatar"><?php echo htmlspecialchars($initials); ?></div>
                </div>
            </div>
        </div>

        <!-- Stats Summary -->
        <div class="stats-summary" id="statsSummary">
            <div class="stat-item">🏢 Total Restrooms: <span class="stat-value" id="totalCount">12</span></div>
            <div class="stat-item">✅ Good: <span class="stat-value" id="goodCount">0</span></div>
            <div class="stat-item">⚠️ Warning: <span class="stat-value" id="warningCount">0</span></div>
            <div class="stat-item">🔴 Critical: <span class="stat-value" id="criticalCount">0</span></div>
        </div>

        <!-- Refresh Controls -->
        <div class="refresh-bar">
            <div class="auto-refresh-toggle">
                <label>
                    <input type="checkbox" id="autoRefreshToggle" checked> Auto-refresh (10s)
                </label>
            </div>
            <div>
                <button class="refresh-btn" onclick="manualRefresh()">⟳ Refresh Now</button>
            </div>
        </div>

        <div class="restroom-grid" id="restroomGrid">
            <div class="loading">Loading restroom data...</div>
        </div>
        <div class="last-updated" id="lastUpdated">Last updated: Just now</div>
    </main>
</div>

<div class="demo-badge" onclick="toggleDemoMode()">🧪 DEMO MODE | Live sensor simulation active</div>

<script>
// =============================================
// CONFIGURATION
// =============================================
const USE_DEMO_MODE = true;
const API_BASE = '../../api/';
let autoRefreshInterval = null;
let isAutoRefreshEnabled = true;

// =============================================
// ACTUAL RESTROOM DATA (HEB and CTEB Buildings)
// =============================================
let restrooms = [
    // HEB Building (2nd and 3rd Floors)
    { id: 1, name: 'HEB - 2nd Floor Male', building: 'HEB', floor: '2nd', gender: 'male', soap: 85, air: 32, vape: false, smoke: false },
    { id: 2, name: 'HEB - 2nd Floor Female', building: 'HEB', floor: '2nd', gender: 'female', soap: 92, air: 28, vape: false, smoke: false },
    { id: 3, name: 'HEB - 3rd Floor Male', building: 'HEB', floor: '3rd', gender: 'male', soap: 78, air: 35, vape: false, smoke: false },
    { id: 4, name: 'HEB - 3rd Floor Female', building: 'HEB', floor: '3rd', gender: 'female', soap: 88, air: 30, vape: false, smoke: false },
    
    // CTEB Building (1st, 2nd, 3rd, 4th Floors)
    { id: 5, name: 'CTEB - 1st Floor Male', building: 'CTEB', floor: '1st', gender: 'male', soap: 95, air: 25, vape: false, smoke: false },
    { id: 6, name: 'CTEB - 1st Floor Female', building: 'CTEB', floor: '1st', gender: 'female', soap: 90, air: 22, vape: false, smoke: false },
    { id: 7, name: 'CTEB - 2nd Floor Male', building: 'CTEB', floor: '2nd', gender: 'male', soap: 45, air: 42, vape: false, smoke: false },
    { id: 8, name: 'CTEB - 2nd Floor Female', building: 'CTEB', floor: '2nd', gender: 'female', soap: 50, air: 38, vape: false, smoke: false },
    { id: 9, name: 'CTEB - 3rd Floor Male', building: 'CTEB', floor: '3rd', gender: 'male', soap: 35, air: 55, vape: true, smoke: false },
    { id: 10, name: 'CTEB - 3rd Floor Female', building: 'CTEB', floor: '3rd', gender: 'female', soap: 40, air: 48, vape: false, smoke: false },
    { id: 11, name: 'CTEB - 4th Floor Male', building: 'CTEB', floor: '4th', gender: 'male', soap: 15, air: 72, vape: false, smoke: true },
    { id: 12, name: 'CTEB - 4th Floor Female', building: 'CTEB', floor: '4th', gender: 'female', soap: 20, air: 65, vape: false, smoke: false }
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

function getSoapValueClass(soap) {
    if (soap < 20) return 'low';
    if (soap < 50) return 'warning';
    return '';
}

function getAirValueClass(air) {
    if (air > 70) return 'low';
    if (air > 40) return 'warning';
    return '';
}

function getGenderIcon(gender) {
    return gender === 'male' ? '👨' : '👩';
}

function updateStats() {
    let good = 0, warning = 0, critical = 0;
    
    restrooms.forEach(r => {
        const status = getStatusClass(r.soap, r.air, r.vape, r.smoke);
        if (status === 'good') good++;
        else if (status === 'warning') warning++;
        else critical++;
    });
    
    document.getElementById('goodCount').textContent = good;
    document.getElementById('warningCount').textContent = warning;
    document.getElementById('criticalCount').textContent = critical;
    document.getElementById('totalCount').textContent = restrooms.length;
}

function renderRestrooms() {
    const grid = document.getElementById('restroomGrid');
    if (!grid) return;
    
    grid.innerHTML = restrooms.map(r => {
        const statusClass = getStatusClass(r.soap, r.air, r.vape, r.smoke);
        const statusText = getStatusText(r.soap, r.air, r.vape, r.smoke);
        const fillClass = getFillClass(r.soap, r.air, r.vape, r.smoke);
        const soapClass = getSoapValueClass(r.soap);
        const airClass = getAirValueClass(r.air);
        const genderIcon = getGenderIcon(r.gender);
        
        return `
            <div class="restroom-card">
                <div class="restroom-header">
                    <div>
                        <span class="restroom-name"><span class="gender-icon">${genderIcon}</span> ${escapeHtml(r.name)}</span>
                        <div class="restroom-location">📍 ${escapeHtml(r.building)} Building - ${escapeHtml(r.floor)} Floor</div>
                    </div>
                    <span class="status-badge status-${statusClass === 'good' ? 'good' : (statusClass === 'warning' ? 'warning' : 'critical')}">${statusText}</span>
                </div>
                <div class="restroom-body">
                    <div class="sensor-row">
                        <span class="sensor-label">🧴 Soap Level</span>
                        <span class="sensor-value ${soapClass}">${Math.round(r.soap)}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill ${fillClass}" style="width: ${Math.min(Math.max(r.soap, 0), 100)}%"></div>
                    </div>
                    
                    <div class="sensor-row">
                        <span class="sensor-label">🌬️ Air Quality (AQI)</span>
                        <span class="sensor-value ${airClass}">${Math.round(r.air)}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill ${r.air > 70 ? 'fill-critical' : (r.air > 40 ? 'fill-warning' : 'fill-good')}" style="width: ${Math.min(Math.max(r.air, 0), 100)}%"></div>
                    </div>
                    
                    ${r.vape ? `<div class="sensor-row"><span class="sensor-label">🚭 Vape Detection</span><span class="alert-icon">🔴 ACTIVE</span></div>` : ''}
                    ${r.smoke ? `<div class="sensor-row"><span class="sensor-label">🔥 Smoke Detection</span><span class="alert-icon">🔴 ACTIVE</span></div>` : ''}
                </div>
            </div>
        `;
    }).join('');
    
    updateStats();
    document.getElementById('lastUpdated').innerHTML = `Last updated: ${new Date().toLocaleTimeString()}`;
}

function updateSensorData() {
    // Demo mode: simulate random changes
    restrooms.forEach(r => {
        // Soap gradually decreases then refills randomly
        r.soap = Math.max(0, Math.min(100, r.soap + (Math.random() - 0.5) * 5));
        // Air quality fluctuates
        r.air = Math.max(0, Math.min(150, r.air + (Math.random() - 0.5) * 5));
        // Random vape/smoke detection (3% chance to toggle)
        if (Math.random() < 0.03) r.vape = !r.vape;
        if (Math.random() < 0.02) r.smoke = !r.smoke;
    });
    
    renderRestrooms();
}

function manualRefresh() {
    updateSensorData();
}

function startAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
    if (isAutoRefreshEnabled) {
        autoRefreshInterval = setInterval(() => {
            updateSensorData();
        }, 10000);
    }
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }
}

function toggleAutoRefresh() {
    const toggle = document.getElementById('autoRefreshToggle');
    isAutoRefreshEnabled = toggle.checked;
    
    if (isAutoRefreshEnabled) {
        startAutoRefresh();
    } else {
        stopAutoRefresh();
    }
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function toggleDemoMode() {
    if (confirm('Toggle between Demo and Live mode?\n\nCurrently in: DEMO MODE\n\nNote: Live mode would connect to:\n- api/get_sensors.php\n- Real-time sensor data from ESP32 devices')) {
        alert('Switching to live mode would fetch real sensor data from the database.');
        window.location.reload();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    renderRestrooms();
    startAutoRefresh();
    updateStats();
    
    const autoToggle = document.getElementById('autoRefreshToggle');
    if (autoToggle) {
        autoToggle.addEventListener('change', toggleAutoRefresh);
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
});
</script>
</body>
</html>