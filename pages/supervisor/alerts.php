<?php
// ============================================================
//  SmartWash - Alerts Page
//  File: pages/supervisor/alerts.php
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
    <title>Alerts — SmartWash | BatStateU ARASOF-Nasugbu</title>
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
        body { font-family: 'DM Sans', sans-serif; background: var(--cream); color: var(--text-dark); overflow-x: hidden; }
        
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
        .sidebar-header { padding: 1.8rem 1.5rem; border-bottom: 1px solid var(--gray-border); text-align: center; }
        .sidebar-logo { display: flex; align-items: center; justify-content: center; gap: 0.7rem; margin-bottom: 0.5rem; }
        .sidebar-icon { width: 40px; height: 40px; background: var(--red-deep); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .sidebar-icon svg { width: 22px; height: 22px; }
        .sidebar-icon svg path { fill: var(--white); }
        .sidebar-logo span { font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 900; color: var(--text-dark); }
        .sidebar-sub { font-size: 0.7rem; color: var(--text-mid); opacity: 0.7; }
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
        .main-content { flex: 1; margin-left: 280px; padding: 1.5rem 2rem; }
        
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
        .page-title h1 { font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 700; color: var(--text-dark); }
        .page-title p { font-size: 0.8rem; color: var(--text-mid); opacity: 0.7; }
        .top-bar-right { display: flex; align-items: center; gap: 1.5rem; }
        .logout-btn { background: none; border: 1px solid var(--gray-border); padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-size: 0.75rem; font-weight: 500; color: var(--text-mid); transition: all 0.2s; }
        .logout-btn:hover { background: var(--red-deep); border-color: var(--red-deep); color: var(--white); }
        .user-menu { display: flex; align-items: center; gap: 1rem; cursor: pointer; }
        .user-avatar { width: 44px; height: 44px; background: linear-gradient(135deg, var(--red-deep), var(--red-vivid)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 1.1rem; }
        .user-info { text-align: right; }
        .user-name { font-weight: 700; font-size: 0.88rem; }
        .user-role { font-size: 0.7rem; opacity: 0.6; }
        
        /* Filter Bar */
        .filter-bar {
            display: flex;
            gap: 0.8rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .filter-btn {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            border: 1px solid var(--gray-border);
            background: var(--white);
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .filter-btn:hover { border-color: var(--red-deep); color: var(--red-deep); }
        .filter-btn.active { background: var(--red-deep); color: var(--white); border-color: var(--red-deep); }
        
        /* Cards */
        .card {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid var(--gray-border);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        
        /* Alert Items */
        .alert-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--gray-border);
            transition: background 0.2s;
        }
        .alert-item:hover { background: var(--light); }
        .alert-item:last-child { border-bottom: none; }
        
        .alert-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        .alert-icon.critical { background: rgba(197,0,0,0.1); }
        .alert-icon.warning { background: rgba(245,124,0,0.1); }
        .alert-icon.info { background: rgba(21,101,192,0.1); }
        
        .alert-content { flex: 1; }
        .alert-title {
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }
        .alert-title.critical { color: var(--red-deep); }
        .alert-title.warning { color: var(--orange-critical); }
        .alert-title.info { color: var(--blue-info); }
        
        .alert-details {
            font-size: 0.75rem;
            color: var(--text-mid);
            opacity: 0.7;
            margin-bottom: 0.3rem;
        }
        .alert-time {
            font-size: 0.65rem;
            color: var(--text-mid);
            opacity: 0.5;
        }
        
        .alert-badge {
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: inline-block;
        }
        .badge-critical { background: rgba(197,0,0,0.15); color: var(--red-deep); }
        .badge-warning { background: rgba(245,124,0,0.15); color: var(--orange-critical); }
        .badge-info { background: rgba(21,101,192,0.15); color: var(--blue-info); }
        
        .btn-resolve {
            background: var(--green-success);
            color: var(--white);
            border: none;
            padding: 0.4rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-resolve:hover { background: #1b5e20; transform: scale(1.02); }
        
        .btn-resolve-all {
            background: var(--red-deep);
            color: var(--white);
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
            margin-bottom: 1rem;
        }
        .btn-resolve-all:hover { background: var(--red-vivid); }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-mid);
            opacity: 0.6;
        }
        
        /* Demo Badge */
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
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .main-content { margin-left: 0; }
            .alert-item { flex-direction: column; align-items: flex-start; gap: 0.8rem; }
            .btn-resolve { align-self: flex-end; }
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <div class="sidebar-icon">
                    <svg viewBox="0 0 24 24">
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
            <div class="nav-item"><a href="live_monitoring.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>Live Monitoring</a></div>
            <div class="nav-item"><a href="vape_incidents.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v4a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Vape Incidents</a></div>
            <div class="nav-item"><a href="air_quality.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M5 5a1 1 0 000 2h8a1 1 0 100-2H5zm0 4a1 1 0 100 2h8a1 1 0 100-2H5zm0 4a1 1 0 100 2h8a1 1 0 100-2H5z"/><path fill-rule="evenodd" d="M3 3a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V3zm2 0h10v14H5V3z" clip-rule="evenodd"/></svg>Air Quality</a></div>
            <div class="nav-item"><a href="checklists.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Checklists</a></div>
            <div class="nav-item"><a href="maintenance_log.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Maintenance Log</a></div>
            <div class="nav-item"><a href="alerts.php" class="nav-link active"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Alerts</a></div>
            <div class="nav-item"><a href="staff_management.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.758 11.226C12.028 10.464 10.6 10 9 10s-3.028.464-3.758 1.226A2 2 0 004 13v1h10v-1a2 2 0 00-1.242-1.774zM4.5 17a.5.5 0 01.5-.5h8a.5.5 0 010 1h-8a.5.5 0 01-.5-.5z"/></svg>Staff Management</a></div>
            <div class="nav-item"><a href="settings.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>Settings</a></div>
        </nav>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h1>System Alerts</h1>
                <p>Real-time alerts requiring immediate attention</p>
            </div>
            <div class="top-bar-right">
                <form method="POST" action="../../auth/logout.php" style="margin:0;">
                    <button type="submit" class="logout-btn">Logout</button>
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

        <!-- Filter Bar -->
        <div class="filter-bar">
            <button class="filter-btn active" onclick="filterAlerts('all')">All Alerts</button>
            <button class="filter-btn" onclick="filterAlerts('critical')">Critical</button>
            <button class="filter-btn" onclick="filterAlerts('warning')">Warnings</button>
            <button class="filter-btn" onclick="filterAlerts('resolved')">Resolved</button>
        </div>

        <!-- Resolve All Button -->
        <div style="text-align: right; margin-bottom: 1rem;">
            <button class="btn-resolve-all" onclick="resolveAllAlerts()">✓ Resolve All Alerts</button>
        </div>

        <!-- Alerts Card -->
        <div class="card">
            <div id="alertsList"></div>
        </div>
    </main>
</div>

<!-- Demo Badge -->
<div class="demo-badge" onclick="toggleDemoMode()" title="Click to toggle between Demo and Live mode">
    🧪 DEMO MODE | Alert System
</div>

<script>
// Alert Data
let alerts = [
    {
        id: 1,
        type: 'critical',
        icon: '🔴',
        title: 'Vape Detected',
        message: 'Vape/smoke detected by sensor',
        location: 'GLR2 - Ground Right',
        time: '10:15 AM',
        date: '2026-04-28',
        status: 'active'
    },
    {
        id: 2,
        type: 'critical',
        icon: '🔴',
        title: 'Smoke Detected',
        message: 'Smoke particles detected in restroom',
        location: '3F-CR - 3rd Floor CR',
        time: '09:45 AM',
        date: '2026-04-28',
        status: 'active'
    },
    {
        id: 3,
        type: 'warning',
        icon: '🟡',
        title: 'Soap Level Low',
        message: 'Soap dispenser at 12% capacity',
        location: '1F-FR - 1st Floor Front',
        time: '09:30 AM',
        date: '2026-04-28',
        status: 'active'
    },
    {
        id: 4,
        type: 'warning',
        icon: '🟡',
        title: 'Soap Level Low',
        message: 'Soap dispenser at 15% capacity',
        location: 'GLR1 - Ground Left',
        time: '08:20 AM',
        date: '2026-04-28',
        status: 'active'
    },
    {
        id: 5,
        type: 'critical',
        icon: '🔴',
        title: 'Air Quality Critical',
        message: 'AQI reached 85 (Unhealthy)',
        location: '2F-MR - 2nd Floor Main',
        time: '07:45 AM',
        date: '2026-04-28',
        status: 'active'
    },
    {
        id: 6,
        type: 'warning',
        icon: '🟡',
        title: 'Paper Towel Empty',
        message: 'Paper towel dispenser needs refill',
        location: '1F-RR - 1st Floor Rear',
        time: 'Yesterday',
        date: '2026-04-27',
        status: 'resolved'
    }
];

let currentFilter = 'all';

function displayAlerts() {
    const container = document.getElementById('alertsList');
    let filteredAlerts = alerts;
    
    if (currentFilter === 'critical') {
        filteredAlerts = alerts.filter(a => a.type === 'critical' && a.status === 'active');
    } else if (currentFilter === 'warning') {
        filteredAlerts = alerts.filter(a => a.type === 'warning' && a.status === 'active');
    } else if (currentFilter === 'resolved') {
        filteredAlerts = alerts.filter(a => a.status === 'resolved');
    } else {
        filteredAlerts = alerts.filter(a => a.status === 'active');
    }
    
    if (filteredAlerts.length === 0) {
        container.innerHTML = '<div class="empty-state">✅ No alerts found. All systems operational!</div>';
        return;
    }
    
    container.innerHTML = filteredAlerts.map(alert => `
        <div class="alert-item" data-id="${alert.id}" data-type="${alert.type}" data-status="${alert.status}">
            <div style="display: flex; align-items: flex-start; gap: 1rem; flex: 1;">
                <div class="alert-icon ${alert.type}">${alert.icon}</div>
                <div class="alert-content">
                    <span class="alert-badge badge-${alert.type}">${alert.type.toUpperCase()}</span>
                    <div class="alert-title ${alert.type}">${escapeHtml(alert.title)}</div>
                    <div class="alert-details">${escapeHtml(alert.message)} • ${escapeHtml(alert.location)}</div>
                    <div class="alert-time">${escapeHtml(alert.time)} • ${escapeHtml(alert.date)}</div>
                </div>
            </div>
            ${alert.status === 'active' ? `<button class="btn-resolve" onclick="resolveAlert(${alert.id})">Mark Resolved</button>` : '<span style="color: #2e7d32; font-size: 0.7rem; font-weight: 600;">✓ Resolved</span>'}
        </div>
    `).join('');
}

function resolveAlert(alertId) {
    if (confirm('Mark this alert as resolved?')) {
        const alertIndex = alerts.findIndex(a => a.id === alertId);
        if (alertIndex !== -1) {
            alerts[alertIndex].status = 'resolved';
            displayAlerts();
            
            // Show success message
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = '✓ Resolved';
            btn.style.background = '#2e7d32';
            setTimeout(() => {
                displayAlerts();
            }, 500);
        }
    }
}

function resolveAllAlerts() {
    const activeAlerts = alerts.filter(a => a.status === 'active');
    if (activeAlerts.length === 0) {
        alert('No active alerts to resolve.');
        return;
    }
    
    if (confirm(`Resolve all ${activeAlerts.length} active alerts?`)) {
        alerts.forEach(alert => {
            if (alert.status === 'active') {
                alert.status = 'resolved';
            }
        });
        displayAlerts();
        alert('All alerts have been marked as resolved.');
    }
}

function filterAlerts(filter) {
    currentFilter = filter;
    
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent.toLowerCase().includes(filter.toLowerCase())) {
            btn.classList.add('active');
        }
    });
    
    displayAlerts();
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function toggleDemoMode() {
    if (confirm('Toggle between Demo and Live mode?\n\nCurrently in: DEMO MODE\n\nNote: Live mode would connect to:\n- api/get_alerts.php\n- api/resolve_alert.php')) {
        alert('Switching to live mode would fetch real alerts from the database.');
    }
}

// Auto-refresh alerts every 30 seconds (demo)
setInterval(() => {
    console.log('Polling for new alerts...');
    // In production: fetch from api/get_alerts.php
}, 30000);

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    displayAlerts();
});
</script>
</body>
</html>