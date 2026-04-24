<?php
// ============================================================
//  SmartWash — Maintenance Home Page
//  File: pages/maintenance/home.php
// ============================================================

// Require session guard - this ensures only logged-in users can access
require_once '../../auth/session.php';

// Only maintenance staff can access this page
requireRole(['maintenance']);

// Get current user info
$user = currentUser();
$fullName = $user['full_name'];
$username = $user['username'];

// Get user initials for avatar
$nameParts = explode(' ', $fullName);
$initials = '';
foreach ($nameParts as $part) {
    if (strlen($part) > 0 && strlen($initials) < 2) {
        $initials .= strtoupper($part[0]);
    }
}
if (strlen($initials) < 2 && strlen($fullName) > 0) {
    $initials = strtoupper(substr($fullName, 0, 2));
}

// Get current time greeting
$hour = date('H');
$greeting = '';
if ($hour < 12) {
    $greeting = 'Good morning';
} elseif ($hour < 18) {
    $greeting = 'Good afternoon';
} else {
    $greeting = 'Good evening';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Home — SmartWash | BatStateU ARASOF-Nasugbu</title>
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

        .logout-btn {
            background: none;
            border: 1px solid var(--gray-border);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-mid);
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: var(--red-deep);
            border-color: var(--red-deep);
            color: var(--white);
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

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, var(--red-deep), var(--red-vivid));
            border-radius: 20px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            color: var(--white);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .welcome-text h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            margin-bottom: 0.3rem;
        }

        .welcome-text p {
            opacity: 0.9;
            font-size: 0.85rem;
        }

        .welcome-date {
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Stats Grid */
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid var(--gray-border);
            cursor: pointer;
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
            font-size: 1.8rem;
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
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--gray-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            font-weight: 700;
        }

        .card-header a {
            color: var(--red-deep);
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1rem 1.5rem;
        }

        /* Task Items */
        .task-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--gray-border);
        }

        .task-item:last-child {
            border-bottom: none;
        }

        .task-info {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex: 1;
        }

        .task-priority {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .task-priority.high { background: var(--red-deep); box-shadow: 0 0 0 3px rgba(197,0,0,0.2); }
        .task-priority.medium { background: var(--yellow-warning); }
        .task-priority.low { background: var(--blue-info); }

        .task-details {
            flex: 1;
        }

        .task-title {
            font-weight: 600;
            font-size: 0.85rem;
        }

        .task-location {
            font-size: 0.7rem;
            color: var(--text-mid);
            opacity: 0.7;
        }

        .task-time {
            font-size: 0.7rem;
            color: var(--text-mid);
        }

        .task-action {
            background: var(--light);
            border: none;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .task-action:hover {
            background: var(--red-deep);
            color: var(--white);
        }

        /* Assigned Restrooms Grid */
        .restrooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
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

        .restroom-card.alert {
            background: rgba(197,0,0,0.08);
            border-color: rgba(197,0,0,0.3);
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

        .restroom-action-btn {
            margin-top: 0.6rem;
            width: 100%;
            background: var(--white);
            border: 1px solid var(--gray-border);
            padding: 0.3rem;
            border-radius: 8px;
            font-size: 0.65rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .restroom-action-btn:hover {
            background: var(--red-deep);
            color: var(--white);
            border-color: var(--red-deep);
        }

        /* Checklist Item */
        .checklist-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--gray-border);
        }

        .checklist-info {
            flex: 1;
        }

        .checklist-restroom {
            font-weight: 600;
            font-size: 0.85rem;
        }

        .checklist-due {
            font-size: 0.65rem;
            color: var(--text-mid);
            opacity: 0.7;
        }

        .checklist-status {
            font-size: 0.65rem;
            padding: 0.2rem 0.5rem;
            border-radius: 20px;
        }

        .checklist-status.pending {
            background: rgba(249,168,37,0.15);
            color: var(--yellow-warning);
        }

        .checklist-status.completed {
            background: rgba(46,125,50,0.15);
            color: var(--green-success);
        }

        .btn-checklist {
            background: var(--red-deep);
            color: var(--white);
            border: none;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-checklist:hover {
            background: var(--red-vivid);
            transform: scale(1.02);
        }

        /* Activity Log */
        .activity-item {
            padding: 0.6rem 0;
            border-bottom: 1px solid var(--gray-border);
            font-size: 0.8rem;
        }

        .activity-time {
            font-size: 0.65rem;
            color: var(--text-mid);
            opacity: 0.6;
        }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .quick-btn {
            flex: 1;
            background: var(--light);
            border: 1px solid var(--gray-border);
            padding: 0.8rem;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .quick-btn:hover {
            background: var(--red-deep);
            border-color: var(--red-deep);
            color: var(--white);
        }

        .quick-btn svg {
            width: 20px;
            height: 20px;
            margin-bottom: 0.3rem;
            color: var(--text-mid);
        }

        .quick-btn:hover svg {
            color: var(--white);
        }

        .quick-btn span {
            display: block;
            font-size: 0.7rem;
            font-weight: 500;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 1.5rem;
            color: var(--text-mid);
            opacity: 0.6;
            font-size: 0.8rem;
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
            .quick-actions {
                flex-direction: column;
            }
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
                <a href="home.php" class="nav-link active">
                    <svg viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    Home
                </a>
            </div>
            <div class="nav-item"><a href="restroom_status.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>My Restrooms</a></div>
            <div class="nav-item"><a href="checklist.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Checklist</a></div>
            <div class="nav-item"><a href="submissions.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>My Submissions</a></div>
            <div class="nav-item"><a href="notifications.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>Notifications</a></div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h1>Maintenance Dashboard</h1>
                <p>Your assigned restrooms and daily tasks</p>
            </div>
            <div class="top-bar-right">
                <div class="notification-bell" id="notificationBell">
                    <div class="bell-icon">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                        </svg>
                    </div>
                    <span class="notification-badge" id="notificationCount">0</span>
                </div>
                <form method="POST" action="../../auth/logout.php" style="margin:0;">
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-name" id="userName"><?php echo htmlspecialchars($fullName); ?></div>
                        <div class="user-role">Maintenance Staff</div>
                    </div>
                    <div class="user-avatar" id="userAvatar"><?php echo htmlspecialchars($initials); ?></div>
                </div>
            </div>
        </div>

        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <div class="welcome-text">
                <h2><?php echo $greeting; ?>, <?php echo htmlspecialchars(explode(' ', $fullName)[0]); ?>! 👋</h2>
                <p>You have <strong id="taskCountSummary">0</strong> tasks that need your attention today.</p>
            </div>
            <div class="welcome-date" id="currentDate"></div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card" onclick="location.href='restroom_status.php'">
                <div class="stat-header">
                    <span>My Restrooms</span>
                    <div class="stat-icon"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg></div>
                </div>
                <div class="stat-value" id="assignedCount">0</div>
                <div class="stat-label">Restrooms Assigned</div>
            </div>
            <div class="stat-card" onclick="location.href='notifications.php'">
                <div class="stat-header">
                    <span>Pending Tasks</span>
                    <div class="stat-icon"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg></div>
                </div>
                <div class="stat-value" id="pendingTasks">0</div>
                <div class="stat-label">Need Action</div>
            </div>
            <div class="stat-card" onclick="location.href='submissions.php'">
                <div class="stat-header">
                    <span>Completed Today</span>
                    <div class="stat-icon"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></div>
                </div>
                <div class="stat-value" id="completedToday">0</div>
                <div class="stat-label">Tasks Completed</div>
            </div>
            <div class="stat-card" onclick="location.href='checklist.php'">
                <div class="stat-header">
                    <span>Pending Checklists</span>
                    <div class="stat-icon"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg></div>
                </div>
                <div class="stat-value" id="pendingChecklists">0</div>
                <div class="stat-label">Need Submission</div>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Active Tasks / Urgent Alerts -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>⚠️ Active Tasks</h3>
                    <a href="notifications.php">View All →</a>
                </div>
                <div class="card-body" id="activeTasksList">
                    <div class="empty-state">Loading tasks...</div>
                </div>
            </div>

            <!-- My Assigned Restrooms -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>🚻 My Assigned Restrooms</h3>
                    <a href="restroom_status.php">View All →</a>
                </div>
                <div class="card-body" id="assignedRestroomsList">
                    <div class="empty-state">Loading restrooms...</div>
                </div>
            </div>

            <!-- Today's Checklist Due -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>📋 Today's Checklist</h3>
                    <a href="checklist.php">View All →</a>
                </div>
                <div class="card-body" id="checklistList">
                    <div class="empty-state">Loading checklists...</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>⚡ Quick Actions</h3>
                    <span style="font-size: 0.7rem; opacity: 0.6;">Tap to start</span>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <div class="quick-btn" onclick="startChecklist()">
                            <svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"/></svg>
                            <span>New Checklist</span>
                        </div>
                        <div class="quick-btn" onclick="reportIssue()">
                            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span>Report Issue</span>
                        </div>
                        <div class="quick-btn" onclick="markAllComplete()">
                            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Mark Complete</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Recent Activity -->
            <div class="dashboard-card full-width">
                <div class="card-header">
                    <h3>📝 My Recent Activity</h3>
                    <a href="submissions.php">View History →</a>
                </div>
                <div class="card-body" id="recentActivityList">
                    <div class="empty-state">Loading activity...</div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
// =============================================
// CONFIGURATION
// =============================================
const USE_DEMO_MODE = true;  // Set to false when API is ready
const API_BASE = '../../api/';
const USER_ID = <?php echo json_encode($user['id']); ?>;

// =============================================
// DEMO DATA (Replace with API calls when production ready)
// =============================================
const DEMO_DATA = {
    assignedRestrooms: [
        { id: 1, name: 'GLR1 - Ground Left', soap_level: 85, air_quality: 32, has_alert: false },
        { id: 2, name: 'GLR2 - Ground Right', soap_level: 45, air_quality: 28, has_alert: true, alert_type: 'vape' },
        { id: 3, name: '1F-FR - 1st Floor Front', soap_level: 12, air_quality: 55, has_alert: true, alert_type: 'soap_low' },
        { id: 4, name: '1F-RR - 1st Floor Rear', soap_level: 92, air_quality: 25, has_alert: false },
        { id: 5, name: '2F-MR - 2nd Floor Main', soap_level: 67, air_quality: 85, has_alert: true, alert_type: 'air_quality' }
    ],
    
    activeTasks: [
        { id: 1, title: 'Soap Level Low', location: '1F-FR - 1st Floor Front', priority: 'high', time: '30 min ago', action: 'Refill Soap' },
        { id: 2, title: 'Air Quality Poor', location: '2F-MR - 2nd Floor Main', priority: 'high', time: '1 hour ago', action: 'Check Ventilation' },
        { id: 3, title: 'Vape Detected', location: 'GLR2 - Ground Right', priority: 'high', time: '2 hours ago', action: 'Inspect Area' },
        { id: 4, title: 'Routine Cleaning', location: 'GLR1 - Ground Left', priority: 'medium', time: 'Due today', action: 'Start Cleaning' }
    ],
    
    checklists: [
        { id: 1, restroom_name: 'GLR1 - Ground Left', due_date: '2026-04-24', status: 'pending' },
        { id: 2, restroom_name: 'GLR2 - Ground Right', due_date: '2026-04-24', status: 'pending' },
        { id: 3, restroom_name: '1F-FR - 1st Floor Front', due_date: '2026-04-23', status: 'completed' },
        { id: 4, restroom_name: '1F-RR - 1st Floor Rear', due_date: '2026-04-24', status: 'pending' }
    ],
    
    recentActivity: [
        { action: 'Completed checklist for 1F-RR', timestamp: '2026-04-24 09:30:00' },
        { action: 'Refilled soap at GLR1', timestamp: '2026-04-24 08:45:00' },
        { action: 'Reported vape incident at GLR2', timestamp: '2026-04-24 08:15:00' },
        { action: 'Cleaned 2F-MR restroom', timestamp: '2026-04-23 16:30:00' }
    ]
};

// =============================================
// DISPLAY FUNCTIONS
// =============================================

function displayActiveTasks() {
    const container = document.getElementById('activeTasksList');
    
    if (!DEMO_DATA.activeTasks.length) {
        container.innerHTML = '<div class="empty-state">✅ No active tasks. Great job!</div>';
        return;
    }
    
    container.innerHTML = DEMO_DATA.activeTasks.map(task => `
        <div class="task-item">
            <div class="task-info">
                <div class="task-priority ${task.priority}"></div>
                <div class="task-details">
                    <div class="task-title">${escapeHtml(task.title)}</div>
                    <div class="task-location">${escapeHtml(task.location)}</div>
                </div>
            </div>
            <div class="task-time">${escapeHtml(task.time)}</div>
            <button class="task-action" onclick="handleTaskAction('${task.action}', ${task.id})">${escapeHtml(task.action)}</button>
        </div>
    `).join('');
}

function displayAssignedRestrooms() {
    const container = document.getElementById('assignedRestroomsList');
    
    if (!DEMO_DATA.assignedRestrooms.length) {
        container.innerHTML = '<div class="empty-state">No restrooms assigned yet.</div>';
        return;
    }
    
    container.innerHTML = `
        <div class="restrooms-grid">
            ${DEMO_DATA.assignedRestrooms.map(restroom => `
                <div class="restroom-card ${restroom.has_alert ? 'alert' : ''}">
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
                        ${restroom.has_alert ? `<span class="stat-indicator" style="color: var(--red-deep);">⚠️</span>` : ''}
                    </div>
                    <button class="restroom-action-btn" onclick="viewRestroom(${restroom.id})">View Details →</button>
                </div>
            `).join('')}
        </div>
    `;
}

function displayChecklists() {
    const container = document.getElementById('checklistList');
    const pendingChecklists = DEMO_DATA.checklists.filter(c => c.status === 'pending');
    
    if (!pendingChecklists.length) {
        container.innerHTML = '<div class="empty-state">✅ All checklists completed for today!</div>';
        return;
    }
    
    container.innerHTML = pendingChecklists.map(checklist => `
        <div class="checklist-item">
            <div class="checklist-info">
                <div class="checklist-restroom">${escapeHtml(checklist.restroom_name)}</div>
                <div class="checklist-due">Due: ${formatDate(checklist.due_date)}</div>
            </div>
            <span class="checklist-status pending">Pending</span>
            <button class="btn-checklist" onclick="startChecklistForRestroom(${checklist.id})">Start →</button>
        </div>
    `).join('');
}

function displayRecentActivity() {
    const container = document.getElementById('recentActivityList');
    
    if (!DEMO_DATA.recentActivity.length) {
        container.innerHTML = '<div class="empty-state">No recent activity.</div>';
        return;
    }
    
    container.innerHTML = DEMO_DATA.recentActivity.map(activity => `
        <div class="activity-item">
            <div>${escapeHtml(activity.action)}</div>
            <div class="activity-time">${formatTime(activity.timestamp)}</div>
        </div>
    `).join('');
}

// =============================================
// ACTION HANDLERS
// =============================================

function handleTaskAction(action, taskId) {
    if (USE_DEMO_MODE) {
        alert(`[DEMO] ${action} for task #${taskId}\n\nIn live mode, this would update the database.`);
        // Simulate removal
        const taskIndex = DEMO_DATA.activeTasks.findIndex(t => t.id === taskId);
        if (taskIndex !== -1) {
            DEMO_DATA.activeTasks.splice(taskIndex, 1);
            displayActiveTasks();
            updateStats();
        }
    } else {
        // TODO: Call API endpoint when ready
        // fetch(`${API_BASE}resolve_task.php`, { method: 'POST', body: JSON.stringify({ task_id: taskId }) })
    }
}

function viewRestroom(restroomId) {
    if (USE_DEMO_MODE) {
        alert(`[DEMO] Viewing details for restroom #${restroomId}\n\nIn live mode, this would show detailed sensor data.`);
    } else {
        window.location.href = `restroom_status.php?id=${restroomId}`;
    }
}

function startChecklist() {
    if (USE_DEMO_MODE) {
        alert('[DEMO] Starting new checklist\n\nIn live mode, this would open the checklist form.');
    } else {
        window.location.href = 'checklist.php';
    }
}

function startChecklistForRestroom(checklistId) {
    if (USE_DEMO_MODE) {
        alert(`[DEMO] Starting checklist #${checklistId}\n\nIn live mode, this would open the evaluation form.`);
        // Mark as completed in demo
        const checklist = DEMO_DATA.checklists.find(c => c.id === checklistId);
        if (checklist) {
            checklist.status = 'completed';
            displayChecklists();
            updateStats();
            // Add to activity
            DEMO_DATA.recentActivity.unshift({
                action: `Completed checklist for ${checklist.restroom_name}`,
                timestamp: new Date().toISOString()
            });
            displayRecentActivity();
        }
    }
}

function reportIssue() {
    if (USE_DEMO_MODE) {
        const issue = prompt('[DEMO] Describe the issue you want to report:');
        if (issue) {
            alert(`[DEMO] Issue reported: "${issue}"\n\nYour supervisor has been notified.`);
            DEMO_DATA.recentActivity.unshift({
                action: `Reported issue: ${issue.substring(0, 50)}...`,
                timestamp: new Date().toISOString()
            });
            displayRecentActivity();
        }
    }
}

function markAllComplete() {
    if (USE_DEMO_MODE) {
        if (confirm('[DEMO] Mark all tasks as complete?')) {
            DEMO_DATA.activeTasks = [];
            DEMO_DATA.checklists.forEach(c => { if (c.status === 'pending') c.status = 'completed'; });
            displayActiveTasks();
            displayChecklists();
            updateStats();
            alert('[DEMO] All tasks marked as complete! Great work!');
        }
    }
}

// =============================================
// HELPER FUNCTIONS
// =============================================

function updateStats() {
    document.getElementById('assignedCount').textContent = DEMO_DATA.assignedRestrooms.length;
    document.getElementById('pendingTasks').textContent = DEMO_DATA.activeTasks.length;
    document.getElementById('pendingChecklists').textContent = DEMO_DATA.checklists.filter(c => c.status === 'pending').length;
    document.getElementById('taskCountSummary').textContent = DEMO_DATA.activeTasks.length;
    
    // Calculate completed today (simulated)
    const todayStr = new Date().toDateString();
    const completedToday = DEMO_DATA.recentActivity.filter(a => {
        const activityDate = new Date(a.timestamp).toDateString();
        return activityDate === todayStr;
    }).length;
    document.getElementById('completedToday').textContent = completedToday;
}

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
    const diffMins = Math.floor((now - date) / 60000);
    
    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins} min ago`;
    if (diffMins < 1440) return `${Math.floor(diffMins / 60)} hours ago`;
    return date.toLocaleDateString();
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
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

function setCurrentDate() {
    const dateElement = document.getElementById('currentDate');
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    dateElement.textContent = now.toLocaleDateString('en-US', options);
}

// =============================================
// NOTIFICATION FUNCTIONS
// =============================================

function fetchNotificationCount() {
    if (USE_DEMO_MODE) {
        document.getElementById('notificationCount').textContent = DEMO_DATA.activeTasks.length;
    } else {
        // TODO: Fetch from API
        // fetch(`${API_BASE}get_notifications.php?user_id=${USER_ID}`)
    }
}

// =============================================
// INITIALIZATION
// =============================================

function initDashboard() {
    setCurrentDate();
    displayActiveTasks();
    displayAssignedRestrooms();
    displayChecklists();
    displayRecentActivity();
    updateStats();
    fetchNotificationCount();
}

// Start dashboard when page loads
document.addEventListener('DOMContentLoaded', initDashboard);

// Notification bell click
document.getElementById('notificationBell').addEventListener('click', () => {
    if (USE_DEMO_MODE) {
        alert(`[DEMO] You have ${DEMO_DATA.activeTasks.length} unread notifications.\n\nIn live mode, this would show actual notifications.`);
    } else {
        window.location.href = 'notifications.php';
    }
});
</script>
</body>
</html>