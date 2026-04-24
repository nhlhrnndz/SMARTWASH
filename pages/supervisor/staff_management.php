<?php
// ============================================================
//  SmartWash - Staff Management Page
//  File: pages/supervisor/staff-management.php
// ============================================================

require_once '../../auth/session.php';
requireRole(['supervisor', 'admin']);

$currentUser = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management - SmartWash | BatStateU ARASOF-Nasugbu</title>
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
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--text-dark);
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

        /* Tabs */
        .tabs {
            display: flex;
            gap: 1rem;
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

        .tab-btn.active {
            color: var(--red-deep);
        }

        .tab-btn.active::after {
            transform: scaleX(1);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid var(--gray-border);
        }

        .stat-value {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--red-deep);
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--text-mid);
            opacity: 0.7;
        }

        /* Table */
        .table-container {
            background: var(--white);
            border-radius: 12px;
            border: 1px solid var(--gray-border);
            overflow-x: auto;
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

        .btn-approve {
            background: var(--green-success);
            color: white;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
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
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-reject:hover {
            background: #9b0000;
            transform: translateY(-1px);
        }

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

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
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
            <div class="nav-item"><a href="live-monitoring.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>Live Monitoring</a></div>
            <div class="nav-item"><a href="vape-incidents.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6.672 1.911a1 1 0 10-1.932.518l.259.966a1 1 0 001.932-.518l-.26-.966zM2.429 4.74a1 1 0 10-.518 1.932l.966.259a1 1 0 00.518-1.932l-.966-.26zm8.814 3.748a1 1 0 00-1.414-1.414L6.5 10.1l-1.5-1.5a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/><path d="M16 15.5a2.5 2.5 0 00-5 0v1a2.5 2.5 0 005 0v-1z"/></svg>Vape Incidents</a></div>
            <div class="nav-item"><a href="air-quality.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 4a1 1 0 011-1h8a1 1 0 011 1v1a1 1 0 01-1 1H6a1 1 0 01-1-1V4zM4 9a1 1 0 011-1h10a1 1 0 011 1v1a1 1 0 01-1 1H5a1 1 0 01-1-1V9zM6 14a1 1 0 100-2h8a1 1 0 100 2H6z" clip-rule="evenodd"/></svg>Air Quality</a></div>
            <div class="nav-item"><a href="checklists.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Checklists</a></div>
            <div class="nav-item"><a href="maintenance-log.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>Maintenance Log</a></div>
            <div class="nav-item"><a href="alerts.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Alerts</a></div>
            <div class="nav-item"><a href="staff-management.php" class="nav-link active"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>Staff Management</a></div>
            <div class="nav-item"><a href="settings.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>Settings</a></div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h1>Staff Management</h1>
                <p>Approve or reject new account requests and manage existing staff</p>
            </div>
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($currentUser['full_name'] ?? 'Supervisor'); ?></div>
                    <div class="user-role"><?php echo htmlspecialchars($currentUser['role'] ?? 'supervisor'); ?></div>
                </div>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($currentUser['full_name'] ?? 'S', 0, 2)); ?>
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
            <button class="tab-btn active" onclick="switchTab('pending')">Pending Approvals</button>
            <button class="tab-btn" onclick="switchTab('approved')">Approved Staff</button>
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

<script>
// API Base URL
const API_BASE = '../../api/';

// Load all data on page load
document.addEventListener('DOMContentLoaded', () => {
    loadPendingRequests();
    loadApprovedStaff();
});

// Tab switching
function switchTab(tab) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    // Update tab contents
    document.getElementById('pendingTab').classList.remove('active');
    document.getElementById('approvedTab').classList.remove('active');
    
    if (tab === 'pending') {
        document.getElementById('pendingTab').classList.add('active');
    } else {
        document.getElementById('approvedTab').classList.add('active');
    }
}

