<?php
require_once '../../auth/session.php';
requireRole(['maintenance']);

$user = currentUser();
$fullName = $user['full_name'];
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
    <title>Notifications — SmartWash</title>
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
        
        .notification-filters {
            display: flex;
            gap: 0.5rem;
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
            transition: all 0.2s;
        }
        .filter-btn.active { background: var(--red-deep); color: var(--white); border-color: var(--red-deep); }
        
        .notification-item {
            background: var(--white);
            border-radius: 12px;
            border: 1px solid var(--gray-border);
            padding: 1rem 1.5rem;
            margin-bottom: 0.8rem;
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            transition: all 0.2s;
        }
        .notification-item.unread { background: rgba(197,0,0,0.03); border-left: 3px solid var(--red-deep); }
        .notification-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.2rem; }
        .notification-icon.alert { background: rgba(197,0,0,0.1); }
        .notification-icon.info { background: rgba(21,101,192,0.1); }
        .notification-icon.success { background: rgba(46,125,50,0.1); }
        .notification-content { flex: 1; }
        .notification-title { font-weight: 700; font-size: 0.9rem; margin-bottom: 0.2rem; }
        .notification-message { font-size: 0.8rem; color: var(--text-mid); margin-bottom: 0.3rem; white-space: pre-line; }
        .notification-time { font-size: 0.65rem; color: var(--text-mid); opacity: 0.6; }
        .mark-read-btn { background: none; border: none; color: var(--blue-info); font-size: 0.7rem; cursor: pointer; margin-top: 0.3rem; }
        .empty-state { text-align: center; padding: 3rem; color: var(--text-mid); opacity: 0.6; }
        .loading { text-align: center; padding: 3rem; color: var(--text-mid); }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
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
            <div class="nav-item"><a href="submissions.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Submissions</a></div>
            <div class="nav-item"><a href="notifications.php" class="nav-link active"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>Notifications</a></div>
        </nav>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="page-title"><h1>Notifications</h1><p>Updates and alerts from your supervisor</p></div>
            <div class="top-bar-right">
                <form method="POST" action="../../auth/logout.php"><button type="submit" class="logout-btn">Logout</button></form>
                <div class="user-menu"><div class="user-info"><div class="user-name"><?php echo htmlspecialchars($fullName); ?></div><div class="user-role">Maintenance Staff</div></div><div class="user-avatar"><?php echo htmlspecialchars($initials); ?></div></div>
            </div>
        </div>

        <div class="notification-filters">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="unread">Unread</button>
        </div>

        <div id="notificationsList"><div class="loading">Loading notifications...</div></div>
    </main>
</div>

<script>
let notifications = [];
let currentFilter = 'all';

async function loadNotifications() {
    const container = document.getElementById('notificationsList');
    container.innerHTML = '<div class="loading">Loading notifications...</div>';
    
    try {
        const response = await fetch('../../api/get_notifications.php?limit=50');
        const result = await response.json();
        
        if (result.success) {
            notifications = result.data.notifications;
            displayNotifications();
        } else {
            container.innerHTML = '<div class="empty-state">Error loading notifications</div>';
        }
    } catch (error) {
        console.error('Error:', error);
        container.innerHTML = '<div class="empty-state">Error loading notifications</div>';
    }
}

function displayNotifications() {
    const container = document.getElementById('notificationsList');
    let filtered = notifications;
    
    if (currentFilter === 'unread') {
        filtered = notifications.filter(n => !n.is_read);
    }
    
    if (filtered.length === 0) {
        container.innerHTML = '<div class="empty-state">📭 No notifications</div>';
        return;
    }
    
    container.innerHTML = filtered.map(n => `
        <div class="notification-item ${!n.is_read ? 'unread' : ''}" data-id="${n.id}">
            <div class="notification-icon ${getIconType(n.type)}">${getIcon(n.type)}</div>
            <div class="notification-content">
                <div class="notification-title">${escapeHtml(n.title)}</div>
                <div class="notification-message">${escapeHtml(n.message).replace(/\n/g, '<br>')}</div>
                <div class="notification-time">${formatTime(n.created_at)}</div>
                ${!n.is_read ? `<button class="mark-read-btn" onclick="markAsRead(${n.id})">Mark as read</button>` : ''}
            </div>
        </div>
    `).join('');
}

function getIcon(type) {
    const icons = {
        'checklist_submitted': '📋',
        'checklist_reviewed': '✓',
        'alert': '⚠️',
        'soap_low': '🧴',
        'vape_detected': '🚭',
        'system': '🔧'
    };
    return icons[type] || '📢';
}

function getIconType(type) {
    if (type === 'checklist_reviewed') return 'success';
    if (type === 'alert' || type === 'vape_detected') return 'alert';
    return 'info';
}

async function markAsRead(id) {
    try {
        const response = await fetch('../../api/mark_notification_read.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ notification_id: id })
        });
        const result = await response.json();
        if (result.success) {
            loadNotifications();
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function formatTime(timestamp) {
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

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function filterNotifications(filter) {
    currentFilter = filter;
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    if (event && event.target) event.target.classList.add('active');
    displayNotifications();
}

document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        currentFilter = this.getAttribute('data-filter');
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        displayNotifications();
    });
});

loadNotifications();
setInterval(loadNotifications, 30000);
</script>
</body>
</html>