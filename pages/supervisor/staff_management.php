<?php
// ============================================================
//  SmartWash - Staff Management Page
//  File: pages/supervisor/staff_management.php
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
    <title>Staff Management — SmartWash | BatStateU ARASOF-Nasugbu</title>
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
            --green-soft: #e8f5e9;
            --red-soft: #ffebee;
            --orange-warning: #f57c00;
            --gray-border: #e0d8c8;
            --blue-info: #1565C0;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Layout */
        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

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

        .sidebar-icon svg {
            width: 22px;
            height: 22px;
        }

        .sidebar-icon svg path {
            fill: var(--white);
        }

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

        .sidebar-nav {
            padding: 1.5rem 1rem;
        }

        .nav-item {
            margin-bottom: 0.3rem;
        }

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

        .nav-link svg {
            width: 20px;
            height: 20px;
            color: #aaa;
            transition: all 0.2s;
        }

        .nav-link:hover {
            background: rgba(197,0,0,0.05);
            color: var(--red-deep);
        }

        .nav-link:hover svg {
            color: var(--red-deep);
        }

        .nav-link.active {
            background: var(--red-deep);
            color: var(--white);
        }

        .nav-link.active svg {
            color: var(--white);
        }

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

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

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

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 700;
            font-size: 0.88rem;
        }

        .user-role {
            font-size: 0.7rem;
            opacity: 0.6;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
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

        /* Tabs */
        .tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--gray-border);
        }

        .tab-btn {
            padding: 0.75rem 1.5rem;
            background: none;
            border: none;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-mid);
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .tab-btn::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--red-deep);
            transform: scaleX(0);
            transition: transform 0.2s;
        }

        .tab-btn:hover {
            color: var(--red-deep);
        }

        .tab-btn.active {
            color: var(--red-deep);
        }

        .tab-btn.active::after {
            transform: scaleX(1);
        }

        /* Tab Content */
        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

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
        .badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .badge-pending {
            background: #fff3e0;
            color: var(--orange-warning);
        }

        .badge-approved {
            background: var(--green-soft);
            color: var(--green-success);
        }

        .badge-rejected {
            background: var(--red-soft);
            color: var(--red-deep);
        }

        .badge-active {
            background: var(--green-soft);
            color: var(--green-success);
        }

        .badge-inactive {
            background: var(--red-soft);
            color: var(--red-deep);
        }

        .badge-supervisor {
            background: rgba(21,101,192,0.15);
            color: var(--blue-info);
        }

        .badge-maintenance {
            background: rgba(46,125,50,0.15);
            color: var(--green-success);
        }

        /* Buttons */
        .btn-approve {
            background: var(--green-success);
            color: white;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 600;
            margin-right: 0.3rem;
            transition: all 0.2s;
        }

        .btn-approve:hover {
            background: #1b5e20;
            transform: translateY(-1px);
        }

        .btn-reject {
            background: var(--red-deep);
            color: white;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-reject:hover {
            background: #9b0000;
            transform: translateY(-1px);
        }

        .btn-deactivate {
            background: var(--orange-warning);
            color: white;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-deactivate:hover {
            background: #e65100;
            transform: translateY(-1px);
        }

        /* Loading & Empty States */
        .loading {
            text-align: center;
            padding: 2rem;
            color: var(--text-mid);
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-mid);
            opacity: 0.6;
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--text-dark);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }

        .toast.success {
            background: var(--green-success);
        }

        .toast.error {
            background: var(--red-deep);
        }

        .toast.info {
            background: var(--blue-info);
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
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
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            th, td { padding: 0.5rem; font-size: 0.75rem; }
            .tab-btn { padding: 0.5rem 1rem; font-size: 0.8rem; }
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <!-- Sidebar -->
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
            <div class="nav-item"><a href="vape_incidents.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v4a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Vape Incidents</a></div>
            <div class="nav-item"><a href="air_quality.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 4a1 1 0 011-1h8a1 1 0 011 1v1a1 1 0 01-1 1H6a1 1 0 01-1-1V4zM4 9a1 1 0 011-1h10a1 1 0 011 1v1a1 1 0 01-1 1H5a1 1 0 01-1-1V9zM6 14a1 1 0 100-2h8a1 1 0 100 2H6z" clip-rule="evenodd"/></svg>Air Quality</a></div>
            <div class="nav-item"><a href="checklists.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Checklists</a></div>
            <div class="nav-item"><a href="maintenance_log.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>Maintenance Log</a></div>
            <div class="nav-item"><a href="alerts.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v4a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Alerts</a></div>
            <div class="nav-item"><a href="staff_management.php" class="nav-link active"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>Staff Management</a></div>
            <div class="nav-item"><a href="settings.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>Settings</a></div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h1>Staff Management</h1>
                <p>Approve or reject new account requests and manage existing staff, etc</p>
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

        <!-- Stats Overview -->
        <div class="stats-grid" id="statsContainer">
            <div class="stat-card">
                <div class="stat-value" id="pendingCount">-</div>
                <div class="stat-label">Pending Approvals</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="totalStaffCount">-</div>
                <div class="stat-label">Total Staff</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="supervisorCount">-</div>
                <div class="stat-label">Supervisors</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="maintenanceCount">-</div>
                <div class="stat-label">Maintenance Staff</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('pending', this)">Pending Approvals</button>
            <button class="tab-btn" onclick="switchTab('approved', this)">Approved Staff</button>
        </div>

        <!-- Pending Approvals Tab -->
        <div id="pendingTab" class="tab-content active">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Requested Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pendingTableBody">
                        <tr><td colspan="5" class="loading">Loading pending requests...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Approved Staff Tab -->
        <div id="approvedTab" class="tab-content">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="approvedTableBody">
                        <tr><td colspan="6" class="loading">Loading staff list...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div class="demo-badge" onclick="toggleDemoMode()">🧪 DEMO MODE | Staff Management (Demo Data)</div>

<script>
// =============================================
// CONFIGURATION
// =============================================
const USE_DEMO_MODE = true;
const API_BASE = '../../api/';

// =============================================
// DEMO DATA
// =============================================
let pendingRequests = [
    { id: 1, full_name: 'Juan D. Santos', username: 'juansantos', role: 'maintenance', requested_at: '2026-04-28 09:30:00' },
    { id: 2, full_name: 'Maria R. Reyes', username: 'mariareyes', role: 'maintenance', requested_at: '2026-04-28 08:15:00' },
    { id: 3, full_name: 'Carlos M. Lopez', username: 'carloslopez', role: 'supervisor', requested_at: '2026-04-27 14:20:00' },
    { id: 4, full_name: 'Ana P. Garcia', username: 'anagarcia', role: 'maintenance', requested_at: '2026-04-27 11:45:00' }
];

let approvedStaff = [
    { id: 1, full_name: 'Rey M. Santos', username: 'reysantos', role: 'maintenance', status: 'active', created_at: '2026-04-15 10:00:00' },
    { id: 2, full_name: 'Maria L. Cruz', username: 'mariacruz', role: 'maintenance', status: 'active', created_at: '2026-04-14 09:30:00' },
    { id: 3, full_name: 'John D. Reyes', username: 'johnreyes', role: 'maintenance', status: 'active', created_at: '2026-04-13 14:15:00' },
    { id: 4, full_name: 'Admin User', username: 'admin', role: 'supervisor', status: 'active', created_at: '2026-04-01 08:00:00' }
];

// =============================================
// INITIALIZATION
// =============================================
document.addEventListener('DOMContentLoaded', () => {
    loadPendingRequests();
    loadApprovedStaff();
});

function loadPendingRequests() {
    if (USE_DEMO_MODE) {
        displayPendingRequests(pendingRequests);
        updateStats();
    } else {
        fetch(API_BASE + 'get_pending_registrations.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayPendingRequests(data.requests);
                    updateStats(data.requests.length);
                }
            })
            .catch(error => console.error('Error:', error));
    }
}