// Load pending registration requests
async function loadPendingRequests() {
    try {
        const response = await fetch(API_BASE + 'get_pending_registrations.php');
        const data = await response.json();
        
        if (data.success) {
            updateStats(data.requests.length);
            displayPendingRequests(data.requests);
        } else {
            showError('Failed to load pending requests: ' + data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('pendingTableBody').innerHTML = '<tr><td colspan="5" class="empty-state">Error loading data</td></tr>';
    }
}

// Display pending requests in table
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
            <td><span class="badge ${req.role === 'supervisor' ? 'badge-approved' : 'badge-pending'}">${escapeHtml(req.role)}</span></td>
            <td>${formatDate(req.requested_at)}</td>
            <td>
                <button class="btn-approve" onclick="approveUser(${req.id})">✓ Approve</button>
                <button class="btn-reject" onclick="rejectUser(${req.id})">✗ Reject</button>
            </td>
        </tr>
    `).join('');
}

// Load approved staff
async function loadApprovedStaff() {
    try {
        const response = await fetch(API_BASE + 'get_approved_staff.php');
        const data = await response.json();
        
        if (data.success) {
            displayApprovedStaff(data.staff);
            updateStaffCounts(data.staff);
        } else {
            showError('Failed to load staff: ' + data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('approvedTableBody').innerHTML = '<tr><td colspan="6" class="empty-state">Error loading data</td></tr>';
    }
}

// Display approved staff in table
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
            <td><span class="badge ${member.role === 'supervisor' ? 'badge-approved' : 'badge-pending'}">${escapeHtml(member.role)}</span></td>
            <td><span class="badge ${member.status === 'active' ? 'badge-active' : 'badge-inactive'}">${escapeHtml(member.status)}</span></td>
            <td>${formatDate(member.created_at)}</td>
            <td>
                ${member.role !== 'admin' ? `<button class="btn-reject" onclick="deactivateUser(${member.id})">Deactivate</button>` : '—'}
            </td>
        </tr>
    `).join('');
}

// Approve user
async function approveUser(requestId) {
    if (!confirm('Approve this user? They will be able to log in immediately.')) return;
    
    showLoading(true);
    
    try {
        const response = await fetch(API_BASE + 'approve_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ request_id: requestId, action: 'approve' })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('User approved successfully!', 'success');
            loadPendingRequests();
            loadApprovedStaff();
        } else {
            showToast('Error: ' + data.error, 'error');
        }
    } catch (error) {
        showToast('Network error: ' + error.message, 'error');
    } finally {
        showLoading(false);
    }
}

// Reject user
async function rejectUser(requestId) {
    if (!confirm('Reject this registration request? The user will be notified.')) return;
    
    showLoading(true);
    
    try {
        const response = await fetch(API_BASE + 'approve_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ request_id: requestId, action: 'reject' })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Registration request rejected.', 'success');
            loadPendingRequests();
        } else {
            showToast('Error: ' + data.error, 'error');
        }
    } catch (error) {
        showToast('Network error: ' + error.message, 'error');
    } finally {
        showLoading(false);
    }
}

// Deactivate user (for future implementation)
function deactivateUser(userId) {
    alert('User deactivation will be implemented in the next phase.');
}

// Update stats
function updateStats(pendingCount) {
    document.getElementById('pendingCount').textContent = pendingCount;
}

function updateStaffCounts(staff) {
    const total = staff.length;
    const supervisors = staff.filter(s => s.role === 'supervisor').length;
    const maintenance = staff.filter(s => s.role === 'staff').length;
    
    document.getElementById('totalStaffCount').textContent = total;
    document.getElementById('supervisorCount').textContent = supervisors;
    document.getElementById('maintenanceCount').textContent = maintenance;
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
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.style.background = type === 'success' ? '#2e7d32' : (type === 'error' ? '#c50000' : '#333');
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function showLoading(show) {
    // Simple loading indicator - you can enhance this
    const pendingBody = document.getElementById('pendingTableBody');
    if (show && pendingBody.innerHTML.includes('Loading')) {
        // Already showing loading
    }
}

// Auto-refresh every 30 seconds
setInterval(() => {
    if (document.getElementById('pendingTab').classList.contains('active')) {
        loadPendingRequests();
    } else {
        loadApprovedStaff();
    }
}, 30000);
</script>
</body>
</html>