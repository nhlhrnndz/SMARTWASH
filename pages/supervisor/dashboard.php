<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard — SmartWash | BatStateU ARASOF-Nasugbu</title>
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

        /* Notification Bell */
        .notification-bell {
            position: relative;
            cursor: pointer;
        }

        .bell-icon {
            width: 40px;
            height: 40px;
            background: var(--light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .bell-icon:hover {
            background: rgba(197,0,0,0.1);
        }

        .bell-icon svg {
            width: 20px;
            height: 20px;
            color: var(--text-mid);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--red-deep);
            color: var(--white);
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 20px;
            min-width: 18px;
            text-align: center;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
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
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.2rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 16px;
            padding: 1.2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid var(--gray-border);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px var(--shadow);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.8rem;
        }

        .stat-header span {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-mid);
            opacity: 0.6;
        }

        .stat-icon {
            width: 32px;
            height: 32px;
            background: rgba(197,0,0,0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon svg {
            width: 16px;
            height: 16px;
            color: var(--red-deep);
        }

        .stat-value {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: 0.3rem;
        }

        .stat-label {
            font-size: 0.78rem;
            color: var(--text-mid);
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .dashboard-card {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid var(--gray-border);
            overflow: hidden;
        }

        .dashboard-card.full-width {
            grid-column: span 2;
        }

        .card-header {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--gray-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .card-header a {
            color: var(--red-deep);
            text-decoration: none;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .card-header a:hover {
            text-decoration: underline;
        }

        .card-body {
            padding: 1.2rem 1.5rem;
        }

        /* Alert List */
        .alert-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--gray-border);
        }

        .alert-item:last-child {
            border-bottom: none;
        }

        .alert-info {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .alert-badge {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .alert-badge.critical { background: var(--red-deep); box-shadow: 0 0 0 3px rgba(197,0,0,0.2); }
        .alert-badge.warning { background: var(--yellow-warning); }
        .alert-badge.info { background: #2196f3; }

        .alert-details {
            font-size: 0.85rem;
        }

        .alert-location {
            font-weight: 700;
        }

        .alert-type {
            font-size: 0.7rem;
            color: var(--text-mid);
            opacity: 0.7;
        }

        .alert-time {
            font-size: 0.7rem;
            color: var(--text-mid);
        }

        /* Restroom Grid */
        .restrooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 0.8rem;
        }

        .restroom-card {
            background: var(--light);
            border-radius: 12px;
            padding: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .restroom-card:hover {
            border-color: var(--red-deep);
            transform: translateY(-2px);
        }

        .restroom-card.critical {
            background: rgba(197,0,0,0.08);
            border-color: rgba(197,0,0,0.3);
        }

        .restroom-card.warning {
            background: rgba(249,168,37,0.08);
            border-color: rgba(249,168,37,0.3);
        }

        .restroom-name {
            font-weight: 700;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .restroom-stats {
            display: flex;
            gap: 0.8rem;
            font-size: 0.65rem;
        }

        .stat-indicator {
            display: flex;
            align-items: center;
            gap: 0.2rem;
        }

        .stat-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .stat-dot.good { background: var(--green-success); }
        .stat-dot.warning { background: var(--yellow-warning); }
        .stat-dot.critical { background: var(--red-deep); }

        /* Incident List */
        .incident-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.7rem 0;
            border-bottom: 1px solid var(--gray-border);
        }

        .incident-type {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .incident-badge {
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .incident-badge.vape { background: rgba(197,0,0,0.15); color: var(--red-deep); }
        .incident-badge.smoke { background: rgba(245,124,0,0.15); color: var(--orange-critical); }

        /* Checklist Item */
        .checklist-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--gray-border);
        }

        .checklist-info {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .checklist-rating {
            background: var(--yellow-warning);
            color: var(--white);
            padding: 0.2rem 0.5rem;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .checklist-actions button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.3rem;
            margin-left: 0.3rem;
            font-size: 1rem;
        }

        .btn-approve { color: var(--green-success); }
        .btn-flag { color: var(--orange-critical); }

        /* Maintenance Log */
        .log-item {
            padding: 0.6rem 0;
            border-bottom: 1px solid var(--gray-border);
            font-size: 0.8rem;
        }

        .log-time {
            font-size: 0.65rem;
            color: var(--text-mid);
            opacity: 0.6;
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

        /* Demo Mode Badge */
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
            transition: opacity 0.2s;
        }

        .demo-badge:hover {
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            .dashboard-card.full-width {
                grid-column: span 1;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .logout-btn:hover {
    background: var(--red-deep);
    color: var(--white);
}
.logout-btn:hover svg {
    stroke: var(--white);
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
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link active">
                    <svg viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    Dashboard
                </a>
            </div>
            <div class="nav-item"><a href="live_monitoring.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>Live Monitoring</a></div>
            <div class="nav-item"><a href="vape_incidents.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6.672 1.911a1 1 0 10-1.932.518l.259.966a1 1 0 001.932-.518l-.26-.966zM2.429 4.74a1 1 0 10-.518 1.932l.966.259a1 1 0 00.518-1.932l-.966-.26zm8.814 3.748a1 1 0 00-1.414-1.414L6.5 10.1l-1.5-1.5a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/><path d="M16 15.5a2.5 2.5 0 00-5 0v1a2.5 2.5 0 005 0v-1z"/></svg>Vape Incidents</a></div>
            <div class="nav-item"><a href="air_quality.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 4a1 1 0 011-1h8a1 1 0 011 1v1a1 1 0 01-1 1H6a1 1 0 01-1-1V4zM4 9a1 1 0 011-1h10a1 1 0 011 1v1a1 1 0 01-1 1H5a1 1 0 01-1-1V9zM6 14a1 1 0 100-2h8a1 1 0 100 2H6z" clip-rule="evenodd"/></svg>Air Quality</a></div>
            <div class="nav-item"><a href="checklists.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Checklists</a></div>
            <div class="nav-item"><a href="maintenance_log.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>Maintenance Log</a></div>
            <div class="nav-item"><a href="alerts.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Alerts</a></div>
            <div class="nav-item"><a href="staff_management.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>Staff Management</a></div>
            <div class="nav-item"><a href="settings.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>Settings</a></div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h1>Supervisor Dashboard</h1>
                <p>Overview of all restroom facilities and real-time status</p>
            </div>
<div class="top-bar-right">
    <div class="notification-bell" id="notificationBell">
        <div class="bell-icon">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
            </svg>
        </div>
        <span class="notification-badge" id="notificationCount">3</span>
    </div>
    <div class="user-menu">
        <div class="user-info">
            <div class="user-name">John M. Dela Cruz</div>
            <div class="user-role">Supervisor</div>
        </div>
        <div class="user-avatar">JD</div>
    </div>
    <a href="../../auth/logout.php" class="logout-btn" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: var(--light); border-radius: 8px; color: var(--text-mid); text-decoration: none; transition: all 0.2s;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
        </svg>
        Logout
    </a>
</div>
    </div>      <!-- Closes top-bar - ADD THIS LINE! -->

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span>Total Facilities</span>
                    <div class="stat-icon"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg></div>
                </div>
                <div class="stat-value" id="totalRestrooms">8</div>
                <div class="stat-label">Active Restrooms</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span>Active Alerts</span>
                    <div class="stat-icon"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg></div>
                </div>
                <div class="stat-value" id="activeAlerts">4</div>
                <div class="stat-label">Need Attention</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span>Pending Review</span>
                    <div class="stat-icon"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg></div>
                </div>
                <div class="stat-value" id="pendingChecklists">3</div>
                <div class="stat-label">Checklists to Review</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span>Today's Incidents</span>
                    <div class="stat-icon"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6.672 1.911a1 1 0 10-1.932.518l.259.966a1 1 0 001.932-.518l-.26-.966zM2.429 4.74a1 1 0 10-.518 1.932l.966.259a1 1 0 00.518-1.932l-.966-.26zm8.814 3.748a1 1 0 00-1.414-1.414L6.5 10.1l-1.5-1.5a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/><path d="M16 15.5a2.5 2.5 0 00-5 0v1a2.5 2.5 0 005 0v-1z"/></svg></div>
                </div>
                <div class="stat-value" id="todayIncidents">2</div>
                <div class="stat-label">Vape/Smoke Events</div>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Active Alerts Section -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>🔴 Active Alerts</h3>
                    <a href="alerts.php">View All →</a>
                </div>
                <div class="card-body" id="activeAlertsList">
                    <!-- Static demo data will be replaced by JS -->
                </div>
            </div>

            <!-- Restroom Status Grid -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>🚻 Restroom Status</h3>
                    <a href="live-monitoring.php">View All →</a>
                </div>
                <div class="card-body" id="restroomStatusGrid">
                    <!-- Static demo data will be replaced by JS -->
                </div>
            </div>

            <!-- Vape/Smoke Incidents Timeline -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>🚭 Recent Incidents</h3>
                    <a href="vape-incidents.php">View All →</a>
                </div>
                <div class="card-body" id="incidentsList">
                    <!-- Static demo data will be replaced by JS -->
                </div>
            </div>

            <!-- Air Quality Summary -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>🌬️ Air Quality Summary</h3>
                    <a href="air-quality.php">View Report →</a>
                </div>
                <div class="card-body">
                    <canvas id="airQualityChart" style="max-height: 200px; width: 100%;"></canvas>
                    <div id="airQualitySummary"></div>
                </div>
            </div>

            <!-- Pending Checklists -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>✅ Pending Checklists</h3>
                    <a href="checklists.php">Review All →</a>
                </div>
                <div class="card-body" id="pendingChecklistsList">
                    <!-- Static demo data will be replaced by JS -->
                </div>
            </div>

            <!-- Recent Maintenance Log -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>📋 Recent Maintenance Log</h3>
                    <a href="maintenance-log.php">View Full Log →</a>
                </div>
                <div class="card-body" id="maintenanceLogList">
                    <!-- Static demo data will be replaced by JS -->
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Demo Mode Indicator -->
<div class="demo-badge" onclick="toggleDemoMode()" title="Click to toggle between Demo and Live mode">
    🧪 DEMO MODE | Using Sample Data
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// =============================================
// CONFIGURATION
// =============================================
// Set to false when database is ready to use real API endpoints
const USE_DEMO_MODE = true;

// API endpoints (will be used when USE_DEMO_MODE = false)
const API_BASE = '../../api/';

// =============================================
// DEMO DATA (for static preview)
// =============================================
const DEMO_DATA = {
    activeAlerts: [
        { id: 1, restroom_name: 'GLR1 - Ground Left', type: 'Soap Low', severity: 'warning', created_at: '2024-01-15 09:30:00' },
        { id: 2, restroom_name: 'GLR2 - Ground Right', type: 'Vape Detected', severity: 'critical', created_at: '2024-01-15 10:15:00' },
        { id: 3, restroom_name: '2F-MR - 2nd Floor Main', type: 'Air Quality Poor', severity: 'critical', created_at: '2024-01-15 10:45:00' },
        { id: 4, restroom_name: '3F-CR - 3rd Floor CR', type: 'Smoke Detected', severity: 'critical', created_at: '2024-01-15 11:00:00' },
        { id: 5, restroom_name: '1F-FR - 1st Floor Front', type: 'Soap Low', severity: 'warning', created_at: '2024-01-15 08:20:00' }
    ],
    
    restrooms: [
        { id: 1, name: 'GLR1 - Ground Left', soap_level: 85, air_quality: 32, smoke_detected: false, vape_detected: false, has_critical: false, has_warning: false },
        { id: 2, name: 'GLR2 - Ground Right', soap_level: 45, air_quality: 28, smoke_detected: false, vape_detected: true, has_critical: true, has_warning: false },
        { id: 3, name: '1F-FR - 1st Floor Front', soap_level: 12, air_quality: 55, smoke_detected: false, vape_detected: false, has_critical: false, has_warning: true },
        { id: 4, name: '1F-RR - 1st Floor Rear', soap_level: 92, air_quality: 25, smoke_detected: false, vape_detected: false, has_critical: false, has_warning: false },
        { id: 5, name: '2F-MR - 2nd Floor Main', soap_level: 67, air_quality: 85, smoke_detected: false, vape_detected: false, has_critical: true, has_warning: false },
        { id: 6, name: '2F-LR - 2nd Floor Left', soap_level: 78, air_quality: 42, smoke_detected: false, vape_detected: false, has_critical: false, has_warning: true },
        { id: 7, name: '3F-CR - 3rd Floor CR', soap_level: 95, air_quality: 18, smoke_detected: true, vape_detected: false, has_critical: true, has_warning: false },
        { id: 8, name: '3F-NR - 3rd Floor North', soap_level: 88, air_quality: 35, smoke_detected: false, vape_detected: false, has_critical: false, has_warning: false }
    ],
    
    incidents: [
        { id: 1, type: 'vape', restroom_name: 'GLR2 - Ground Right', timestamp: '2024-01-15 10:15:00' },
        { id: 2, type: 'smoke', restroom_name: '3F-CR - 3rd Floor CR', timestamp: '2024-01-15 09:45:00' },
        { id: 3, type: 'vape', restroom_name: '2F-MR - 2nd Floor Main', timestamp: '2024-01-14 16:20:00' },
        { id: 4, type: 'vape', restroom_name: 'GLR1 - Ground Left', timestamp: '2024-01-14 14:30:00' },
        { id: 5, type: 'smoke', restroom_name: '1F-RR - 1st Floor Rear', timestamp: '2024-01-14 11:15:00' }
    ],
    
    checklists: [
        { id: 1, restroom_name: 'GLR1 - Ground Left', staff_name: 'Rey M. Santos', rating: 5, submitted_at: '2024-01-15 09:00:00' },
        { id: 2, restroom_name: 'GLR2 - Ground Right', staff_name: 'Maria L. Cruz', rating: 3, submitted_at: '2024-01-15 09:30:00' },
        { id: 3, restroom_name: '1F-FR - 1st Floor Front', staff_name: 'Rey M. Santos', rating: 4, submitted_at: '2024-01-15 10:00:00' }
    ],
    
    maintenanceLogs: [
        { id: 1, action: 'Soap refilled', restroom_name: '1F-FR - 1st Floor Front', staff_name: 'Rey M. Santos', timestamp: '2024-01-15 09:15:00' },
        { id: 2, action: 'Air freshener triggered', restroom_name: '2F-MR - 2nd Floor Main', staff_name: 'System Auto', timestamp: '2024-01-15 08:45:00' },
        { id: 3, action: 'Vape incident reported', restroom_name: 'GLR2 - Ground Right', staff_name: 'Sensor', timestamp: '2024-01-15 10:15:00' },
        { id: 4, action: 'Cleaned and sanitized', restroom_name: '3F-CR - 3rd Floor CR', staff_name: 'Maria L. Cruz', timestamp: '2024-01-15 08:00:00' },
        { id: 5, action: 'Soap refilled', restroom_name: 'GLR1 - Ground Left', staff_name: 'Rey M. Santos', timestamp: '2024-01-15 07:30:00' }
    ],
    
    airQualityReadings: {
        labels: ['00:00', '02:00', '04:00', '06:00', '08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'],
        values: [28, 25, 22, 30, 45, 68, 72, 65, 58, 42, 35, 30]
    }
};

// =============================================
// FETCH FUNCTIONS (Will connect to real API when DB is ready)
// =============================================

async function fetchActiveAlerts() {
    if (USE_DEMO_MODE) {
        displayActiveAlerts(DEMO_DATA.activeAlerts);
        return;
    }
    
    try {
        const response = await fetch(API_BASE + 'get_alerts.php?status=active&limit=5');
        const data = await response.json();
        displayActiveAlerts(data.alerts || []);
    } catch (error) {
        console.error('Error fetching alerts:', error);
        displayActiveAlerts([]);
    }
}

async function fetchRestroomStatus() {
    if (USE_DEMO_MODE) {
        displayRestroomStatus(DEMO_DATA.restrooms);
        return;
    }
    
    try {
        const response = await fetch(API_BASE + 'get_sensors.php?all_restrooms=1');
        const data = await response.json();
        displayRestroomStatus(data.restrooms || []);
    } catch (error) {
        console.error('Error fetching restroom status:', error);
        displayRestroomStatus([]);
    }
}

async function fetchIncidents() {
    if (USE_DEMO_MODE) {
        displayIncidents(DEMO_DATA.incidents);
        return;
    }
    
    try {
        const response = await fetch(API_BASE + 'get_incidents.php?days=7&limit=5');
        const data = await response.json();
        displayIncidents(data.incidents || []);
    } catch (error) {
        console.error('Error fetching incidents:', error);
        displayIncidents([]);
    }
}

async function fetchPendingChecklists() {
    if (USE_DEMO_MODE) {
        displayPendingChecklists(DEMO_DATA.checklists);
        return;
    }
    
    try {
        const response = await fetch(API_BASE + 'get_checklists.php?status=pending&limit=5');
        const data = await response.json();
        displayPendingChecklists(data.checklists || []);
    } catch (error) {
        console.error('Error fetching checklists:', error);
        displayPendingChecklists([]);
    }
}

async function fetchMaintenanceLogs() {
    if (USE_DEMO_MODE) {
        displayMaintenanceLogs(DEMO_DATA.maintenanceLogs);
        return;
    }
    
    try {
        const response = await fetch(API_BASE + 'get_logs.php?limit=5');
        const data = await response.json();
        displayMaintenanceLogs(data.logs || []);
    } catch (error) {
        console.error('Error fetching logs:', error);
        displayMaintenanceLogs([]);
    }
}

async function fetchAirQualityChart() {
    if (USE_DEMO_MODE) {
        displayAirQualityChart(DEMO_DATA.airQualityReadings);
        return;
    }
    
    try {
        const response = await fetch(API_BASE + 'get_air_quality.php?hours=24');
        const data = await response.json();
        if (data.readings && data.readings.length > 0) {
            displayAirQualityChart({
                labels: data.readings.map(r => r.hour),
                values: data.readings.map(r => r.aqi)
            });
        } else {
            displayAirQualityChart(DEMO_DATA.airQualityReadings);
        }
    } catch (error) {
        console.error('Error fetching air quality:', error);
        displayAirQualityChart(DEMO_DATA.airQualityReadings);
    }
}

// =============================================
// DISPLAY FUNCTIONS
// =============================================

function displayActiveAlerts(alerts) {
    const container = document.getElementById('activeAlertsList');
    
    if (!alerts || alerts.length === 0) {
        container.innerHTML = '<div class="empty-state">✅ No active alerts</div>';
        return;
    }
    
    container.innerHTML = alerts.slice(0, 5).map(alert => `
        <div class="alert-item">
            <div class="alert-info">
                <div class="alert-badge ${alert.severity}"></div>
                <div class="alert-details">
                    <div class="alert-location">${escapeHtml(alert.restroom_name)}</div>
                    <div class="alert-type">${escapeHtml(alert.type)}</div>
                </div>
            </div>
            <div class="alert-time">${formatTime(alert.created_at)}</div>
        </div>
    `).join('');
}

function displayRestroomStatus(restrooms) {
    const container = document.getElementById('restroomStatusGrid');
    
    if (!restrooms || restrooms.length === 0) {
        container.innerHTML = '<div class="empty-state">No restrooms found</div>';
        return;
    }
    
    container.innerHTML = `
        <div class="restrooms-grid">
            ${restrooms.map(restroom => {
                const statusClass = restroom.has_critical ? 'critical' : (restroom.has_warning ? 'warning' : '');
                return `
                    <div class="restroom-card ${statusClass}" onclick="location.href='live-monitoring.php?restroom_id=${restroom.id}'">
                        <div class="restroom-name">${escapeHtml(restroom.name)}</div>
                        <div class="restroom-stats">
                            <div class="stat-indicator">
                                <div class="stat-dot ${getSoapStatus(restroom.soap_level)}"></div>
                                <span>🧴 ${restroom.soap_level}%</span>
                            </div>
                            <div class="stat-indicator">
                                <div class="stat-dot ${getAirStatus(restroom.air_quality)}"></div>
                                <span>🌬️ ${restroom.air_quality}</span>
                            </div>
                            ${restroom.vape_detected ? '<span class="stat-indicator" style="color: var(--red-deep);">🚭 VAPE</span>' : ''}
                            ${restroom.smoke_detected ? '<span class="stat-indicator" style="color: var(--orange-critical);">🔥 SMOKE</span>' : ''}
                        </div>
                    </div>
                `;
            }).join('')}
        </div>
    `;
}

function displayIncidents(incidents) {
    const container = document.getElementById('incidentsList');
    
    if (!incidents || incidents.length === 0) {
        container.innerHTML = '<div class="empty-state">No recent incidents</div>';
        return;
    }
    
    container.innerHTML = incidents.slice(0, 5).map(incident => `
        <div class="incident-item">
            <div class="incident-type">
                <span class="incident-badge ${incident.type}">${incident.type === 'vape' ? '🚭 VAPE' : '🔥 SMOKE'}</span>
                <span>${escapeHtml(incident.restroom_name)}</span>
            </div>
            <div class="alert-time">${formatTime(incident.timestamp)}</div>
        </div>
    `).join('');
}

function displayPendingChecklists(checklists) {
    const container = document.getElementById('pendingChecklistsList');
    
    if (!checklists || checklists.length === 0) {
        container.innerHTML = '<div class="empty-state">No pending checklists</div>';
        return;
    }
    
    container.innerHTML = checklists.map(checklist => `
        <div class="checklist-item">
            <div class="checklist-info">
                <span class="checklist-rating">${checklist.rating}/5</span>
                <div>
                    <div><strong>${escapeHtml(checklist.restroom_name)}</strong></div>
                    <div class="alert-type">by ${escapeHtml(checklist.staff_name)}</div>
                </div>
            </div>
            <div class="checklist-actions">
                <button class="btn-approve" onclick="approveChecklist(${checklist.id})" title="Approve">✅</button>
                <button class="btn-flag" onclick="flagChecklist(${checklist.id})" title="Flag for review">⚠️</button>
            </div>
        </div>
    `).join('');
}

function displayMaintenanceLogs(logs) {
    const container = document.getElementById('maintenanceLogList');
    
    if (!logs || logs.length === 0) {
        container.innerHTML = '<div class="empty-state">No recent maintenance logs</div>';
        return;
    }
    
    container.innerHTML = logs.slice(0, 5).map(log => `
        <div class="log-item">
            <div><strong>${escapeHtml(log.action)}</strong> - ${escapeHtml(log.restroom_name)}</div>
            <div class="log-time">by ${escapeHtml(log.staff_name)} • ${formatTime(log.timestamp)}</div>
        </div>
    `).join('');
}

let airQualityChart = null;

function displayAirQualityChart(data) {
    const ctx = document.getElementById('airQualityChart').getContext('2d');
    
    if (airQualityChart) {
        airQualityChart.destroy();
    }
    
    airQualityChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Air Quality Index',
                data: data.values,
                borderColor: '#C50000',
                backgroundColor: 'rgba(197, 0, 0, 0.1)',
                tension: 0.3,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#C50000',
                pointBorderColor: '#FFFFFF',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: { 
                    callbacks: { 
                        label: (ctx) => `AQI: ${ctx.raw}` 
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    title: { display: true, text: 'AQI', font: { size: 10 } },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: { 
                    title: { display: true, text: 'Time (last 24h)', font: { size: 10 } },
                    ticks: { maxRotation: 45, minRotation: 45 }
                }
            }
        }
    });
    
    const avgAQI = (data.values.reduce((a, b) => a + b, 0) / data.values.length).toFixed(0);
    document.getElementById('airQualitySummary').innerHTML = `
        <div class="alert-item" style="margin-top: 10px; justify-content: space-between;">
            <div>24h Average AQI: <strong>${avgAQI}</strong></div>
            <div class="alert-time">${avgAQI > 70 ? '⚠️ Poor Air Quality' : (avgAQI > 40 ? '🟡 Moderate' : '✅ Good')}</div>
        </div>
    `;
}

// =============================================
// ACTION FUNCTIONS
// =============================================

async function approveChecklist(id) {
    if (USE_DEMO_MODE) {
        alert(`[DEMO] Checklist #${id} approved. In live mode, this would update the database.`);
        // Remove from UI in demo mode
        const container = document.getElementById('pendingChecklistsList');
        const currentHTML = container.innerHTML;
        // Simple demo removal (in real app, would refresh from API)
        fetchPendingChecklists();
        return;
    }
    
    try {
        const response = await fetch(API_BASE + 'review_checklist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id, action: 'approve' })
        });
        if (response.ok) {
            fetchPendingChecklists();
        }
    } catch (error) {
        console.error('Error approving checklist:', error);
    }
}

async function flagChecklist(id) {
    if (USE_DEMO_MODE) {
        alert(`[DEMO] Checklist #${id} flagged. In live mode, this would update the database.`);
        fetchPendingChecklists();
        return;
    }
    
    try {
        const response = await fetch(API_BASE + 'review_checklist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id, action: 'flag' })
        });
        if (response.ok) {
            fetchPendingChecklists();
        }
    } catch (error) {
        console.error('Error flagging checklist:', error);
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

function formatTime(timestamp) {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    
    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins} min ago`;
    if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
    return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
}

function getSoapStatus(level) {
    if (!level) return 'critical';
    if (level < 20) return 'critical';
    if (level < 50) return 'warning';
    return 'good';
}

function getAirStatus(aqi) {
    if (!aqi) return 'good';
    if (aqi > 70) return 'critical';
    if (aqi > 40) return 'warning';
    return 'good';
}

function toggleDemoMode() {
    const badge = document.querySelector('.demo-badge');
    if (confirm('Toggle between Demo and Live mode?\n\nCurrently in: ' + (USE_DEMO_MODE ? 'DEMO MODE' : 'LIVE MODE') + '\n\nNote: Live mode requires API endpoints to be implemented.')) {
        window.location.reload();
    }
}

// =============================================
// AUTO-REFRESH & INITIALIZATION
// =============================================

let refreshInterval;

function initDashboard() {
    // Load all data
    fetchActiveAlerts();
    fetchRestroomStatus();
    fetchIncidents();
    fetchPendingChecklists();
    fetchMaintenanceLogs();
    fetchAirQualityChart();
    updateStatsFromData();
    
    // Auto-refresh every 30 seconds (only active alerts and restroom status)
    if (!USE_DEMO_MODE) {
        refreshInterval = setInterval(() => {
            fetchActiveAlerts();
            fetchRestroomStatus();
            fetchIncidents();
        }, 30000);
    }
}

function updateStatsFromData() {
    // Update stat card numbers with demo data
    if (USE_DEMO_MODE) {
        document.getElementById('activeAlerts').textContent = DEMO_DATA.activeAlerts.length;
        document.getElementById('pendingChecklists').textContent = DEMO_DATA.checklists.length;
        document.getElementById('todayIncidents').textContent = DEMO_DATA.incidents.filter(i => {
            const today = new Date().toDateString();
            const incidentDate = new Date(i.timestamp).toDateString();
            return today === incidentDate;
        }).length;
    }
}

// Start dashboard when page loads
document.addEventListener('DOMContentLoaded', () => {
    initDashboard();
    
    // Notification bell click handler
    document.getElementById('notificationBell').addEventListener('click', () => {
        if (USE_DEMO_MODE) {
            alert('[DEMO] You have 3 unread notifications.\n\nIn live mode, this would show actual notifications.');
        } else {
            window.location.href = 'notifications.php';
        }
    });
});
</script>
</body>
</html> 