function loadApprovedStaff() {
    if (USE_DEMO_MODE) {
        displayApprovedStaff(approvedStaff);
    } else {
        fetch(API_BASE + 'get_approved_staff.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayApprovedStaff(data.staff);
                }
            })
            .catch(error => console.error('Error:', error));
    }
}

// =============================================
// DISPLAY FUNCTIONS
// =============================================

function displayPendingRequests(requests) {
    const tbody = document.getElementById('pendingTableBody');
    
    if (!requests || requests.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="empty-state">No pending registration requests</td></tr>';
        return;
    }
    
    tbody.innerHTML = requests.map(req => `
        <tr>
            <td><strong>${escapeHtml(req.full_name)}</strong></td>
            <td>${escapeHtml(req.username)}</td>
            <td><span class="badge ${req.role === 'supervisor' ? 'badge-supervisor' : 'badge-maintenance'}">${escapeHtml(req.role)}</span></td>
            <td>${formatDate(req.requested_at)}</td>
            <td>
                <button class="btn-approve" onclick="approveUser(${req.id})">✓ Approve</button>
                <button class="btn-reject" onclick="rejectUser(${req.id})">✗ Reject</button>
            </td>
        </tr>
    `).join('');
}

function displayApprovedStaff(staff) {
    const tbody = document.getElementById('approvedTableBody');
    
    if (!staff || staff.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="empty-state">No staff members found</td></tr>';
        return;
    }
    
    tbody.innerHTML = staff.map(member => `
        <tr>
            <td><strong>${escapeHtml(member.full_name)}</strong></td>
            <td>${escapeHtml(member.username)}</td>
            <td><span class="badge ${member.role === 'supervisor' ? 'badge-supervisor' : 'badge-maintenance'}">${escapeHtml(member.role)}</span></td>
            <td><span class="badge ${member.status === 'active' ? 'badge-active' : 'badge-inactive'}">${escapeHtml(member.status)}</span></td>
            <td>${formatDate(member.created_at)}</td>
            <td>
                ${member.role !== 'supervisor' ? `<button class="btn-deactivate" onclick="deactivateUser(${member.id})">Deactivate</button>` : '<span class="badge badge-info">Admin</span>'}
            </td>
        </tr>
    `).join('');
}

