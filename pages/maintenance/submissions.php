<?php
require_once '../../auth/session.php';
requireRole(['maintenance']);

$user = currentUser();
$fullName = $user['full_name'];
$userId = $user['id'];
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
    <title>My Submissions — SmartWash</title>
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
            --gray-border: #e0d8c8;
            --blue-info: #1565C0;
        }
        body { font-family: 'DM Sans', sans-serif; background: var(--cream); color: var(--text-dark); }
        .dashboard-wrapper { display: flex; min-height: 100vh; }
        
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
        .nav-link svg { width: 20px; height: 20px; color: #aaa; }
        .nav-link:hover { background: rgba(197,0,0,0.05); color: var(--red-deep); }
        .nav-link:hover svg { color: var(--red-deep); }
        .nav-link.active { background: var(--red-deep); color: var(--white); }
        .nav-link.active svg { color: var(--white); }
        
        .main-content { flex: 1; margin-left: 280px; padding: 1.5rem 2rem; }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: var(--white);
            padding: 1rem 1.5rem;
            border-radius: 16px;
        }
        .page-title h1 { font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 700; }
        .page-title p { font-size: 0.8rem; color: var(--text-mid); opacity: 0.7; }
        .top-bar-right { display: flex; align-items: center; gap: 1.5rem; }
        .logout-btn { background: none; border: 1px solid var(--gray-border); padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-size: 0.75rem; }
        .logout-btn:hover { background: var(--red-deep); border-color: var(--red-deep); color: var(--white); }
        .user-menu { display: flex; align-items: center; gap: 1rem; }
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
        
        /* Stats Cards */
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
        }
        .stat-value { font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 900; color: var(--red-deep); }
        .stat-label { font-size: 0.7rem; color: var(--text-mid); opacity: 0.7; }
        
        /* Submission Card */
        .submission-card {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid var(--gray-border);
            margin-bottom: 1rem;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .submission-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px var(--shadow); }
        .submission-header {
            background: var(--light);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--gray-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .submission-restroom { font-weight: 700; font-size: 1rem; }
        .submission-date { font-size: 0.75rem; color: var(--text-mid); }
        .status-badge {
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .status-pending { background: rgba(249,168,37,0.15); color: #b45309; }
        .status-approved { background: rgba(46,125,50,0.15); color: var(--green-success); }
        .status-flagged { background: rgba(197,0,0,0.15); color: var(--red-deep); }
        .submission-body { padding: 1rem 1.5rem; }
        .rating-display {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }
        .rating-stars { display: flex; gap: 0.2rem; }
        .star { font-size: 1rem; color: #ddd; }
        .star.filled { color: var(--yellow-warning); }
        .review-notes {
            background: var(--light);
            padding: 0.75rem;
            border-radius: 8px;
            margin-top: 0.75rem;
            font-size: 0.8rem;
        }
        .empty-state { text-align: center; padding: 3rem; color: var(--text-mid); opacity: 0.6; }
        .loading { text-align: center; padding: 3rem; color: var(--text-mid); }
        .btn-view {
            background: var(--light);
            color: var(--text-mid);
            border: 1px solid var(--gray-border);
            padding: 0.3rem 0.8rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-view:hover { background: var(--red-deep); color: var(--white); border-color: var(--red-deep); }
        
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
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
            .submission-header { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <div class="sidebar-icon"><svg viewBox="0 0 24 24"><path d="M12 2C12 2 6 9 6 14C6 17.3 8.7 20 12 20C15.3 20 18 17.3 18 14C18 9 12 2 12 2Z" fill="#C50000"/><path d="M9 13.5C9.5 12.5 10.7 12 12 12C13.3 12 14.5 12.5 15 13.5" stroke="#fff" stroke-width="1.4"/><circle cx="12" cy="18" r="1" fill="#fff"/></svg></div>
                <span>SmartWash</span>
            </div>
            <div class="sidebar-sub">BatStateU ARASOF-Nasugbu</div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-item"><a href="home.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>Dashboard</a></div>
            <div class="nav-item"><a href="restroom_status.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>My Restrooms</a></div>
            <div class="nav-item"><a href="checklist.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Checklist</a></div>
            <div class="nav-item"><a href="submissions.php" class="nav-link active"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>My Submissions</a></div>
            <div class="nav-item"><a href="notifications.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>Notifications</a></div>
        </nav>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="page-title"><h1>My Submissions</h1><p>View your checklist submission history and feedback</p></div>
            <div class="top-bar-right">
                <form method="POST" action="../../auth/logout.php"><button type="submit" class="logout-btn">Logout</button></form>
                <div class="user-menu"><div class="user-info"><div class="user-name"><?php echo htmlspecialchars($fullName); ?></div><div class="user-role">Maintenance Staff</div></div><div class="user-avatar"><?php echo htmlspecialchars($initials); ?></div></div>
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
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="pending">Pending</button>
            <button class="filter-btn" data-filter="approved">Approved</button>
            <button class="filter-btn" data-filter="flagged">Flagged</button>
        </div>

        <!-- Submissions List -->
        <div id="submissionsList"></div>
    </main>
</div>

<script>
// =============================================
// CONFIGURATION
// =============================================
const API_BASE = '../../api/';
const USER_ID = <?php echo json_encode($userId); ?>;
let currentFilter = 'all';
let allSubmissions = [];

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
// LOAD SUBMISSIONS FROM API
// =============================================
async function loadSubmissions() {
    const container = document.getElementById('submissionsList');
    container.innerHTML = '<div class="loading">Loading your submissions...</div>';
    
    try {
        const response = await fetch(`${API_BASE}get_user_checklists.php?user_id=${USER_ID}`);
        const result = await response.json();
        
        if (result.success && result.data) {
            allSubmissions = result.data;
            updateStats();
            displaySubmissions();
        } else {
            container.innerHTML = '<div class="empty-state">No submissions found</div>';
        }
    } catch (error) {
        console.error('Error:', error);
        container.innerHTML = '<div class="empty-state">Error loading submissions</div>';
        showToast('Error loading submissions', 'error');
    }
}

// =============================================
// DISPLAY SUBMISSIONS
// =============================================
function displaySubmissions() {
    const container = document.getElementById('submissionsList');
    let filtered = allSubmissions;
    
    if (currentFilter !== 'all') {
        filtered = allSubmissions.filter(s => s.status === currentFilter);
    }
    
    if (filtered.length === 0) {
        container.innerHTML = '<div class="empty-state">📋 No submissions found for this filter.</div>';
        return;
    }
    
    container.innerHTML = filtered.map(sub => {
        const autoRating = sub.auto_rating || 0;
        const supervisorRating = sub.supervisor_rating;
        
        return `
            <div class="submission-card">
                <div class="submission-header">
                    <div>
                        <span class="submission-restroom">🚻 ${escapeHtml(sub.restroom_name)}</span>
                        <div class="submission-date">📅 ${formatDate(sub.submitted_at)}</div>
                    </div>
                    <span class="status-badge status-${sub.status}">
                        ${sub.status === 'pending' ? '⏳ Pending Review' : sub.status === 'approved' ? '✓ Approved' : '⚠️ Flagged'}
                    </span>
                </div>
                <div class="submission-body">
                    <div class="rating-display">
                        <div class="rating-stars">
                            ${getStarRating(Math.floor(autoRating))}
                        </div>
                        <span>Auto Rating: ${autoRating}/5</span>
                    </div>
                    ${supervisorRating ? `
                        <div class="rating-display">
                            <div class="rating-stars">
                                ${getStarRating(supervisorRating)}
                            </div>
                            <span>Supervisor Rating: ${supervisorRating}/5</span>
                        </div>
                    ` : ''}
                    ${sub.review_notes && sub.status !== 'pending' ? `
                        <div class="review-notes">
                            <strong>📝 ${sub.status === 'approved' ? 'Feedback' : 'Reason for flagging'}:</strong><br>
                            ${escapeHtml(sub.review_notes)}
                        </div>
                    ` : ''}
                    <div style="margin-top: 0.75rem; text-align: right;">
                        <button class="btn-view" onclick="viewDetails(${sub.id})">View Full Details →</button>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// =============================================
// VIEW DETAILS (Modal or Alert)
// =============================================
async function viewDetails(id) {
    try {
        const response = await fetch(`${API_BASE}get_checklist_details.php?id=${id}`);
        const result = await response.json();
        
        if (result.success && result.data) {
            const c = result.data;
            const autoRating = c.rating || 0;
            const supervisorRating = c.supervisor_rating;
            
            let message = `📋 CHECKLIST DETAILS\n━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n`;
            message += `📍 Restroom: ${c.restroom_name}\n`;
            message += `📅 Submitted: ${formatDateTime(c.submitted_at)}\n`;
            message += `📊 Status: ${c.status.toUpperCase()}\n\n`;
            message += `━━━━━━━━━━━━━━━━━━━━━━━━━━\n`;
            message += `📝 TASKS COMPLETED:\n`;
            message += `  ${c.floor_clean ? '✅' : '❌'} Floor Clean\n`;
            message += `  ${c.toilets_clean ? '✅' : '❌'} Toilets Clean\n`;
            message += `  ${c.sinks_clean ? '✅' : '❌'} Sinks Clean\n`;
            message += `  ${c.mirrors_clean ? '✅' : '❌'} Mirrors Clean\n`;
            message += `  ${c.soap_refilled ? '✅' : '❌'} Soap Refilled\n`;
            message += `  ${c.trash_emptied ? '✅' : '❌'} Trash Emptied\n`;
            message += `  ${c.odor_free ? '✅' : '❌'} Odor Free\n\n`;
            message += `━━━━━━━━━━━━━━━━━━━━━━━━━━\n`;
            message += `⭐ Auto Rating: ${autoRating}/5\n`;
            
            if (supervisorRating) {
                message += `👑 Supervisor Rating: ${supervisorRating}/5\n`;
            }
            
            if (c.review_notes && c.status !== 'pending') {
                message += `\n📝 ${c.status === 'approved' ? 'Supervisor Feedback' : 'Flag Reason'}:\n   ${c.review_notes}\n`;
            }
            
            if (c.notes) {
                message += `\n📝 Your Notes:\n   ${c.notes}\n`;
            }
            
            message += `\n${c.status === 'approved' ? '✓ Good work! Keep it up!' : c.status === 'flagged' ? '⚠️ Please review and re-submit.' : '⏳ Waiting for supervisor review.'}`;
            
            alert(message);
        } else {
            showToast('Could not load details', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error loading details', 'error');
    }
}

// =============================================
// HELPER FUNCTIONS
// =============================================
function getStarRating(score) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += `<span class="star ${i <= score ? 'filled' : ''}">★</span>`;
    }
    return stars;
}

function updateStats() {
    const total = allSubmissions.length;
    const pending = allSubmissions.filter(s => s.status === 'pending').length;
    const approved = allSubmissions.filter(s => s.status === 'approved').length;
    const flagged = allSubmissions.filter(s => s.status === 'flagged').length;
    
    document.getElementById('totalCount').textContent = total;
    document.getElementById('pendingCount').textContent = pending;
    document.getElementById('approvedCount').textContent = approved;
    document.getElementById('flaggedCount').textContent = flagged;
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
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function formatDateTime(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}

// =============================================
// FILTER FUNCTION
// =============================================
function filterSubmissions(filter) {
    currentFilter = filter;
    
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-filter') === filter) {
            btn.classList.add('active');
        }
    });
    
    displaySubmissions();
}

// =============================================
// INITIALIZATION
// =============================================
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => filterSubmissions(btn.getAttribute('data-filter')));
});

document.addEventListener('DOMContentLoaded', () => {
    loadSubmissions();
});
</script>
</body>
</html>