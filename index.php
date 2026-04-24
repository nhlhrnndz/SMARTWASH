<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if (in_array($_SESSION['role'], ['supervisor', 'admin'])) {
        header('Location: pages/supervisor/dashboard.php');
    } else {
        header('Location: pages/maintenance/home.php');
    }
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/db.php';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? '';

    if ($username && $password && $role) {
        $pdo    = getDB();
        $dbRole = ($role === 'supervisor') ? 'supervisor' : 'staff';
        $stmt   = $pdo->prepare("SELECT * FROM users WHERE username = ? AND (role = ? OR role = 'admin') LIMIT 1");
        $stmt->execute([$username, $dbRole]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];
            header(in_array($user['role'], ['supervisor','admin'])
                ? 'Location: pages/supervisor/dashboard.php'
                : 'Location: pages/maintenance/home.php');
            exit;
        } else {
            $error = '1';
        }
    } else {
        $error = '2';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SmartWash — BatStateU ARASOF-Nasugbu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --red-deep:  #C50000;
      --red-vivid: #E70000;
      --cream:     #FFFDEF;
      --light:     #F1F1F1;
      --white:     #ffffff;
      --text-dark: #1a0000;
      --text-mid:  #4a1010;
      --shadow:    rgba(197,0,0,0.18);
    }
    html, body { height: 100%; font-family: 'DM Sans', sans-serif; background: var(--cream); }

    /* VIEW SYSTEM */
    .view { display: none; }
    .view.active { display: block; }

    /* SPLIT LAYOUT */
    .page { display: grid; grid-template-columns: 1fr 1fr; min-height: 100vh; }

    /* LEFT */
    .left {
      position: relative; background: var(--red-deep);
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      padding: 3rem; overflow: hidden;
    }
    .left::before {
      content: ''; position: absolute; top: -120px; right: -120px;
      width: 380px; height: 380px; border-radius: 50%;
      background: rgba(255,255,255,0.05);
    }
    .left::after {
      content: ''; position: absolute; bottom: -80px; left: -80px;
      width: 280px; height: 280px; border-radius: 50%;
      background: rgba(231,0,0,0.4);
    }
    .left-inner { position: relative; z-index: 1; text-align: center; animation: fadeUp 0.9s ease both; }

    .seal-ring {
      width: 110px; height: 110px; border-radius: 50%;
      border: 3px solid rgba(255,255,255,0.4);
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 1.4rem; background: rgba(255,255,255,0.08);
      backdrop-filter: blur(4px);
    }
    .seal-ring svg { width: 68px; height: 68px; fill: none; }
    .batstateu-label { font-size: 0.65rem; font-weight: 600; letter-spacing: 0.25em; text-transform: uppercase; color: rgba(255,255,255,0.65); margin-bottom: 0.35rem; }
    .univ-name { font-family: 'Playfair Display', serif; font-size: 1.35rem; font-weight: 700; color: var(--white); line-height: 1.25; margin-bottom: 0.25rem; }
    .campus-name { font-size: 0.72rem; color: rgba(255,255,255,0.55); letter-spacing: 0.06em; margin-bottom: 2.5rem; }
    .brand-divider { width: 48px; height: 2px; background: rgba(255,255,255,0.3); margin: 0 auto 2rem; border-radius: 99px; }
    .sw-logo { display: flex; align-items: center; justify-content: center; gap: 0.65rem; margin-bottom: 0.8rem; }
    .sw-icon { width: 42px; height: 42px; background: var(--white); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 20px rgba(0,0,0,0.2); }
    .sw-icon svg { width: 24px; height: 24px; }
    .sw-name { font-family: 'Playfair Display', serif; font-size: 2.2rem; font-weight: 900; color: var(--white); }
    .sw-tagline { font-size: 0.75rem; color: rgba(255,255,255,0.6); font-weight: 300; line-height: 1.6; max-width: 280px; margin: 0 auto; }
    .features { display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: center; margin-top: 2rem; }
    .feat-pill { background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 99px; padding: 0.3rem 0.85rem; font-size: 0.65rem; color: rgba(255,255,255,0.8); font-weight: 500; }

    /* RIGHT */
    .right {
      background: var(--cream); display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      padding: 3rem 3.5rem; position: relative; overflow-y: auto;
    }
    .right::before {
      content: ''; position: absolute; inset: 0;
      background-image: linear-gradient(rgba(197,0,0,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(197,0,0,0.04) 1px, transparent 1px);
      background-size: 32px 32px; pointer-events: none;
    }

    /* CARD */
    .card { position: relative; width: 100%; max-width: 400px; animation: fadeUp 0.9s 0.15s ease both; }
    .card-heading { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.4rem; }
    .card-sub { font-size: 0.82rem; color: var(--text-mid); margin-bottom: 2rem; }

    /* ROLE TABS */
    .role-tabs { display: flex; background: var(--light); border-radius: 10px; padding: 4px; margin-bottom: 1.8rem; gap: 4px; }
    .role-tab { flex: 1; padding: 0.5rem; border: none; border-radius: 7px; background: transparent; font-family: 'DM Sans', sans-serif; font-size: 0.78rem; font-weight: 500; color: #888; cursor: pointer; transition: all 0.22s; }
    .role-tab.active { background: var(--white); color: var(--red-deep); font-weight: 600; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }

    /* FIELDS */
    .field { margin-bottom: 1.1rem; }
    label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-mid); letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 0.45rem; }
    .input-wrap { position: relative; display: flex; align-items: center; }
    .input-icon { position: absolute; left: 14px; width: 17px; height: 17px; color: #bbb; pointer-events: none; transition: color 0.2s; }
    .input-wrap:focus-within .input-icon { color: var(--red-deep); }
    input[type="text"], input[type="password"], input[type="email"] {
      width: 100%; padding: 0.75rem 1rem 0.75rem 2.75rem;
      border: 1.5px solid #e0d8c8; border-radius: 10px;
      background: var(--white); font-family: 'DM Sans', sans-serif;
      font-size: 0.88rem; color: var(--text-dark); outline: none;
      transition: border-color 0.22s, box-shadow 0.22s;
    }
    input:focus { border-color: var(--red-deep); box-shadow: 0 0 0 3px rgba(197,0,0,0.1); }
    select.field-select {
      width: 100%; padding: 0.75rem 1rem 0.75rem 2.75rem;
      border: 1.5px solid #e0d8c8; border-radius: 10px;
      background: var(--white); font-family: 'DM Sans', sans-serif;
      font-size: 0.88rem; color: var(--text-dark); outline: none;
      appearance: none; transition: border-color 0.22s, box-shadow 0.22s;
    }
    select.field-select:focus { border-color: var(--red-deep); box-shadow: 0 0 0 3px rgba(197,0,0,0.1); }
    .pw-toggle { position: absolute; right: 13px; background: none; border: none; cursor: pointer; color: #bbb; display: flex; align-items: center; padding: 0; transition: color 0.2s; }
    .pw-toggle:hover { color: var(--red-deep); }
    .pw-toggle svg { width: 17px; height: 17px; }

    /* FORM FOOTER ROW */
    .form-footer-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .remember { display: flex; align-items: center; gap: 0.45rem; font-size: 0.78rem; color: var(--text-mid); cursor: pointer; }
    .remember input[type="checkbox"] { width: 15px; height: 15px; accent-color: var(--red-deep); }
    .link-btn { font-size: 0.78rem; color: var(--red-deep); background: none; border: none; cursor: pointer; font-weight: 500; transition: opacity 0.2s; }
    .link-btn:hover { opacity: 0.7; }

    /* BUTTONS */
    .btn-primary {
      width: 100%; padding: 0.85rem; background: var(--red-deep); color: var(--white);
      border: none; border-radius: 10px; font-family: 'Playfair Display', serif;
      font-size: 1rem; font-weight: 700; letter-spacing: 0.03em; cursor: pointer;
      position: relative; overflow: hidden;
      transition: background 0.22s, transform 0.15s, box-shadow 0.22s;
      box-shadow: 0 4px 18px var(--shadow);
    }
    .btn-primary::after { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, transparent 60%); pointer-events: none; }
    .btn-primary:hover { background: var(--red-vivid); transform: translateY(-1px); box-shadow: 0 8px 24px var(--shadow); }
    .btn-primary:active { transform: translateY(0); }
    .btn-ghost {
      width: 100%; padding: 0.75rem; background: transparent; color: var(--text-mid);
      border: 1.5px solid #e0d8c8; border-radius: 10px;
      font-family: 'DM Sans', sans-serif; font-size: 0.88rem; font-weight: 500;
      cursor: pointer; margin-top: 0.6rem; transition: border-color 0.2s, color 0.2s;
    }
    .btn-ghost:hover { border-color: var(--red-deep); color: var(--red-deep); }

    /* MESSAGES */
    .msg-box { display: none; border-radius: 8px; padding: 0.65rem 0.9rem; font-size: 0.78rem; font-weight: 500; margin-bottom: 1rem; align-items: center; gap: 0.5rem; }
    .msg-box.show { display: flex; }
    .msg-box svg { width: 15px; height: 15px; flex-shrink: 0; }
    .msg-error   { background: #fff0f0; border: 1.5px solid #f5bcbc; color: var(--red-deep); }
    .msg-success { background: #f0fff4; border: 1.5px solid #86efac; color: #166534; }

    /* OR DIVIDER */
    .or-divider { display: flex; align-items: center; gap: 0.75rem; margin: 1.2rem 0; font-size: 0.72rem; color: #bbb; }
    .or-divider::before, .or-divider::after { content: ''; flex: 1; height: 1px; background: #e0d8c8; }

    /* NOTES */
    .login-note { margin-top: 1.5rem; text-align: center; font-size: 0.7rem; color: #aaa; line-height: 1.7; }
    .login-note span { color: var(--red-deep); font-weight: 600; }

    /* SDG STRIP */
    .sdg-strip { position: absolute; bottom: 1.5rem; left: 50%; transform: translateX(-50%); display: flex; gap: 0.4rem; }
    .sdg-badge { font-size: 0.58rem; font-weight: 700; padding: 0.22rem 0.5rem; border-radius: 4px; letter-spacing: 0.04em; }
    .sdg-3  { background:#4C9F38; color:#fff; }
    .sdg-6  { background:#26BDE2; color:#fff; }
    .sdg-9  { background:#FD6925; color:#fff; }
    .sdg-11 { background:#FD9D24; color:#fff; }

    @keyframes fadeUp { from { opacity:0; transform:translateY(22px); } to { opacity:1; transform:translateY(0); } }

    @media (max-width: 780px) {
      .page { grid-template-columns: 1fr; }
      .left { display: none; }
      .right { padding: 2rem 1.5rem; min-height: 100vh; justify-content: flex-start; padding-top: 3rem; }
      .sdg-strip { position: relative; bottom: auto; left: auto; transform: none; justify-content: center; margin-top: 2rem; }
    }
  </style>
</head>
<body>
<div class="page">

  <!-- ══ LEFT ══ -->
  <div class="left">
    <div class="left-inner">
      <div class="seal-ring">
        <svg viewBox="0 0 68 68" xmlns="http://www.w3.org/2000/svg">
          <path d="M34 8 C28 16 24 22 26 30 C28 36 34 38 34 38 C34 38 40 36 42 30 C44 22 40 16 34 8Z" fill="rgba(255,255,255,0.85)"/>
          <rect x="30" y="37" width="8" height="14" rx="2" fill="rgba(255,255,255,0.7)"/>
          <rect x="26" y="50" width="16" height="4" rx="2" fill="rgba(255,255,255,0.6)"/>
          <path d="M20 44 C16 40 14 34 18 30" stroke="rgba(255,255,255,0.45)" stroke-width="2" fill="none" stroke-linecap="round"/>
          <path d="M18 36 C14 32 13 26 17 23" stroke="rgba(255,255,255,0.3)" stroke-width="2" fill="none" stroke-linecap="round"/>
          <path d="M48 44 C52 40 54 34 50 30" stroke="rgba(255,255,255,0.45)" stroke-width="2" fill="none" stroke-linecap="round"/>
          <path d="M50 36 C54 32 55 26 51 23" stroke="rgba(255,255,255,0.3)" stroke-width="2" fill="none" stroke-linecap="round"/>
        </svg>
      </div>
      <p class="batstateu-label">Republic of the Philippines</p>
      <h1 class="univ-name">Batangas State University</h1>
      <p class="campus-name">ARASOF – Nasugbu Campus</p>
      <div class="brand-divider"></div>
      <div class="sw-logo">
        <div class="sw-icon">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M12 2C12 2 6 9 6 14C6 17.3 8.7 20 12 20C15.3 20 18 17.3 18 14C18 9 12 2 12 2Z" fill="#C50000"/>
            <path d="M9 13.5C9.5 12.5 10.7 12 12 12C13.3 12 14.5 12.5 15 13.5" stroke="#fff" stroke-width="1.4" stroke-linecap="round"/>
            <path d="M10.5 16C11 15.4 11.5 15 12 15C12.5 15 13 15.4 13.5 16" stroke="#fff" stroke-width="1.4" stroke-linecap="round"/>
            <circle cx="12" cy="18" r="1" fill="#fff"/>
          </svg>
        </div>
        <span class="sw-name">SmartWash</span>
      </div>
      <p class="sw-tagline">IoT-Based Hygiene & Air Quality Monitoring System for BatStateU Restroom Facilities</p>
      <div class="features">
        <span class="feat-pill">🚭 Vape Detection</span>
        <span class="feat-pill">🧴 Soap Monitoring</span>
        <span class="feat-pill">🌬️ Air Quality</span>
        <span class="feat-pill">📊 Live Dashboard</span>
        <span class="feat-pill">🔔 Instant Alerts</span>
        <span class="feat-pill">🧹 Cleanliness Logs</span>
      </div>
    </div>
  </div>

  <!-- ══ RIGHT ══ -->
  <div class="right">

    <!-- ─── VIEW: LOGIN ─── -->
    <div class="card view active" id="view-login">
      <h2 class="card-heading">Welcome back</h2>
      <p class="card-sub">Sign in to your SmartWash account to continue.</p>

      <div class="role-tabs">
        <button class="role-tab active" id="tab-supervisor" onclick="setRole('supervisor')">Supervisor</button>
        <button class="role-tab" id="tab-maintenance" onclick="setRole('maintenance')">Maintenance</button>
      </div>

      <div class="msg-box msg-error" id="loginError">
        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <span id="loginErrorText">Invalid username or password.</span>
      </div>

      <form method="POST" action="" onsubmit="return validateLogin()" novalidate>
        <input type="hidden" name="role" id="roleInput" value="supervisor">

        <div class="field">
          <label for="username">Username</label>
          <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
            <input type="text" id="username" name="username" placeholder="Enter your username" autocomplete="username">
          </div>
        </div>

        <div class="field">
          <label for="password">Password</label>
          <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
            <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password">
            <button type="button" class="pw-toggle" onclick="togglePw('password','eyeLogin')">
              <svg id="eyeLogin" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
            </button>
          </div>
        </div>

        <div class="form-footer-row">
          <label class="remember">
            <input type="checkbox" name="remember" style="width:15px;height:15px;">
            Remember me
          </label>
          <button type="button" class="link-btn" onclick="showView('view-forgot')">Forgot password?</button>
        </div>

        <button type="submit" class="btn-primary">Sign In to SmartWash</button>
      </form>

      <div class="or-divider">or</div>
      <button class="btn-ghost" onclick="showView('view-register')">Create a new account</button>

      <p class="login-note">
        Access is restricted to authorized BatStateU personnel only.<br>
        Contact your administrator if you need an account.<br><br>
        <span>SmartWash</span> © 2025 · BatStateU ARASOF-Nasugbu
      </p>
    </div>

    <!-- ─── VIEW: REGISTER ─── -->
    <div class="card view" id="view-register">
      <h2 class="card-heading">Create account</h2>
      <p class="card-sub">Register a new SmartWash account. Your account will need administrator approval before you can sign in.</p>

      <div class="role-tabs">
        <button class="role-tab active" id="reg-tab-supervisor" onclick="setRegRole('supervisor')">Supervisor</button>
        <button class="role-tab" id="reg-tab-maintenance" onclick="setRegRole('maintenance')">Maintenance</button>
      </div>

      <div class="msg-box msg-error" id="regError">
        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <span id="regErrorText">Please fill in all fields.</span>
      </div>
      <div class="msg-box msg-success" id="regSuccess">
        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span>Account request submitted! Please wait for administrator approval.</span>
      </div>

      <form onsubmit="submitRegister(event)" novalidate>
        <input type="hidden" id="regRoleInput" value="supervisor">

        <div class="field">
          <label>Full Name</label>
          <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
            <input type="text" id="regFullName" placeholder="Your full name">
          </div>
        </div>

        <div class="field">
          <label>Username</label>
          <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
            <input type="text" id="regUsername" placeholder="Choose a username">
          </div>
        </div>

        <div class="field">
          <label>Password</label>
          <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
            <input type="password" id="regPassword" placeholder="Create a password (min. 6 chars)">
            <button type="button" class="pw-toggle" onclick="togglePw('regPassword','eyeReg')">
              <svg id="eyeReg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
            </button>
          </div>
        </div>

        <div class="field" style="margin-bottom:1.5rem">
          <label>Confirm Password</label>
          <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
            <input type="password" id="regConfirm" placeholder="Repeat your password">
          </div>
        </div>

        <button type="submit" class="btn-primary">Submit Registration</button>
        <button type="button" class="btn-ghost" onclick="showView('view-login')">← Back to Sign In</button>
      </form>
    </div>

    <!-- ─── VIEW: FORGOT PASSWORD ─── -->
    <div class="card view" id="view-forgot">
      <h2 class="card-heading">Reset password</h2>
      <p class="card-sub">Enter your username and role. Your reset request will be sent to the system administrator.</p>

      <div class="msg-box msg-error" id="forgotError">
        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <span id="forgotErrorText">Please enter your username.</span>
      </div>
      <div class="msg-box msg-success" id="forgotSuccess">
        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span>Reset request sent! Your administrator will contact you shortly.</span>
      </div>

      <form onsubmit="submitForgot(event)" novalidate>
        <div class="field">
          <label>Username</label>
          <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
            <input type="text" id="forgotUsername" placeholder="Enter your username">
          </div>
        </div>

        <div class="field" style="margin-bottom:1.5rem">
          <label>Account Role</label>
          <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
            <select id="forgotRole" class="field-select">
              <option value="supervisor">Supervisor</option>
              <option value="maintenance">Maintenance</option>
            </select>
          </div>
        </div>

        <button type="submit" class="btn-primary">Send Reset Request</button>
        <button type="button" class="btn-ghost" onclick="showView('view-login')">← Back to Sign In</button>
      </form>

      <p class="login-note" style="margin-top:1.2rem">
        Password resets require admin approval.<br>
        For urgent help: <span>admin@batstateu-arasof.edu.ph</span>
      </p>
    </div>

    <div class="sdg-strip">
      <span class="sdg-badge sdg-3">SDG 3</span>
      <span class="sdg-badge sdg-6">SDG 6</span>
      <span class="sdg-badge sdg-9">SDG 9</span>
      <span class="sdg-badge sdg-11">SDG 11</span>
    </div>
  </div>
</div>

<script>
  function showView(id) {
    document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
    document.getElementById(id).classList.add('active');
  }

  function setRole(role) {
    document.querySelectorAll('#view-login .role-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + role).classList.add('active');
    document.getElementById('roleInput').value = role;
  }

  function setRegRole(role) {
    document.querySelectorAll('#view-register .role-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('reg-tab-' + role).classList.add('active');
    document.getElementById('regRoleInput').value = role;
  }

  const eyeOpen  = '<path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>';
  const eyeClose = '<path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>';

  function togglePw(fieldId, iconId) {
    const pw = document.getElementById(fieldId);
    const ic = document.getElementById(iconId);
    if (pw.type === 'password') { pw.type = 'text'; ic.innerHTML = eyeClose; }
    else { pw.type = 'password'; ic.innerHTML = eyeOpen; }
  }

  function showErr(boxId, textId, msg) {
    const b = document.getElementById(boxId);
    if (textId) document.getElementById(textId).textContent = msg;
    b.classList.add('show');
  }
  function hideMsg(boxId) { document.getElementById(boxId).classList.remove('show'); }

  function validateLogin() {
    const u = document.getElementById('username').value.trim();
    const p = document.getElementById('password').value;
    if (!u || !p) { showErr('loginError','loginErrorText','Please enter your username and password.'); return false; }
    return true;
  }

  function submitRegister(e) {
    e.preventDefault();
    hideMsg('regError'); hideMsg('regSuccess');
    const full = document.getElementById('regFullName').value.trim();
    const user = document.getElementById('regUsername').value.trim();
    const pw   = document.getElementById('regPassword').value;
    const conf = document.getElementById('regConfirm').value;
    if (!full || !user || !pw || !conf) { showErr('regError','regErrorText','Please fill in all fields.'); return; }
    if (pw.length < 6) { showErr('regError','regErrorText','Password must be at least 6 characters.'); return; }
    if (pw !== conf)   { showErr('regError','regErrorText','Passwords do not match.'); return; }
    showErr('regSuccess', null, '');
    setTimeout(() => showView('view-login'), 3000);
  }

  function submitForgot(e) {
    e.preventDefault();
    hideMsg('forgotError'); hideMsg('forgotSuccess');
    const u = document.getElementById('forgotUsername').value.trim();
    if (!u) { showErr('forgotError','forgotErrorText','Please enter your username.'); return; }
    showErr('forgotSuccess', null, '');
  }

  <?php if ($error === '1'): ?>
  showErr('loginError','loginErrorText','Invalid username or password. Please try again.');
  <?php elseif ($error === '2'): ?>
  showErr('loginError','loginErrorText','Please fill in all fields.');
  <?php endif; ?>

  ['username','password'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', () => hideMsg('loginError'));
  });
</script>
</body>
</html>