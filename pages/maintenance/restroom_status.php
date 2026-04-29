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
    <title>My Restrooms — SmartWash</title>
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
        
        /* Stats Card */
        .stats-card {
            background: var(--white);
            border-radius: 16px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--gray-border);
            display: inline-block;
        }
        .stats-value { font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 900; color: var(--red-deep); }
        
        /* Restrooms Grid */
        .restrooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.2rem;
        }
        .restroom-card {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid var(--gray-border);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .restroom-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px var(--shadow);
        }
        .restroom-card.critical {
            background: rgba(197,0,0,0.02);
            border-left: 4px solid var(--red-deep);
        }
        .restroom-card.warning {
            border-left: 4px solid var(--yellow-warning);
        }
        .restroom-header {
            background: var(--light);
            padding: 1rem 1.2rem;
            border-bottom: 1px solid var(--gray-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .restroom-name {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1rem;
        }
        .restroom-location {
            font-size: 0.7rem;
            color: var(--text-mid);
            opacity: 0.7;
            margin-top: 0.2rem;
        }
        .restroom-gender {
            background: var(--light);
            padding: 0.2rem 0.5rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }
        .restroom-gender.male { background: rgba(21,101,192,0.15); color: var(--blue-info); }
        .restroom-gender.female { background: rgba(197,0,0,0.15); color: var(--red-deep); }
        .restroom-gender.unisex { background: rgba(46,125,50,0.15); color: var(--green-success); }
        
        .restroom-body {
            padding: 1rem 1.2rem;
        }
        .sensor-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--gray-border);
        }
        .sensor-row:last-child {
            border-bottom: none;
        }
        .sensor-label {
            font-weight: 600;
            font-size: 0.75rem;
            color: var(--text-mid);
        }
        .sensor-value {
            font-size: 0.85rem;
            font-weight: 700;
        }
        .sensor-value.good { color: var(--green-success); }
        .sensor-value.warning { color: var(--yellow-warning); }
        .sensor-value.critical { color: var(--red-deep); }
        
        .progress-bar {
            background: var(--light);
            border-radius: 10px;
            height: 6px;
            overflow: hidden;
            margin-top: 0.3rem;
        }
        .progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s;
        }
        .fill-good { background: var(--green-success); }
        .fill-warning { background: var(--yellow-warning); }
        .fill-critical { background: var(--red-deep); }
        
        .alert-badge {
            background: rgba(197,0,0,0.15);
            color: var(--red-deep);
            padding: 0.2rem 0.5rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .btn-checklist {
            background: var(--red-deep);
            color: var(--white);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 600;
            width: 100%;
            margin-top: 0.8rem;
            transition: all 0.2s;
        }
        .btn-checklist:hover {
            background: var(--red-vivid);
            transform: scale(1.02);
        }
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
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
            .restrooms-grid { grid-template-columns: 1fr; }
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
            <div class="nav-item"><a href="restroom_status.php" class="nav-link active"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>My Restrooms</a></div>
            <div class="nav-item"><a href="checklist.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Checklist</a></div>
            <div class="nav-item"><a href="submissions.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Submissions</a></div>
            <div class="nav-item"><a href="notifications.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>Notifications</a></div>
        </nav>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="page-title"><h1>My Assigned Restrooms</h1><p>Restrooms under your responsibility</p></div>
            <div class="top-bar-right">
                <form method="POST" action="../../auth/logout.php"><button type="submit" class="logout-btn">Logout</button></form>
                <div class="user-menu"><div class="user-info"><div class="user-name"><?php echo htmlspecialchars($fullName); ?></div><div class="user-role">Maintenance Staff</div></div><div class="user-avatar"><?php echo htmlspecialchars($initials); ?></div></div>
            </div>
        </div>

        <div class="stats-card">
            <span class="stats-value" id="restroomCount">0</span> Restrooms Assigned
        </div>

        <div class="restrooms-grid" id="restroomsGrid">
            <div class="loading">Loading assigned restrooms...</div>
        </div>
    </main>
</div>

<script>
// =============================================
// CONFIGURATION
// =============================================
const API_BASE = '../../api/';
const USER_ID = <?php echo json_encode($userId); ?>;

// =============================================
// HELPER FUNCTIONS
// =============================================
function getSoapStatus(level) {
    if (!level && level !== 0) return 'good';
    if (level < 20) return 'critical';
    if (level < 50) return 'warning';
    return 'good';
}

function getAirStatus(aqi) {
    if (!aqi && aqi !== 0) return 'good';
    if (aqi > 70) return 'critical';
    if (aqi > 40) return 'warning';
    return 'good';
}

function getSoapFillClass(level) {
    if (!level && level !== 0) return 'fill-good';
    if (level < 20) return 'fill-critical';
    if (level < 50) return 'fill-warning';
    return 'fill-good';
}

function getCardStatus(hasAlert, soapLevel, airQuality) {
    if (hasAlert) return 'critical';
    if (soapLevel < 20 || airQuality > 70) return 'critical';
    if (soapLevel < 50 || airQuality > 40) return 'warning';
    return 'good';
}

function getGenderIcon(gender) {
    switch(gender) {
        case 'male': return '👨';
        case 'female': return '👩';
        default: return '👥';
    }
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// =============================================
// LOAD ASSIGNED RESTROOMS
// =============================================
async function loadAssignedRestrooms() {
    const container = document.getElementById('restroomsGrid');
    container.innerHTML = '<div class="loading">Loading assigned restrooms...</div>';
    
    try {
        const response = await fetch(`${API_BASE}get_assigned_restrooms.php?user_id=${USER_ID}`);
        const result = await response.json();
        
        if (result.success && result.data && result.data.length > 0) {
            document.getElementById('restroomCount').textContent = result.data.length;
            displayRestrooms(result.data);
        } else {
            container.innerHTML = '<div class="empty-state">🚻 No restrooms assigned yet.<br><br>Please contact your supervisor for assignment.</div>';
            document.getElementById('restroomCount').textContent = '0';
        }
    } catch (error) {
        console.error('Error:', error);
        container.innerHTML = '<div class="empty-state">Error loading restrooms. Please refresh the page.</div>';
    }
}

// =============================================
// DISPLAY RESTROOMS
// =============================================
function displayRestrooms(restrooms) {
    const container = document.getElementById('restroomsGrid');
    
    container.innerHTML = restrooms.map(restroom => {
        const soapLevel = restroom.soap_level ?? 100;
        const airQuality = restroom.air_quality ?? 0;
        const hasAlert = restroom.has_alert == 1;
        const cardStatus = getCardStatus(hasAlert, soapLevel, airQuality);
        
        return `
            <div class="restroom-card ${cardStatus}">
                <div class="restroom-header">
                    <div>
                        <div class="restroom-name">${escapeHtml(restroom.name)}</div>
                        <div class="restroom-location">📍 ${escapeHtml(restroom.location)}</div>
                    </div>
                    <span class="restroom-gender ${restroom.gender}">${getGenderIcon(restroom.gender)} ${escapeHtml(restroom.gender)}</span>
                </div>
                <div class="restroom-body">
                    ${hasAlert ? `
                        <div class="sensor-row">
                            <span class="alert-badge">⚠️ Active Alert - Needs Attention</span>
                        </div>
                    ` : ''}
                    <div class="sensor-row">
                        <span class="sensor-label">🧴 Soap Level</span>
                        <span class="sensor-value ${getSoapStatus(soapLevel)}">${soapLevel}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill ${getSoapFillClass(soapLevel)}" style="width: ${soapLevel}%"></div>
                    </div>
                    <div class="sensor-row">
                        <span class="sensor-label">🌬️ Air Quality (AQI)</span>
                        <span class="sensor-value ${getAirStatus(airQuality)}">${airQuality}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill ${getSoapFillClass(airQuality > 70 ? 0 : (airQuality > 40 ? 50 : 100))}" style="width: ${Math.min(airQuality, 100)}%"></div>
                    </div>
                    <button class="btn-checklist" onclick="startChecklist(${restroom.id}, '${escapeHtml(restroom.name)}')">
                        📋 Submit Checklist
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

// =============================================
// START CHECKLIST
// =============================================
function startChecklist(restroomId, restroomName) {
    if (confirm(`Start checklist for ${restroomName}?`)) {
        window.location.href = `checklist.php?restroom_id=${restroomId}`;
    }
}

// =============================================
// INITIALIZATION
// =============================================
document.addEventListener('DOMContentLoaded', () => {
    loadAssignedRestrooms();
    
    // Auto-refresh every 30 seconds
    setInterval(loadAssignedRestrooms, 30000);
});
</script>
</body>
</html>