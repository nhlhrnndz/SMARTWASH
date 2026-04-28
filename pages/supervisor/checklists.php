<?php
// ============================================================
//  SmartWash - Checklists Page
//  File: pages/supervisor/checklists.php
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
    <title>Checklists — SmartWash | BatStateU ARASOF-Nasugbu</title>
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
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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
        
        /* Checklist Items */
        .checklist-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--gray-border);
            transition: background 0.2s;
        }
        .checklist-item:hover { background: var(--light); }
        .checklist-item:last-child { border-bottom: none; }
        
        .checklist-info { flex: 1; }
        .checklist-restroom {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.25rem;
            color: var(--text-dark);
        }
        .checklist-staff {
            font-size: 0.75rem;
            color: var(--text-mid);
            margin-bottom: 0.2rem;
        }
        .checklist-date {
            font-size: 0.65rem;
            color: var(--text-mid);
            opacity: 0.6;
        }
        
        .rating-stars {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            margin-bottom: 0.5rem;
        }
        .star {
            font-size: 0.9rem;
            color: #ddd;
        }
        .star.filled { color: var(--yellow-warning); }
        .rating-value {
            font-weight: 600;
            font-size: 0.8rem;
            margin-left: 0.5rem;
        }
        
        .supervisor-rating {
            margin-top: 0.3rem;
            font-size: 0.7rem;
            color: var(--green-success);
        }
        
        .score-badge {
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }
        .score-excellent { background: rgba(46,125,50,0.15); color: var(--green-success); }
        .score-good { background: rgba(21,101,192,0.15); color: var(--blue-info); }
        .score-fair { background: rgba(249,168,37,0.15); color: #b45309; }
        .score-poor { background: rgba(197,0,0,0.15); color: var(--red-deep); }
        
        .checklist-actions {
            display: flex;
            gap: 0.5rem;
        }
        .btn-approve {
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
        .btn-approve:hover { background: #1b5e20; transform: scale(1.02); }
        
        .btn-flag {
            background: var(--orange-critical);
            color: var(--white);
            border: none;
            padding: 0.4rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-flag:hover { background: #e65100; transform: scale(1.02); }
        
        .btn-view {
            background: var(--light);
            color: var(--text-mid);
            border: 1px solid var(--gray-border);
            padding: 0.4rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-view:hover { background: var(--red-deep); color: var(--white); border-color: var(--red-deep); }
        
        .status-badge {
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }
        .status-pending { background: rgba(249,168,37,0.15); color: #b45309; }
        .status-approved { background: rgba(46,125,50,0.15); color: var(--green-success); }
        .status-flagged { background: rgba(197,0,0,0.15); color: var(--red-deep); }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-mid);
            opacity: 0.6;
        }
        
        .loading {
            text-align: center;
            padding: 3rem;
            color: var(--text-mid);
        }
        
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
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .main-content { margin-left: 0; }
            .checklist-item { flex-direction: column; align-items: flex-start; gap: 1rem; }
            .checklist-actions { align-self: flex-end; }
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
            <div class="nav-item"><a href="checklists.php" class="nav-link active"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Checklists</a></div>
            <div class="nav-item"><a href="maintenance_log.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Maintenance Log</a></div>
            <div class="nav-item"><a href="alerts.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Alerts</a></div>
            <div class="nav-item"><a href="staff_management.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.758 11.226C12.028 10.464 10.6 10 9 10s-3.028.464-3.758 1.226A2 2 0 004 13v1h10v-1a2 2 0 00-1.242-1.774zM4.5 17a.5.5 0 01.5-.5h8a.5.5 0 010 1h-8a.5.5 0 01-.5-.5z"/></svg>Staff Management</a></div>
            <div class="nav-item"><a href="settings.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>Settings</a></div>
        </nav>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h1>Cleanliness Checklists</h1>
                <p>Review and approve submissions from maintenance staff</p>
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

        <!-- Stats Cards -->
        <div class="stats-grid" id="statsContainer">
            <div class="stat-card"><div class="stat-value" id="totalCount">-</div><div class="stat-label">Total</div></div>
            <div class="stat-card"><div class="stat-value" id="pendingCount">-</div><div class="stat-label">Pending</div></div>
            <div class="stat-card"><div class="stat-value" id="approvedCount">-</div><div class="stat-label">Approved</div></div>
            <div class="stat-card"><div class="stat-value" id="flaggedCount">-</div><div class="stat-label">Flagged</div></div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <button class="filter-btn active" data-filter="all">All Submissions</button>
            <button class="filter-btn" data-filter="pending">Pending Review</button>
            <button class="filter-btn" data-filter="approved">Approved</button>
            <button class="filter-btn" data-filter="flagged">Flagged</button>
        </div>

        <!-- Checklists Card -->
        <div class="card">
            <div id="checklistsList">
                <div class="loading">Loading checklists...</div>
            </div>
        </div>
    </main>
</div>

<script>
// Configuration
let currentFilter = 'all';
let currentChecklists = [];

// Helper function to show toast notifications
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Load checklists from API
async function loadChecklists() {
    const container = document.getElementById('checklistsList');
    container.innerHTML = '<div class="loading">Loading checklists...</div>';
    
    try {
        const status = currentFilter === 'all' ? 'all' : currentFilter;
        const response = await fetch(`../../api/get_checklists.php?status=${status}`);
        const result = await response.json();
        
        if (result.success && result.data) {
            currentChecklists = result.data;
            displayChecklists(currentChecklists);
            updateStats(currentChecklists);
        } else {
            container.innerHTML = '<div class="empty-state">No checklists found</div>';
        }
    } catch (error) {
        console.error('Error:', error);
        container.innerHTML = '<div class="empty-state">Error loading checklists. Please refresh the page.</div>';
    }
}

// Display checklists
function displayChecklists(checklists) {
    const container = document.getElementById('checklistsList');
    
    if (!checklists || checklists.length === 0) {
        container.innerHTML = '<div class="empty-state">📋 No checklists found for this filter.</div>';
        return;
    }
    
    container.innerHTML = checklists.map(checklist => {
        const starRating = Math.floor(checklist.rating);
        const supervisorRating = checklist.supervisor_rating;
        
        return `
            <div class="checklist-item" data-id="${checklist.id}">
                <div class="checklist-info">
                    <div class="checklist-restroom">${escapeHtml(checklist.restroom_name)}</div>
                    <div class="checklist-staff">👤 ${escapeHtml(checklist.staff_name)}</div>
                    <div class="checklist-date">📅 ${formatDate(checklist.submitted_at)}</div>
                </div>
                <div style="text-align: center; min-width: 140px;">
                    <div class="rating-stars">
                        ${getStarRating(starRating)}
                        <span class="rating-value">${checklist.rating}/5</span>
                    </div>
                    ${supervisorRating ? `
                        <div class="supervisor-rating">
                            ⭐ Supervisor: ${'★'.repeat(supervisorRating)}${'☆'.repeat(5-supervisorRating)}
                        </div>
                    ` : ''}
                    <div style="margin-top: 0.3rem;">
                        <span class="status-badge status-${checklist.status}">
                            ${checklist.status === 'pending' ? '⏳ Pending' : checklist.status === 'approved' ? '✓ Approved' : '⚠️ Flagged'}
                        </span>
                    </div>
                </div>
                <div class="checklist-actions">
                    <button class="btn-view" onclick="viewDetails(${checklist.id})">View Details</button>
                    ${checklist.status === 'pending' ? `
                        <button class="btn-approve" onclick="reviewChecklist(${checklist.id}, 'approve')">✓ Approve & Rate</button>
                        <button class="btn-flag" onclick="reviewChecklist(${checklist.id}, 'flag')">⚠️ Flag</button>
                    ` : checklist.status === 'flagged' ? `
                        <button class="btn-approve" onclick="reviewChecklist(${checklist.id}, 'approve')">✓ Approve & Rate</button>
                    ` : ''}
                </div>
            </div>
        `;
    }).join('');
}

// View details of a checklist
async function viewDetails(id) {
    try {
        const response = await fetch(`../../api/get_checklist_details.php?id=${id}`);
        const result = await response.json();
        
        if (result.success && result.data) {
            const c = result.data;
            const supervisorRatingDisplay = c.supervisor_rating 
                ? `\n⭐ Supervisor Rating: ${c.supervisor_rating}/5 ${'★'.repeat(c.supervisor_rating)}${'☆'.repeat(5-c.supervisor_rating)}`
                : '';
            const reviewNotesDisplay = c.review_notes ? `\n\n📝 Supervisor Feedback: ${c.review_notes}` : '';
            
            alert(`📋 CHECKLIST DETAILS\n━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n` +
                  `📍 Restroom: ${c.restroom_name}\n` +
                  `👤 Staff: ${c.staff_name}\n` +
                  `📅 Date: ${formatDate(c.submitted_at)}\n` +
                  `📊 Status: ${c.status.toUpperCase()}\n` +
                  `⭐ Auto Rating: ${c.rating}/5 (based on completed tasks)${supervisorRatingDisplay}\n\n` +
                  `━━━━━━━━━━━━━━━━━━━━━━━━━━\n` +
                  `📝 TASKS COMPLETED:\n` +
                  `  ${c.floor_clean ? '✅' : '❌'} Floor Clean\n` +
                  `  ${c.toilets_clean ? '✅' : '❌'} Toilets Clean\n` +
                  `  ${c.sinks_clean ? '✅' : '❌'} Sinks Clean\n` +
                  `  ${c.mirrors_clean ? '✅' : '❌'} Mirrors Clean\n` +
                  `  ${c.soap_refilled ? '✅' : '❌'} Soap Refilled\n` +
                  `  ${c.trash_emptied ? '✅' : '❌'} Trash Emptied\n` +
                  `  ${c.odor_free ? '✅' : '❌'} Odor Free\n` +
                  `${c.notes ? `\n📝 Staff Notes: ${c.notes}` : ''}${reviewNotesDisplay}`);
        } else {
            showToast('Could not load checklist details', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error loading details', 'error');
    }
}

// Review checklist (approve or flag)
async function reviewChecklist(id, action) {
    let reviewNotes = null;
    let rating = null;
    
    if (action === 'flag') {
        reviewNotes = prompt('Please provide a reason for flagging this checklist:');
        if (reviewNotes === null) return;
        if (!reviewNotes.trim()) {
            showToast('Please provide a reason for flagging.', 'error');
            return;
        }
    } else {
        rating = prompt('Rate this work (1-5 stars):\n\n1 = Poor\n2 = Fair\n3 = Good\n4 = Very Good\n5 = Excellent', '5');
        if (rating === null) return;
        
        rating = parseInt(rating);
        if (isNaN(rating) || rating < 1 || rating > 5) {
            showToast('Please enter a valid rating between 1 and 5.', 'error');
            return;
        }
        
        reviewNotes = prompt('Optional feedback notes for the staff (or click OK to skip):');
        if (reviewNotes === null) reviewNotes = '';
    }
    
    try {
        const response = await fetch('../../api/review_checklist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                checklist_id: id,
                action: action,
                rating: action === 'approve' ? rating : null,
                review_notes: reviewNotes
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            let message = result.data.message;
            if (action === 'approve' && rating) {
                message += `\n\nRating given: ${rating}/5 ${'★'.repeat(rating)}${'☆'.repeat(5-rating)}`;
            }
            showToast(message, 'success');
            loadChecklists(); // Refresh the list
        } else {
            showToast('Error: ' + (result.error || 'Failed to review checklist'), 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Failed to review checklist. Please try again.', 'error');
    }
}

// Get star rating HTML
function getStarRating(score) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += `<span class="star ${i <= score ? 'filled' : ''}">★</span>`;
    }
    return stars;
}

// Update statistics
function updateStats(checklists) {
    const total = checklists.length;
    const pending = checklists.filter(c => c.status === 'pending').length;
    const approved = checklists.filter(c => c.status === 'approved').length;
    const flagged = checklists.filter(c => c.status === 'flagged').length;
    
    document.getElementById('totalCount').textContent = total;
    document.getElementById('pendingCount').textContent = pending;
    document.getElementById('approvedCount').textContent = approved;
    document.getElementById('flaggedCount').textContent = flagged;
}

// Filter checklists
function filterChecklists(filter) {
    currentFilter = filter;
    
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-filter') === filter) {
            btn.classList.add('active');
        }
    });
    
    loadChecklists();
}

// Helper functions
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
        month: 'short', 
        day: 'numeric', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Add event listeners to filter buttons
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => filterChecklists(btn.getAttribute('data-filter')));
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    loadChecklists();
});
</script>
</body>
</html>