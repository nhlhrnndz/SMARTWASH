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
        .page-title h1 { font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 700; color: var(--text-dark); }
        .page-title p { font-size: 0.8rem; color: var(--text-mid); opacity: 0.7; }
        .top-bar-right { display: flex; align-items: center; gap: 1.5rem; }
        .logout-btn { background: none; border: 1px solid var(--gray-border); padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-size: 0.75rem; font-weight: 500; color: var(--text-mid); transition: all 0.2s; text-decoration: none; }
        .logout-btn:hover { background: var(--red-deep); border-color: var(--red-deep); color: var(--white); }
        .user-menu { display: flex; align-items: center; gap: 1rem; }
        .user-avatar { width: 44px; height: 44px; background: linear-gradient(135deg, var(--red-deep), var(--red-vivid)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 1.1rem; }
        .user-info { text-align: right; }
        .user-name { font-weight: 700; font-size: 0.88rem; }
        .user-role { font-size: 0.7rem; opacity: 0.6; }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .stat-card {
            background: var(--white);
            border-radius: 16px;
            padding: 1rem;
            border: 1px solid var(--gray-border);
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px var(--shadow); }
        .stat-value { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 900; color: var(--red-deep); }
        .stat-label { font-size: 0.7rem; color: var(--text-mid); opacity: 0.7; }

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
        .tab-btn:hover { color: var(--red-deep); }
        .tab-btn.active { color: var(--red-deep); }
        .tab-btn.active::after { transform: scaleX(1); }

        .tab-content { display: none; animation: fadeIn 0.3s ease; }
        .tab-content.active { display: block; }
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
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--gray-border); }
        th { background: var(--light); font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-mid); }
        tr:hover { background: rgba(197,0,0,0.02); }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-pending { background: #fff3e0; color: var(--orange-warning); }
        .badge-active { background: var(--green-soft); color: var(--green-success); }
        .badge-inactive { background: var(--red-soft); color: var(--red-deep); }
        .badge-maintenance { background: rgba(46,125,50,0.15); color: var(--green-success); }

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
        .btn-approve:hover { background: #1b5e20; transform: translateY(-1px); }
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
        .btn-reject:hover { background: #9b0000; transform: translateY(-1px); }
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
        .btn-deactivate:hover { background: #e65100; transform: translateY(-1px); }
        .btn-assign {
            background: var(--blue-info);
            color: white;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-assign:hover { background: #0d47a1; transform: translateY(-1px); }

        .empty-state { text-align: center; padding: 3rem; color: var(--text-mid); opacity: 0.6; }
        .loading { text-align: center; padding: 3rem; color: var(--text-mid); }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        .modal.active { display: flex; }
        .modal-content {
            background: var(--white);
            border-radius: 16px;
            width: 500px;
            max-width: 90%;
            padding: 1.5rem;
        }
        .modal-header { margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--gray-border); }
        .modal-header h3 { font-family: 'Playfair Display', serif; font-size: 1.2rem; }
        .modal-body { margin-bottom: 1rem; }
        .modal-body p { font-size: 0.85rem; color: var(--text-mid); margin-bottom: 1rem; }
        .restrooms-list { max-height: 300px; overflow-y: auto; border: 1px solid var(--gray-border); border-radius: 8px; padding: 0.5rem; }
        .restroom-checkbox-item { display: block; padding: 0.5rem; margin-bottom: 0.25rem; cursor: pointer; border-radius: 6px; }
        .restroom-checkbox-item:hover { background: var(--light); }
        .restroom-checkbox-item input { margin-right: 0.5rem; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem; padding-top: 0.5rem; border-top: 1px solid var(--gray-border); }
        .modal-btn { padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-size: 0.75rem; font-weight: 600; }
        .modal-btn-cancel { background: var(--light); border: 1px solid var(--gray-border); color: var(--text-mid); }
        .modal-btn-confirm { background: var(--red-deep); color: var(--white); border: none; }
        .modal-btn-confirm:hover { background: var(--red-vivid); }

        /* Toast */
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

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .main-content { margin-left: 0; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            th, td { padding: 0.5rem; font-size: 0.75rem; }
            .tab-btn { padding: 0.5rem 1rem; font-size: 0.8rem; }
        }

        /* Assigned Restrooms Tags */
.assigned-restrooms {
    display: flex;
    flex-wrap: wrap;
    gap: 0.3rem;
}
.restroom-tag {
    background: var(--light);
    color: var(--text-mid);
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 500;
    border: 1px solid var(--gray-border);
}
.no-assigned {
    color: var(--text-mid);
    opacity: 0.6;
    font-size: 0.7rem;
    font-style: italic;
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
            <div class="nav-item"><a href="air_quality.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 4a1 1 0 011-1h8a1 1 0 011 1v1a1 1 0 01-1 1H6a1 1 0 01-1-1V4zM4 9a1 1 0 011-1h10a1 1 0 011 1v1a1 1 0 01-1 1H5a1 1 0 01-1-1V9zM6 14a1 1 0 100-2h8a1 1 0 100 2H6z" clip-rule="evenodd"/></svg>Air Quality</a></div>
            <div class="nav-item"><a href="checklists.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Checklists</a></div>
            <div class="nav-item"><a href="maintenance_log.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>Maintenance Log</a></div>
            <div class="nav-item"><a href="alerts.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v4a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Alerts</a></div>
            <div class="nav-item"><a href="staff_management.php" class="nav-link active"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>Staff Management</a></div>
            <div class="nav-item"><a href="settings.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>Settings</a></div>
        </nav>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h1>Staff Management</h1>
                <p>Approve or reject new account requests and manage existing staff</p>
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

        <!-- Stats Overview -->
        <div class="stats-grid" id="statsContainer">
            <div class="stat-card"><div class="stat-value" id="pendingCount">-</div><div class="stat-label">Pending Approvals</div></div>
            <div class="stat-card"><div class="stat-value" id="totalStaffCount">-</div><div class="stat-label">Total Staff</div></div>
            <div class="stat-card"><div class="stat-value" id="maintenanceCount">-</div><div class="stat-label">Active Maintenance</div></div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn active" data-tab="pending">Pending Approvals</button>
            <button class="tab-btn" data-tab="approved">Approved Staff</button>
        </div>

        <!-- Pending Approvals Tab -->
        <div id="pendingTab" class="tab-content active">
            <div class="table-container">
                <table>
                    <thead>
                        <tr><th>Full Name</th><th>Username</th><th>Role</th><th>Requested Date</th><th>Actions</th></tr>
                    </thead>
                    <tbody id="pendingTableBody"><tr><td colspan="5" class="loading">Loading pending requests...</td></tr></tbody>
                </table>
            </div>
        </div>

        <!-- Approved Staff Tab -->
        <!-- Approved Staff Tab -->
<div id="approvedTab" class="tab-content">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Joined Date</th>
                    <th>Assigned Restrooms</th>
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

<!-- Assign Restrooms Modal (for approving maintenance staff) -->
<div id="assignRestroomsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header"><h3>Assign Restrooms to Staff</h3></div>
        <div class="modal-body">
            <p>Select the restrooms this maintenance staff will be responsible for:</p>
            <div id="assignRestroomsList" class="restrooms-list"><div class="loading">Loading restrooms...</div></div>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-cancel" onclick="closeAssignRestroomsModal()">Cancel</button>
            <button class="modal-btn modal-btn-confirm" onclick="submitWithAssignments()">Approve & Assign</button>
        </div>
    </div>
</div>

<script>
// =============================================
// CONFIGURATION
// =============================================
const API_BASE = '../../api/';
let currentRequestId = null;
let pendingRequestsData = [];

// =============================================
// TOAST NOTIFICATION
// =============================================
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// =============================================
// LOAD PENDING REQUESTS
// =============================================
async function loadPendingRequests() {
    const tbody = document.getElementById('pendingTableBody');
    tbody.innerHTML = '<tr><td colspan="5" class="loading">Loading pending requests...</td></tr>';
    
    try {
        const response = await fetch(API_BASE + 'get_pending_registrations.php');
        const result = await response.json();
        
        if (result.success && result.data && result.data.length > 0) {
            pendingRequestsData = result.data;
            tbody.innerHTML = result.data.map(req => `
                <tr>
                    <td><strong>${escapeHtml(req.full_name)}</strong></td>
                    <td>${escapeHtml(req.username)}</td>
                    <td><span class="badge badge-maintenance">${escapeHtml(req.role)}</span></td>
                    <td>${formatDate(req.requested_at)}</td>
                    <td>
                        <button class="btn-approve" onclick="processRequest(${req.id}, 'approve', '${req.role}')">✓ Approve</button>
                        <button class="btn-reject" onclick="processRequest(${req.id}, 'reject', '${req.role}')">✗ Reject</button>
                    </td>
                </tr>
            `).join('');
            document.getElementById('pendingCount').textContent = result.data.length;
        } else {
            tbody.innerHTML = '<tr><td colspan="5" class="empty-state">No pending registration requests</td></tr>';
            document.getElementById('pendingCount').textContent = '0';
        }
    } catch (error) {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="5" class="empty-state">Error loading data</td></tr>';
    }
}

async function loadApprovedStaff() {
    const tbody = document.getElementById('approvedTableBody');
    tbody.innerHTML = '<tr><td colspan="6" class="loading">Loading staff list...</td></tr>';
    
    try {
        const response = await fetch(API_BASE + 'get_approved_staff.php');
        const result = await response.json();
        
        console.log('Approved staff response:', result);
        
        if (result.success && result.data && result.data.length > 0) {
            const activeCount = result.data.filter(s => s.status === 'active').length;
            
            document.getElementById('totalStaffCount').textContent = result.data.length;
            document.getElementById('maintenanceCount').textContent = activeCount;
            
            tbody.innerHTML = result.data.map(member => `
                <tr>
                    <td><strong>${escapeHtml(member.full_name)}</strong></td>
                    <td>${escapeHtml(member.username)}</td>
                    <td><span class="badge ${member.status === 'active' ? 'badge-active' : 'badge-inactive'}">${escapeHtml(member.status)}</span></td>
                    <td>${member.joined_date || formatDate(member.created_at)}</td>
                    <td>
                        ${member.assigned_restrooms ? 
                            `<div class="assigned-restrooms">
                                ${member.assigned_restrooms.split(',').map(r => `<span class="restroom-tag">${escapeHtml(r.trim())}</span>`).join('')}
                             </div>` : 
                            '<span class="no-assigned">No restrooms assigned</span>'}
                    </td>
                    <td>
                        ${member.status === 'active' ? `<button class="btn-assign" onclick="openAssignModal(${member.id})">📋 Assign</button>` : ''}
                        ${member.status === 'active' ? `<button class="btn-deactivate" onclick="deactivateUser(${member.id})">⛔ Deactivate</button>` : ''}
                    </td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="empty-state">No maintenance staff found</td></tr>';
            document.getElementById('totalStaffCount').textContent = '0';
            document.getElementById('maintenanceCount').textContent = '0';
        }
    } catch (error) {
        console.error('Error loading staff:', error);
        tbody.innerHTML = '<tr><td colspan="6" class="empty-state">Error loading data</td></tr>';
    }
}

// =============================================
// PROCESS APPROVE/REJECT
// =============================================
function processRequest(requestId, action, role) {
    if (action === 'approve' && role === 'maintenance') {
        currentRequestId = requestId;
        loadRestroomsForAssignment();
        document.getElementById('assignRestroomsModal').classList.add('active');
    } else {
        confirmAction(requestId, action, []);
    }
}

async function confirmAction(requestId, action, assignedRestrooms) {
    if (!confirm(`Are you sure you want to ${action} this registration request?`)) return;
    
    try {
        const response = await fetch(API_BASE + 'approve_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ request_id: requestId, action: action, assigned_restrooms: assignedRestrooms })
        });
        const result = await response.json();
        
        if (result.success) {
            showToast(result.message, 'success');
            loadPendingRequests();
            loadApprovedStaff();
            closeAssignRestroomsModal();
        } else {
            showToast(result.error, 'error');
        }
    } catch (error) {
        showToast('Error processing request', 'error');
    }
}

async function loadRestroomsForAssignment() {
    try {
        const response = await fetch(API_BASE + 'get_restrooms_for_assign.php');
        const result = await response.json();
        
        if (result.success && result.data.length > 0) {
            const container = document.getElementById('assignRestroomsList');
            container.innerHTML = result.data.map(r => `
                <label class="restroom-checkbox-item">
                    <input type="checkbox" value="${r.id}" class="restroom-checkbox">
                    <strong>${escapeHtml(r.name)}</strong><br>
                    <span style="font-size: 0.75rem; color: var(--text-mid); margin-left: 1.5rem;">📍 ${escapeHtml(r.location)} | ${escapeHtml(r.gender)}</span>
                </label>
            `).join('');
        } else {
            document.getElementById('assignRestroomsList').innerHTML = '<div class="empty-state">No restrooms available</div>';
        }
    } catch (error) {
        console.error('Error loading restrooms:', error);
        document.getElementById('assignRestroomsList').innerHTML = '<div class="empty-state">Error loading restrooms</div>';
    }
}

function submitWithAssignments() {
    const checkboxes = document.querySelectorAll('#assignRestroomsList input:checked');
    const assignedRestrooms = Array.from(checkboxes).map(cb => parseInt(cb.value));
    
    if (assignedRestrooms.length === 0) {
        if (!confirm('No restrooms selected. Continue without assignment?')) return;
    }
    confirmAction(currentRequestId, 'approve', assignedRestrooms);
}

function closeAssignRestroomsModal() {
    document.getElementById('assignRestroomsModal').classList.remove('active');
    currentRequestId = null;
}

// =============================================
// ASSIGN RESTROOM TO EXISTING STAFF
// =============================================
let currentAssignUserId = null;

function openAssignModal(userId) {
    currentAssignUserId = userId;
    loadRestroomsForExistingStaff();
    document.getElementById('assignModal').classList.add('active');
}

async function loadRestroomsForExistingStaff() {
    try {
        const response = await fetch(API_BASE + 'get_restrooms_for_assign.php');
        const result = await response.json();
        
        if (result.success && result.data.length > 0) {
            const select = document.getElementById('assignRestroomSelect');
            select.innerHTML = '<option value="">-- Select Restroom --</option>' + 
                result.data.map(r => `<option value="${r.id}">${escapeHtml(r.name)} (${escapeHtml(r.location)})</option>`).join('');
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.remove('active');
    currentAssignUserId = null;
}

async function confirmAssign() {
    const restroomId = document.getElementById('assignRestroomSelect').value;
    if (!restroomId) {
        showToast('Please select a restroom', 'error');
        return;
    }
    
    try {
        const response = await fetch(API_BASE + 'assign_restroom.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: currentAssignUserId, restroom_id: restroomId })
        });
        const result = await response.json();
        
        if (result.success) {
            showToast(result.message, 'success');
            closeAssignModal();
            loadApprovedStaff(); // Refresh to show updated assignments
        } else {
            showToast(result.error, 'error');
        }
    } catch (error) {
        showToast('Error assigning restroom', 'error');
    }
}

// =============================================
// DEACTIVATE USER
// =============================================
async function deactivateUser(userId) {
    if (!confirm('Deactivate this user? They will no longer be able to log in.')) return;
    
    try {
        const response = await fetch(API_BASE + 'deactivate_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId })
        });
        const result = await response.json();
        
        if (result.success) {
            showToast(result.message, 'success');
            loadApprovedStaff();
        } else {
            showToast(result.error, 'error');
        }
    } catch (error) {
        showToast('Error deactivating user', 'error');
    }
}

// =============================================
// HELPER FUNCTIONS
// =============================================
function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

// =============================================
// TAB SWITCHING
// =============================================
function switchTab(tabId) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`.tab-btn[data-tab="${tabId}"]`).classList.add('active');
    
    document.getElementById('pendingTab').classList.remove('active');
    document.getElementById('approvedTab').classList.remove('active');
    
    if (tabId === 'pending') {
        document.getElementById('pendingTab').classList.add('active');
        loadPendingRequests();
    } else {
        document.getElementById('approvedTab').classList.add('active');
        loadApprovedStaff();
    }
}

// =============================================
// INITIALIZATION
// =============================================
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => switchTab(btn.getAttribute('data-tab')));
});

document.addEventListener('DOMContentLoaded', () => {
    loadPendingRequests();
    loadApprovedStaff();
});
</script>

<!-- Assign Modal for Existing Staff -->
<div id="assignModal" class="modal">
    <div class="modal-content">
        <div class="modal-header"><h3>Assign Restroom to Staff</h3></div>
        <div class="modal-body">
            <p>Select a restroom to assign to this staff member:</p>
            <select id="assignRestroomSelect" style="width: 100%; padding: 0.5rem; border-radius: 8px; border: 1px solid var(--gray-border);">
                <option value="">Loading...</option>
            </select>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-cancel" onclick="closeAssignModal()">Cancel</button>
            <button class="modal-btn modal-btn-confirm" onclick="confirmAssign()">Assign</button>
        </div>
    </div>
</div>
</body>
</html>