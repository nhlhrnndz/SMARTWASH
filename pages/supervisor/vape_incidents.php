<?php
// ============================================================
//  SmartWash - Vape Incidents Page
//  File: pages/supervisor/vape_incidents.php
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
    <title>Vape Incidents — SmartWash | BatStateU ARASOF-Nasugbu</title>
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

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.2rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--white);
            border-radius: 16px;
            padding: 1.2rem;
            border: 1px solid var(--gray-border);
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px var(--shadow);
        }
        .stat-value {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 900;
            color: var(--red-deep);
        }
        .stat-label {
            font-size: 0.75rem;
            color: var(--text-mid);
            opacity: 0.7;
        }

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

        /* Date Range Picker */
        .date-range {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .date-input {
            padding: 0.4rem 0.8rem;
            border: 1px solid var(--gray-border);
            border-radius: 8px;
            font-size: 0.75rem;
            font-family: 'DM Sans', sans-serif;
        }
        .export-btn {
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
        .export-btn:hover { background: #1b5e20; transform: scale(1.02); }

        /* Table */
        .table-container {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid var(--gray-border);
            overflow-x: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--gray-border);
        }
        th {
            background: var(--light);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-mid);
        }
        tr:hover {
            background: rgba(197,0,0,0.02);
        }

        /* Badges */
        .badge-vape {
            background: rgba(197,0,0,0.15);
            color: var(--red-deep);
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }
        .badge-smoke {
            background: rgba(245,124,0,0.15);
            color: var(--orange-critical);
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }
        .badge-active {
            background: rgba(197,0,0,0.15);
            color: var(--red-deep);
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }
        .badge-resolved {
            background: rgba(46,125,50,0.15);
            color: var(--green-success);
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }
        .severity-high {
            color: var(--red-deep);
            font-weight: 600;
        }
        .severity-critical {
            color: var(--orange-critical);
            font-weight: 600;
        }
        .severity-medium {
            color: var(--yellow-warning);
            font-weight: 600;
        }
        .severity-low {
            color: var(--blue-info);
            font-weight: 600;
        }

        /* Action Button */
        .btn-resolve {
            background: var(--green-success);
            color: white;
            border: none;
            padding: 0.3rem 0.8rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-resolve:hover {
            background: #1b5e20;
            transform: scale(1.02);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-mid);
            opacity: 0.6;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            padding: 1rem;
        }
        .page-btn {
            padding: 0.3rem 0.8rem;
            border: 1px solid var(--gray-border);
            background: var(--white);
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.7rem;
            transition: all 0.2s;
        }
        .page-btn:hover { border-color: var(--red-deep); color: var(--red-deep); }
        .page-btn.active { background: var(--red-deep); color: var(--white); border-color: var(--red-deep); }

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
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            th, td { padding: 0.5rem; font-size: 0.75rem; }
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
            <div class="nav-item"><a href="live_monitoring.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>Live Monitoring</a></div>
            <div class="nav-item"><a href="vape_incidents.php" class="nav-link active"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v4a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Vape Incidents</a></div>
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
                <h1>Vape & Smoke Incidents</h1>
                <p>Track and monitor all detected incidents across all restrooms, etc</p>
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

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value" id="totalIncidents">12</div>
                <div class="stat-label">Total Incidents</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="weeklyIncidents">3</div>
                <div class="stat-label">This Week</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="activeIncidents">2</div>
                <div class="stat-label">Active / Unresolved</div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <button class="filter-btn active" onclick="filterIncidents('all')">All Incidents</button>
            <button class="filter-btn" onclick="filterIncidents('vape')">Vape</button>
            <button class="filter-btn" onclick="filterIncidents('smoke')">Smoke</button>
            <button class="filter-btn" onclick="filterIncidents('active')">Active</button>
            <button class="filter-btn" onclick="filterIncidents('resolved')">Resolved</button>
        </div>

        <!-- Date Range & Export -->
        <div class="date-range">
            <input type="date" id="startDate" class="date-input" value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>">
            <span>to</span>
            <input type="date" id="endDate" class="date-input" value="<?php echo date('Y-m-d'); ?>">
            <button class="export-btn" onclick="exportIncidents()">📥 Export to CSV</button>
        </div>

        <!-- Incidents Table -->
        <div class="table-container">
            <table id="incidentsTable">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Restroom</th>
                        <th>Type</th>
                        <th>Severity</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="incidentsTableBody">
                    <tr><td colspan="6" class="empty-state">Loading incidents...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="pagination" id="pagination"></div>
    </main>
</div>

<div class="demo-badge" onclick="toggleDemoMode()">🧪 DEMO MODE | Incident Log Data</div>

<script>
// =============================================
// CONFIGURATION
// =============================================
const USE_DEMO_MODE = true;
const API_BASE = '../../api/';
let currentFilter = 'all';
let currentPage = 1;
const itemsPerPage = 10;

// Demo Incidents Data
let allIncidents = [
    { id: 1, datetime: '2026-04-28 10:15:00', restroom: 'GLR2 - Ground Right', type: 'vape', severity: 'high', status: 'active' },
    { id: 2, datetime: '2026-04-28 09:45:00', restroom: '3F-CR - 3rd Floor CR', type: 'smoke', severity: 'critical', status: 'active' },
    { id: 3, datetime: '2026-04-27 16:20:00', restroom: '2F-MR - 2nd Floor Main', type: 'vape', severity: 'medium', status: 'resolved' },
    { id: 4, datetime: '2026-04-27 14:30:00', restroom: 'GLR1 - Ground Left', type: 'vape', severity: 'low', status: 'resolved' },
    { id: 5, datetime: '2026-04-27 11:15:00', restroom: '1F-RR - 1st Floor Rear', type: 'smoke', severity: 'high', status: 'resolved' },
    { id: 6, datetime: '2026-04-26 09:30:00', restroom: '2F-LR - 2nd Floor Left', type: 'vape', severity: 'medium', status: 'resolved' },
    { id: 7, datetime: '2026-04-25 15:45:00', restroom: '1F-FR - 1st Floor Front', type: 'vape', severity: 'low', status: 'resolved' },
    { id: 8, datetime: '2026-04-25 10:00:00', restroom: '3F-NR - 3rd Floor North', type: 'smoke', severity: 'medium', status: 'resolved' },
    { id: 9, datetime: '2026-04-24 13:20:00', restroom: 'GLR2 - Ground Right', type: 'vape', severity: 'high', status: 'resolved' },
    { id: 10, datetime: '2026-04-24 08:45:00', restroom: '2F-MR - 2nd Floor Main', type: 'vape', severity: 'medium', status: 'resolved' },
    { id: 11, datetime: '2026-04-23 17:00:00', restroom: '1F-RR - 1st Floor Rear', type: 'smoke', severity: 'critical', status: 'resolved' },
    { id: 12, datetime: '2026-04-23 09:15:00', restroom: 'GLR1 - Ground Left', type: 'vape', severity: 'low', status: 'resolved' }
];

function filterIncidentsByType() {
    let filtered = allIncidents;
    
    if (currentFilter === 'vape') {
        filtered = allIncidents.filter(i => i.type === 'vape');
    } else if (currentFilter === 'smoke') {
        filtered = allIncidents.filter(i => i.type === 'smoke');
    } else if (currentFilter === 'active') {
        filtered = allIncidents.filter(i => i.status === 'active');
    } else if (currentFilter === 'resolved') {
        filtered = allIncidents.filter(i => i.status === 'resolved');
    }
    
    return filtered;
}

function filterIncidentsByDateRange(incidents) {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (!startDate && !endDate) return incidents;
    
    return incidents.filter(incident => {
        const incidentDate = incident.datetime.split(' ')[0];
        if (startDate && incidentDate < startDate) return false;
        if (endDate && incidentDate > endDate) return false;
        return true;
    });
}

function displayIncidents() {
    let filteredIncidents = filterIncidentsByType();
    filteredIncidents = filterIncidentsByDateRange(filteredIncidents);
    
    // Sort by datetime descending (newest first)
    filteredIncidents.sort((a, b) => new Date(b.datetime) - new Date(a.datetime));
    
    const totalPages = Math.ceil(filteredIncidents.length / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const paginatedIncidents = filteredIncidents.slice(startIndex, startIndex + itemsPerPage);
    
    const tbody = document.getElementById('incidentsTableBody');
    
    if (paginatedIncidents.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="empty-state">No incidents found for the selected filters</td></tr>';
        document.getElementById('pagination').innerHTML = '';
        return;
    }
    
    tbody.innerHTML = paginatedIncidents.map(incident => `
        <tr>
            <td style="white-space: nowrap;">${formatDateTime(incident.datetime)}</td>
            <td>${escapeHtml(incident.restroom)}</td>
            <td><span class="badge-${incident.type === 'vape' ? 'vape' : 'smoke'}">${incident.type === 'vape' ? '🚭 Vape' : '🔥 Smoke'}</span></td>
            <td><span class="severity-${incident.severity}">${getSeverityText(incident.severity)}</span></td>
            <td><span class="badge-${incident.status === 'active' ? 'active' : 'resolved'}">${incident.status === 'active' ? '⚠️ Active' : '✅ Resolved'}</span></td>
            <td>
                ${incident.status === 'active' ? `<button class="btn-resolve" onclick="resolveIncident(${incident.id})">Mark Resolved</button>` : '—'}
            </td>
        </tr>
    `).join('');
    
    // Update stats
    updateStats();
    
    // Pagination
    let paginationHtml = '';
    for (let i = 1; i <= totalPages; i++) {
        paginationHtml += `<button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
    }
    document.getElementById('pagination').innerHTML = paginationHtml;
}

function goToPage(page) {
    currentPage = page;
    displayIncidents();
}

function filterIncidents(filter) {
    currentFilter = filter;
    currentPage = 1;
    
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent.toLowerCase().includes(filter.toLowerCase()) || 
            (filter === 'all' && btn.textContent === 'All Incidents')) {
            btn.classList.add('active');
        }
    });
    
    displayIncidents();
}

function resolveIncident(incidentId) {
    if (!confirm('Mark this incident as resolved?')) return;
    
    if (USE_DEMO_MODE) {
        const incident = allIncidents.find(i => i.id === incidentId);
        if (incident && incident.status === 'active') {
            incident.status = 'resolved';
            displayIncidents();
            showToast(`Incident at ${incident.restroom} marked as resolved.`, 'success');
        }
    } else {
        // API call would go here
        fetch(API_BASE + 'resolve_incident.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: incidentId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayIncidents();
                showToast('Incident resolved successfully!', 'success');
            }
        });
    }
}

function exportIncidents() {
    let filteredIncidents = filterIncidentsByType();
    filteredIncidents = filterIncidentsByDateRange(filteredIncidents);
    filteredIncidents.sort((a, b) => new Date(b.datetime) - new Date(a.datetime));
    
    if (filteredIncidents.length === 0) {
        alert('No incidents to export for the selected filters.');
        return;
    }
    
    // Create CSV content
    const headers = ['Date & Time', 'Restroom', 'Type', 'Severity', 'Status'];
    const csvRows = [headers];
    
    filteredIncidents.forEach(incident => {
        csvRows.push([
            incident.datetime,
            incident.restroom,
            incident.type === 'vape' ? 'Vape' : 'Smoke',
            getSeverityText(incident.severity),
            incident.status === 'active' ? 'Active' : 'Resolved'
        ]);
    });
    
    const csvContent = csvRows.map(row => row.join(',')).join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `incidents_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    
    if (USE_DEMO_MODE) {
        showToast(`Exported ${filteredIncidents.length} incidents to CSV`, 'success');
    }
}

function updateStats() {
    if (USE_DEMO_MODE) {
        const total = allIncidents.length;
        const active = allIncidents.filter(i => i.status === 'active').length;
        
        // Calculate weekly incidents (last 7 days)
        const sevenDaysAgo = new Date();
        sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);
        const weekly = allIncidents.filter(i => new Date(i.datetime) >= sevenDaysAgo).length;
        
        document.getElementById('totalIncidents').textContent = total;
        document.getElementById('weeklyIncidents').textContent = weekly;
        document.getElementById('activeIncidents').textContent = active;
    }
}

function getSeverityText(severity) {
    const texts = {
        'critical': '🔴 Critical',
        'high': '🟠 High',
        'medium': '🟡 Medium',
        'low': '🔵 Low'
    };
    return texts[severity] || severity;
}

function formatDateTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        hour: 'numeric', 
        minute: '2-digit',
        hour12: true 
    });
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function toggleDemoMode() {
    if (confirm('Toggle between Demo and Live mode?\n\nCurrently in: DEMO MODE\n\nNote: Live mode would connect to:\n- api/get_incidents.php\n- api/resolve_incident.php')) {
        alert('Switching to live mode would fetch real incident data from the database.');
    }
}

// Add toast styles if not present
const style = document.createElement('style');
style.textContent = `
    .toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        color: white;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    }
    .toast.success { background: var(--green-success); }
    .toast.error { background: var(--red-deep); }
    .toast.info { background: var(--blue-info); }
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
`;
document.head.appendChild(style);

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    displayIncidents();
    
    // Add event listeners for date range
    document.getElementById('startDate').addEventListener('change', () => {
        currentPage = 1;
        displayIncidents();
    });
    document.getElementById('endDate').addEventListener('change', () => {
        currentPage = 1;
        displayIncidents();
    });
});
</script>
</body>
</html>