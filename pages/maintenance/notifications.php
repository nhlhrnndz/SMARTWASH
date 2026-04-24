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
            --gray-border: #e0d8c8;
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
        
        .notification-filters { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .filter-btn { padding: 0.4rem 1rem; border-radius: 20px; border: 1px solid var(--gray-border); background: var(--white); cursor: pointer; font-size: 0.75rem; transition: all 0.2s; }
        .filter-btn.active { background: var(--red-deep); color: var(--white); border-color: var(--red-deep); }
        .notification-item { background: var(--white); border-radius: 12px; border: 1px solid var(--gray-border); padding: 1rem 1.5rem; margin-bottom: 0.8rem; display: flex; gap: 1rem; align-items: flex-start; transition: all 0.2s; }
        .notification-item.unread { background: rgba(197,0,0,0.03); border-left: 3px solid var(--red-deep); }
        .notification-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .notification-icon.alert { background: rgba(197,0,0,0.1); }
        .notification-icon.info { background: rgba(21,101,192,0.1); }
        .notification-icon.success { background: rgba(46,125,50,0.1); }
        .notification-content { flex: 1; }
        .notification-title { font-weight: 700; font-size: 0.9rem; margin-bottom: 0.2rem; }
        .notification-message { font-size: 0.8rem; color: var(--text-mid); margin-bottom: 0.3rem; }
        .notification-time { font-size: 0.65rem; color: var(--text-mid); opacity: 0.6; }
        .notification-action { display: flex; gap: 0.5rem; margin-top: 0.5rem; }
        .action-link { font-size: 0.7rem; color: var(--red-deep); text-decoration: none; font-weight: 600; cursor: pointer; }
        .empty-state { text-align: center; padding: 3rem; color: var(--text-mid); opacity: 0.6; }
        .mark-all { text-align: right; margin-bottom: 1rem; }
        .mark-all-btn { background: none; border: none; color: var(--red-deep); font-size: 0.75rem; cursor: pointer; font-weight: 600; }
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
            <div class="page-title"><h1>Notifications & Alerts</h1><p>Stay updated on alerts, feedback, and reminders</p></div>
            <div class="top-bar-right">
                <form method="POST" action="../../auth/logout.php" style="margin:0;"><button type="submit" class="logout-btn">Logout</button></form>
                <div class="user-menu"><div class="user-info"><div class="user-name"><?php echo htmlspecialchars($fullName); ?></div><div class="user-role">Maintenance Staff</div></div><div class="user-avatar"><?php echo htmlspecialchars($initials); ?></div></div>
            </div>
        </div>

        <div class="notification-filters">
            <button class="filter-btn active" onclick="filterNotifications('all')">All</button>
            <button class="filter-btn" onclick="filterNotifications('unread')">Unread</button>
            <button class="filter-btn" onclick="filterNotifications('alert')">Alerts</button>
            <button class="filter-btn" onclick="filterNotifications('info')">Updates</button>
        </div>
        
        <div class="mark-all"><button class="mark-all-btn" onclick="markAllRead()">✓ Mark all as read</button></div>
        
        <div id="notificationsList">
            <!-- Static demo notifications -->
            <div class="notification-item unread" data-type="alert" data-read="false">
                <div class="notification-icon alert">⚠️</div>
                <div class="notification-content">
                    <div class="notification-title">Critical: Soap Level Low</div>
                    <div class="notification-message">1F-FR - 1st Floor Front soap dispenser is at 12%. Please refill immediately.</div>
                    <div class="notification-time">30 minutes ago</div>
                    <div class="notification-action"><a class="action-link" href="restroom_status.php">View Restroom →</a> <a class="action-link" onclick="markAsRead(this)">Mark as read</a></div>
                </div>
            </div>
            
            <div class="notification-item unread" data-type="alert" data-read="false">
                <div class="notification-icon alert">🚭</div>
                <div class="notification-content">
                    <div class="notification-title">Vape Detected</div>
                    <div class="notification-message">GLR2 - Ground Right vape sensor triggered at 08:15 AM. Please inspect the area.</div>
                    <div class="notification-time">2 hours ago</div>
                    <div class="notification-action"><a class="action-link" href="restroom_status.php">View Restroom →</a> <a class="action-link" onclick="markAsRead(this)">Mark as read</a></div>
                </div>
            </div>
            
            <div class="notification-item" data-type="info" data-read="true">
                <div class="notification-icon info">📋</div>
                <div class="notification-content">
                    <div class="notification-title">Checklist Approved</div>
                    <div class="notification-message">Your submission for GLR1 - Ground Left has been approved by Supervisor.</div>
                    <div class="notification-time">Yesterday at 4:30 PM</div>
                </div>
            </div>
            
            <div class="notification-item" data-type="alert" data-read="true">
                <div class="notification-icon alert">🌬️</div>
                <div class="notification-content">
                    <div class="notification-title">Air Quality Alert</div>
                    <div class="notification-message">2F-MR - 2nd Floor Main AQI reached 85 (Unhealthy). Check ventilation system.</div>
                    <div class="notification-time">Yesterday at 10:15 AM</div>
                </div>
            </div>
            
            <div class="notification-item" data-type="info" data-read="true">
                <div class="notification-icon success">✓</div>
                <div class="notification-content">
                    <div class="notification-title">Checklist Flagged for Review</div>
                    <div class="notification-message">Your submission for 1F-FR - 1st Floor Front requires re-inspection. Please check supervisor feedback.</div>
                    <div class="notification-time">Apr 22, 2026 at 2:00 PM</div>
                    <div class="notification-action"><a class="action-link" href="submissions.php">View Submission →</a></div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function filterNotifications(type) {
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    const items = document.querySelectorAll('.notification-item');
    items.forEach(item => {
        if (type === 'all') {
            item.style.display = 'flex';
        } else if (type === 'unread') {
            item.style.display = item.getAttribute('data-read') === 'false' ? 'flex' : 'none';
        } else {
            item.style.display = item.getAttribute('data-type') === type ? 'flex' : 'none';
        }
    });
}

function markAsRead(element) {
    const item = element.closest('.notification-item');
    item.setAttribute('data-read', 'true');
    item.classList.remove('unread');
    const actionDiv = item.querySelector('.notification-action');
    if (actionDiv) {
        const markLink = actionDiv.querySelector('.action-link:last-child');
        if (markLink && markLink.textContent === 'Mark as read') {
            markLink.remove();
        }
    }
}

function markAllRead() {
    const items = document.querySelectorAll('.notification-item');
    items.forEach(item => {
        item.setAttribute('data-read', 'true');
        item.classList.remove('unread');
        const actionDiv = item.querySelector('.notification-action');
        if (actionDiv) {
            const markLink = actionDiv.querySelector('.action-link:last-child');
            if (markLink && markLink.textContent === 'Mark as read') {
                markLink.remove();
            }
        }
    });
    alert('All notifications marked as read (Demo mode)');
}
</script>
</body>
</html>