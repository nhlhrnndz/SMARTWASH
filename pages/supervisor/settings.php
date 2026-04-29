<?php
// ============================================================
//  SmartWash - Settings Page
//  File: pages/supervisor/settings.php
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
    <title>Settings — SmartWash | BatStateU ARASOF-Nasugbu</title>
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

        /* Settings Grid */
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
        }
        
        /* Cards */
        .card {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid var(--gray-border);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        
        .card-header {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--gray-border);
            background: var(--light);
        }
        .card-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .card-body {
            padding: 1.2rem 1.5rem;
        }
        
        /* Setting Rows */
        .setting-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--gray-border);
        }
        .setting-row:last-child {
            border-bottom: none;
        }
        .setting-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-dark);
        }
        .setting-description {
            font-size: 0.7rem;
            color: var(--text-mid);
            opacity: 0.7;
            margin-top: 0.2rem;
        }
        .setting-control {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        /* Inputs */
        input[type="number"] {
            width: 100px;
            padding: 0.5rem;
            border: 1px solid var(--gray-border);
            border-radius: 8px;
            font-size: 0.85rem;
            font-family: 'DM Sans', sans-serif;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 200px;
            padding: 0.5rem;
            border: 1px solid var(--gray-border);
            border-radius: 8px;
            font-size: 0.85rem;
            font-family: 'DM Sans', sans-serif;
        }
        select {
            padding: 0.5rem;
            border: 1px solid var(--gray-border);
            border-radius: 8px;
            font-size: 0.85rem;
            font-family: 'DM Sans', sans-serif;
            background: var(--white);
        }
        
        /* Checkbox Toggle */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.3s;
            border-radius: 24px;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }
        input:checked + .toggle-slider {
            background-color: var(--green-success);
        }
        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        
        /* Buttons */
        .btn-primary {
            background: var(--red-deep);
            color: var(--white);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background: var(--red-vivid);
            transform: scale(1.02);
        }
        .btn-secondary {
            background: var(--light);
            color: var(--text-mid);
            border: 1px solid var(--gray-border);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-secondary:hover {
            background: var(--red-deep);
            color: var(--white);
            border-color: var(--red-deep);
        }
        .btn-danger {
            background: transparent;
            color: var(--red-deep);
            border: 1px solid var(--red-deep);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-danger:hover {
            background: var(--red-deep);
            color: var(--white);
        }
        
        .save-bar {
            position: sticky;
            bottom: 20px;
            background: var(--white);
            border: 1px solid var(--gray-border);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-top: 1.5rem;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .main-content { margin-left: 0; }
            .settings-grid { grid-template-columns: 1fr; }
            .setting-row { flex-direction: column; align-items: flex-start; gap: 0.8rem; }
            .setting-control { width: 100%; justify-content: flex-end; }
            input[type="text"], input[type="email"] { width: 100%; }
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
            <div class="nav-item"><a href="vape_incidents.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v4a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Vape Incidents</a></div>
            <div class="nav-item"><a href="air_quality.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 4a1 1 0 011-1h8a1 1 0 011 1v1a1 1 0 01-1 1H6a1 1 0 01-1-1V4zM4 9a1 1 0 011-1h10a1 1 0 011 1v1a1 1 0 01-1 1H5a1 1 0 01-1-1V9zM6 14a1 1 0 100-2h8a1 1 0 100 2H6z" clip-rule="evenodd"/></svg>Air Quality</a></div>
            <div class="nav-item"><a href="checklists.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Checklists</a></div>
            <div class="nav-item"><a href="maintenance_log.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>Maintenance Log</a></div>
            <div class="nav-item"><a href="alerts.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v4a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Alerts</a></div>
            <div class="nav-item"><a href="staff_management.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>Staff Management</a></div>
            <div class="nav-item"><a href="settings.php" class="nav-link active"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>Settings</a></div>
        </nav>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h1>System Settings</h1>
                <p>Configure thresholds, preferences, and system options, etc</p>
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

        <div class="settings-grid">
            <!-- Alert Thresholds Card -->
            <div class="card">
                <div class="card-header">
                    <h3>🔔 Alert Thresholds</h3>
                </div>
                <div class="card-body">
                    <div class="setting-row">
                        <div>
                            <div class="setting-label">Soap Low Alert (%)</div>
                            <div class="setting-description">Trigger alert when soap level drops below this percentage</div>
                        </div>
                        <div class="setting-control">
                            <input type="number" id="soapThreshold" value="20" min="0" max="100" step="5">
                            <span>%</span>
                        </div>
                    </div>
                    <div class="setting-row">
                        <div>
                            <div class="setting-label">Air Quality Warning (AQI)</div>
                            <div class="setting-description">Yellow warning threshold for air quality</div>
                        </div>
                        <div class="setting-control">
                            <input type="number" id="aqiWarning" value="50" min="0" max="300" step="10">
                        </div>
                    </div>
                    <div class="setting-row">
                        <div>
                            <div class="setting-label">Air Quality Critical (AQI)</div>
                            <div class="setting-description">Red critical threshold for air quality</div>
                        </div>
                        <div class="setting-control">
                            <input type="number" id="aqiCritical" value="100" min="0" max="300" step="10">
                        </div>
                    </div>
                    <div class="setting-row">
                        <div>
                            <div class="setting-label">Auto Air Freshener</div>
                            <div class="setting-description">Automatically trigger air freshener when AQI exceeds warning level</div>
                        </div>
                        <div class="setting-control">
                            <label class="toggle-switch">
                                <input type="checkbox" id="autoFreshener" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    <div class="setting-row">
                        <div>
                            <div class="setting-label">Vape Detection Sensitivity</div>
                            <div class="setting-description">Adjust sensitivity for vape/smoke detection sensors</div>
                        </div>
                        <div class="setting-control">
                            <select id="vapeSensitivity">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Settings Card -->
            <div class="card">
                <div class="card-header">
                    <h3>📧 Notification Settings</h3>
                </div>
                <div class="card-body">
                    <div class="setting-row">
                        <div>
                            <div class="setting-label">Email Notifications</div>
                            <div class="setting-description">Receive critical alerts via email</div>
                        </div>
                        <div class="setting-control">
                            <label class="toggle-switch">
                                <input type="checkbox" id="emailNotifications" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    <div class="setting-row">
                        <div>
                            <div class="setting-label">Alert Email Address</div>
                            <div class="setting-description">Email address for receiving notifications</div>
                        </div>
                        <div class="setting-control">
                            <input type="email" id="alertEmail" placeholder="supervisor@batstateu.edu.ph">
                        </div>
                    </div>
                    <div class="setting-row">
                        <div>
                            <div class="setting-label">Push Notifications</div>
                            <div class="setting-description">Browser push notifications for real-time alerts</div>
                        </div>
                        <div class="setting-control">
                            <label class="toggle-switch">
                                <input type="checkbox" id="pushNotifications" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    <div class="setting-row">
                        <div>
                            <div class="setting-label">Alert Sound</div>
                            <div class="setting-description">Play sound when critical alerts are triggered</div>
                        </div>
                        <div class="setting-control">
                            <label class="toggle-switch">
                                <input type="checkbox" id="alertSound">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Settings Card -->
            <div class="card">
                <div class="card-header">
                    <h3>👤 Account Settings</h3>
                </div>
                <div class="card-body">
                    <div class="setting-row">
                        <div>
                            <div class="setting-label">Full Name</div>
                            <div class="setting-description">Your display name on the system</div>
                        </div>
                        <div class="setting-control">
                            <input type="text" id="fullName" value="<?php echo htmlspecialchars($fullName); ?>">
                        </div>
                    </div>
                    <div class="setting-row">
                        <div>
                            <div class="setting-label">Email Address</div>
                            <div class="setting-description">Your contact email</div>
                        </div>
                        <div class="setting-control">
                            <input type="email" id="userEmail" placeholder="supervisor@batstateu.edu.ph">
                        </div>
                    </div>
                    <div class="setting-row">
                        <div>
                            <div class="setting-label">Change Password</div>
                            <div class="setting-description">Update your account password</div>
                        </div>
                        <div class="setting-control">
                            <button class="btn-secondary" onclick="changePassword()">Change Password</button>
                        </div>
                    </div>
                    <div class="setting-row">
                        <div>
                            <div class="setting-label">Session Timeout (minutes)</div>
                            <div class="setting-description">Auto-logout after inactivity</div>
                        </div>
                        <div class="setting-control">
                            <input type="number" id="sessionTimeout" value="30" min="5" max="120" step="5">
                            <span>min</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3>ℹ️ System Information</h3>
                </div>
                <div class="card-body">
                    <div class="setting-row">
                        <div class="setting-label">Version</div>
                        <div>SmartWash v2.0</div>
                    </div>
                    <div class="setting-row">
                        <div class="setting-label">Last Sensor Update</div>
                        <div id="lastSensorUpdate">Just now</div>
                    </div>
                    <div class="setting-row">
                        <div class="setting-label">Database Status</div>
                        <div><span style="color: var(--green-success);">● Connected</span></div>
                    </div>
                    <div class="setting-row">
                        <div class="setting-label">ESP32 Devices Online</div>
                        <div id="onlineDevices">8 / 8</div>
                    </div>
                    <div class="setting-row">
                        <div class="setting-label">Storage Usage</div>
                        <div>45% (225 MB / 500 MB)</div>
                    </div>
                    <div class="setting-row">
                        <div>
                            <div class="setting-label">Clear Cache</div>
                            <div class="setting-description">Clear system cache and temporary data</div>
                        </div>
                        <div>
                            <button class="btn-secondary" onclick="clearCache()">Clear Cache</button>
                        </div>
                    </div>
                    <div class="setting-row">
                        <div>
                            <div class="setting-label">Backup Database</div>
                            <div class="setting-description">Download a backup of all system data</div>
                        </div>
                        <div>
                            <button class="btn-secondary" onclick="backupDatabase()">Backup Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Bar -->
        <div class="save-bar">
            <button class="btn-secondary" onclick="resetSettings()">Reset to Default</button>
            <button class="btn-primary" onclick="saveSettings()">Save All Changes</button>
        </div>
    </main>
</div>

<div class="demo-badge" onclick="toggleDemoMode()">🧪 DEMO MODE | Settings Configuration</div>

<script>
const USE_DEMO_MODE = true;
const API_BASE = '../../api/';

function saveSettings() {
    const settings = {
        soap_threshold: document.getElementById('soapThreshold').value,
        aqi_warning: document.getElementById('aqiWarning').value,
        aqi_critical: document.getElementById('aqiCritical').value,
        auto_freshener: document.getElementById('autoFreshener').checked,
        vape_sensitivity: document.getElementById('vapeSensitivity').value,
        email_notifications: document.getElementById('emailNotifications').checked,
        alert_email: document.getElementById('alertEmail').value,
        push_notifications: document.getElementById('pushNotifications').checked,
        alert_sound: document.getElementById('alertSound').checked,
        full_name: document.getElementById('fullName').value,
        user_email: document.getElementById('userEmail').value,
        session_timeout: document.getElementById('sessionTimeout').value
    };
    
    if (USE_DEMO_MODE) {
        alert('[DEMO] Settings saved!\n\n' + JSON.stringify(settings, null, 2));
    } else {
        // Save to API
        fetch(API_BASE + 'save_settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(settings)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Settings saved successfully!');
            } else {
                alert('Error saving settings: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving settings. Please try again.');
        });
    }
}

function resetSettings() {
    if (confirm('Reset all settings to default values?')) {
        document.getElementById('soapThreshold').value = '20';
        document.getElementById('aqiWarning').value = '50';
        document.getElementById('aqiCritical').value = '100';
        document.getElementById('autoFreshener').checked = true;
        document.getElementById('vapeSensitivity').value = 'medium';
        document.getElementById('emailNotifications').checked = true;
        document.getElementById('alertEmail').value = '';
        document.getElementById('pushNotifications').checked = true;
        document.getElementById('alertSound').checked = false;
        document.getElementById('sessionTimeout').value = '30';
        
        if (USE_DEMO_MODE) {
            alert('[DEMO] Settings reset to default values.');
        }
    }
}

function changePassword() {
    const currentPassword = prompt('Enter current password:');
    if (!currentPassword) return;
    
    const newPassword = prompt('Enter new password (min 6 characters):');
    if (!newPassword) return;
    
    const confirmPassword = prompt('Confirm new password:');
    if (newPassword !== confirmPassword) {
        alert('Passwords do not match!');
        return;
    }
    
    if (newPassword.length < 6) {
        alert('Password must be at least 6 characters!');
        return;
    }
    
    if (USE_DEMO_MODE) {
        alert('[DEMO] Password change request submitted.\n\nIn live mode, this would update your password in the database.');
    } else {
        // Update password via API
        fetch(API_BASE + 'change_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                current_password: currentPassword,
                new_password: newPassword
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Password changed successfully!');
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function clearCache() {
    if (confirm('Clear system cache? This may temporarily slow down the system while it rebuilds.')) {
        if (USE_DEMO_MODE) {
            alert('[DEMO] Cache cleared successfully.');
        } else {
            fetch(API_BASE + 'clear_cache.php', { method: 'POST' })
                .then(() => alert('Cache cleared successfully!'));
        }
    }
}

function backupDatabase() {
    if (USE_DEMO_MODE) {
        alert('[DEMO] Database backup would download here.\n\nIn live mode, this would generate a SQL dump file.');
    } else {
        window.location.href = API_BASE + 'backup_database.php';
    }
}

function toggleDemoMode() {
    if (confirm('Toggle between Demo and Live mode?\n\nCurrently in: DEMO MODE\n\nNote: Live mode would connect to:\n- api/save_settings.php\n- api/change_password.php\n- Real configuration database')) {
        alert('Switching to live mode would save settings to the database.');
    }
}

function updateLastSensorTime() {
    const lastUpdate = new Date().toLocaleTimeString();
    document.getElementById('lastSensorUpdate').innerHTML = lastUpdate;
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    updateLastSensorTime();
    
    // Auto-update sensor time every minute
    setInterval(updateLastSensorTime, 60000);
});
</script>
</body>
</html>