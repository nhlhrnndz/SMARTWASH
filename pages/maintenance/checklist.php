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

// Fetch restrooms from database
require_once '../../config/db.php';
$pdo = getDB();
$stmt = $pdo->query("SELECT id, name FROM restrooms WHERE status = 'active' ORDER BY name");
$restrooms = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist — SmartWash</title>
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
        .logout-btn { background: none; border: 1px solid var(--gray-border); padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-size: 0.75rem; transition: all 0.2s; }
        .logout-btn:hover { background: var(--red-deep); border-color: var(--red-deep); color: var(--white); }
        .user-menu { display: flex; align-items: center; gap: 1rem; }
        .user-avatar { width: 44px; height: 44px; background: linear-gradient(135deg, var(--red-deep), var(--red-vivid)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 1.1rem; }
        .user-info { text-align: right; }
        .user-name { font-weight: 700; font-size: 0.88rem; }
        .user-role { font-size: 0.7rem; opacity: 0.6; }
        
        .checklist-container { background: var(--white); border-radius: 16px; border: 1px solid var(--gray-border); padding: 1.5rem; margin-bottom: 1.5rem; }
        .checklist-header { margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--gray-border); }
        .checklist-title { font-family: 'Playfair Display', serif; font-size: 1.3rem; font-weight: 700; margin-bottom: 0.3rem; }
        .checklist-sub { font-size: 0.8rem; color: var(--text-mid); opacity: 0.7; }
        .form-group { margin-bottom: 1.2rem; }
        label { display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-mid); }
        select, textarea { width: 100%; padding: 0.7rem; border: 1px solid var(--gray-border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; background: var(--white); }
        select:focus, textarea:focus { outline: none; border-color: var(--red-deep); box-shadow: 0 0 0 3px rgba(197,0,0,0.1); }
        .rating-group { display: flex; gap: 1rem; flex-wrap: wrap; }
        .rating-option { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
        .rating-option input { width: 18px; height: 18px; cursor: pointer; }
        .btn-submit { background: var(--red-deep); color: var(--white); border: none; padding: 0.8rem 1.5rem; border-radius: 10px; font-weight: 700; cursor: pointer; width: 100%; font-size: 1rem; transition: all 0.2s; }
        .btn-submit:hover { background: var(--red-vivid); transform: translateY(-1px); }
        .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
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
        .toast.info { background: #1565C0; }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
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
            <div class="nav-item"><a href="checklist.php" class="nav-link active"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Checklist</a></div>
            <div class="nav-item"><a href="submissions.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>Submissions</a></div>
            <div class="nav-item"><a href="notifications.php" class="nav-link"><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>Notifications</a></div>
        </nav>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="page-title"><h1>Cleanliness Evaluation Form</h1><p>Submit daily checklist for your assigned restrooms</p></div>
            <div class="top-bar-right">
                <form method="POST" action="../../auth/logout.php" style="margin:0;"><button type="submit" class="logout-btn">Logout</button></form>
                <div class="user-menu"><div class="user-info"><div class="user-name"><?php echo htmlspecialchars($fullName); ?></div><div class="user-role">Maintenance Staff</div></div><div class="user-avatar"><?php echo htmlspecialchars($initials); ?></div></div>
            </div>
        </div>

        <div class="checklist-container">
            <div class="checklist-header">
                <div class="checklist-title">📋 Daily Restroom Inspection</div>
                <div class="checklist-sub">Complete this form after inspecting each assigned restroom</div>
            </div>
            
            <form id="checklistForm">
                <div class="form-group">
    <label>Select Restroom *</label>
    <select id="restroomSelect" required>
        <option value="">-- Select Restroom --</option>
        <?php foreach ($restrooms as $restroom): ?>
            <option value="<?php echo $restroom['id']; ?>">
                <?php echo htmlspecialchars($restroom['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
                
                <div class="form-group">
                    <label>Toilet Cleanliness</label>
                    <div class="rating-group">
                        <label class="rating-option"><input type="radio" name="toilet" value="excellent"> Excellent</label>
                        <label class="rating-option"><input type="radio" name="toilet" value="good"> Good</label>
                        <label class="rating-option"><input type="radio" name="toilet" value="fair"> Fair</label>
                        <label class="rating-option"><input type="radio" name="toilet" value="poor"> Poor</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Floor Cleanliness</label>
                    <div class="rating-group">
                        <label class="rating-option"><input type="radio" name="floor" value="excellent"> Excellent</label>
                        <label class="rating-option"><input type="radio" name="floor" value="good"> Good</label>
                        <label class="rating-option"><input type="radio" name="floor" value="fair"> Fair</label>
                        <label class="rating-option"><input type="radio" name="floor" value="poor"> Poor</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Sink Cleanliness</label>
                    <div class="rating-group">
                        <label class="rating-option"><input type="radio" name="sink" value="excellent"> Excellent</label>
                        <label class="rating-option"><input type="radio" name="sink" value="good"> Good</label>
                        <label class="rating-option"><input type="radio" name="sink" value="fair"> Fair</label>
                        <label class="rating-option"><input type="radio" name="sink" value="poor"> Poor</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Mirror Cleanliness</label>
                    <div class="rating-group">
                        <label class="rating-option"><input type="radio" name="mirror" value="excellent"> Excellent</label>
                        <label class="rating-option"><input type="radio" name="mirror" value="good"> Good</label>
                        <label class="rating-option"><input type="radio" name="mirror" value="fair"> Fair</label>
                        <label class="rating-option"><input type="radio" name="mirror" value="poor"> Poor</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Soap Dispenser Status</label>
                    <div class="rating-group">
                        <label class="rating-option"><input type="radio" name="soap" value="full"> Full</label>
                        <label class="rating-option"><input type="radio" name="soap" value="half"> Half Full</label>
                        <label class="rating-option"><input type="radio" name="soap" value="low"> Low - Needs Refill</label>
                        <label class="rating-option"><input type="radio" name="soap" value="empty"> Empty</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Paper Towel / Tissue Status</label>
                    <div class="rating-group">
                        <label class="rating-option"><input type="radio" name="towel" value="full"> Full</label>
                        <label class="rating-option"><input type="radio" name="towel" value="half"> Half Full</label>
                        <label class="rating-option"><input type="radio" name="towel" value="low"> Low - Needs Refill</label>
                        <label class="rating-option"><input type="radio" name="towel" value="empty"> Empty</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Odor Free</label>
                    <div class="rating-group">
                        <label class="rating-option"><input type="radio" name="odor" value="yes"> Yes, odor free</label>
                        <label class="rating-option"><input type="radio" name="odor" value="no"> No, there is odor</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Additional Notes / Issues Found</label>
                    <textarea id="notes" rows="3" placeholder="Describe any issues, damages, or special concerns..."></textarea>
                </div>
                
                <button type="submit" class="btn-submit" id="submitBtn">✓ Submit Checklist</button>
            </form>
        </div>
    </main>
</div>

<script>
// Get user ID from PHP
const USER_ID = <?php echo json_encode($user['id']); ?>;

// Helper function to show toast notifications
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Helper function to get selected radio value
function getSelectedValue(name) {
    const selected = document.querySelector(`input[name="${name}"]:checked`);
    return selected ? selected.value : null;
}

// Form submission handler
document.getElementById('checklistForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Get form data
    const restroomSelect = document.getElementById('restroomSelect');
    const restroomId = restroomSelect.value;
    const restroomName = restroomSelect.options[restroomSelect.selectedIndex]?.text;
    
    if (!restroomId || restroomId === '') {
        showToast('Please select a restroom', 'error');
        return;
    }
    
    // Get all ratings
    const toilet = getSelectedValue('toilet');
    const floor = getSelectedValue('floor');
    const sink = getSelectedValue('sink');
    const mirror = getSelectedValue('mirror');
    const soap = getSelectedValue('soap');
    const towel = getSelectedValue('towel');
    const odor = getSelectedValue('odor');
    
    // Validate required fields
    if (!toilet || !floor || !sink || !mirror || !soap || !towel || !odor) {
        showToast('Please complete all rating fields before submitting', 'error');
        return;
    }
    
    // Convert to boolean (1/0)
    const isExcellentOrGood = (value) => value === 'excellent' || value === 'good';
    const isFull = (value) => value === 'full';
    
    const payload = {
        user_id: USER_ID,
        restroom_id: parseInt(restroomId),
        floor_clean: isExcellentOrGood(floor) ? 1 : 0,
        toilets_clean: isExcellentOrGood(toilet) ? 1 : 0,
        sinks_clean: isExcellentOrGood(sink) ? 1 : 0,
        mirrors_clean: isExcellentOrGood(mirror) ? 1 : 0,
        soap_refilled: isFull(soap) ? 1 : 0,
        trash_emptied: isFull(towel) ? 1 : 0,
        odor_free: odor === 'yes' ? 1 : 0,
        notes: document.getElementById('notes')?.value || ''
    };
    
    console.log('Submitting payload:', payload);
    
    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = '⏳ Submitting...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('../../api/submit_checklist.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('✓ Checklist submitted successfully! Waiting for supervisor approval.', 'success');
            document.getElementById('checklistForm').reset();
        } else {
            showToast('Error: ' + (result.error || 'Failed to submit checklist'), 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Network error. Failed to submit checklist. Please try again.', 'error');
    } finally {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }
});
</script>
</body>
</html>