// =============================================
// ACTION FUNCTIONS
// =============================================

function approveUser(requestId) {
    if (!confirm('Approve this user? They will be able to log in immediately.')) return;
    
    if (USE_DEMO_MODE) {
        const request = pendingRequests.find(r => r.id === requestId);
        if (request) {
            // Add to approved staff
            const newStaff = {
                id: approvedStaff.length + 1,
                full_name: request.full_name,
                username: request.username,
                role: request.role,
                status: 'active',
                created_at: new Date().toISOString()
            };
            approvedStaff.push(newStaff);
            
            // Remove from pending
            pendingRequests = pendingRequests.filter(r => r.id !== requestId);
            
            // Refresh displays
            displayPendingRequests(pendingRequests);
            displayApprovedStaff(approvedStaff);
            updateStats();
            
            showToast(`✓ ${request.full_name} has been approved!`, 'success');
        }
    } else {
        // API call would go here
        showToast('Approval functionality will connect to API', 'info');
    }
}

function rejectUser(requestId) {
    if (!confirm('Reject this registration request? The user will be notified.')) return;
    
    if (USE_DEMO_MODE) {
        const request = pendingRequests.find(r => r.id === requestId);
        if (request) {
            pendingRequests = pendingRequests.filter(r => r.id !== requestId);
            displayPendingRequests(pendingRequests);
            updateStats();
            showToast(`✗ Registration request for ${request.full_name} has been rejected.`, 'error');
        }
    } else {
        showToast('Rejection functionality will connect to API', 'info');
    }
}

function deactivateUser(userId) {
    if (!confirm('Deactivate this user? They will no longer be able to log in.')) return;
    
    if (USE_DEMO_MODE) {
        const user = approvedStaff.find(s => s.id === userId);
        if (user) {
            user.status = 'inactive';
            displayApprovedStaff(approvedStaff);
            updateStats();
            showToast(`⚠️ ${user.full_name} has been deactivated.`, 'info');
        }
    } else {
        showToast('Deactivation functionality will connect to API', 'info');
    }
}

// =============================================
// HELPER FUNCTIONS
// =============================================

function updateStats() {
    if (USE_DEMO_MODE) {
        document.getElementById('pendingCount').textContent = pendingRequests.length;
        document.getElementById('totalStaffCount').textContent = approvedStaff.length;
        document.getElementById('supervisorCount').textContent = approvedStaff.filter(s => s.role === 'supervisor').length;
        document.getElementById('maintenanceCount').textContent = approvedStaff.filter(s => s.role === 'maintenance' && s.status === 'active').length;
    }
}

function switchTab(tab, element) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    element.classList.add('active');
    
    // Update tab contents
    document.getElementById('pendingTab').classList.remove('active');
    document.getElementById('approvedTab').classList.remove('active');
    
    if (tab === 'pending') {
        document.getElementById('pendingTab').classList.add('active');
        loadPendingRequests();
    } else {
        document.getElementById('approvedTab').classList.add('active');
        loadApprovedStaff();
    }
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric'
    });
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
    if (confirm('Toggle between Demo and Live mode?\n\nCurrently in: DEMO MODE\n\nNote: Live mode would connect to:\n- api/get_pending_registrations.php\n- api/get_approved_staff.php\n- api/approve_user.php')) {
        alert('Switching to live mode would fetch real staff data from the database.');
    }
}
</script>
</body>
</html>