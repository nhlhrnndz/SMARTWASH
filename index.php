<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SmartWash — BatStateU ARASOF-Nasugbu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --red-deep:    #C50000;
      --red-vivid:   #E70000;
      --cream:       #FFFDEF;
      --light:       #F1F1F1;
      --white:       #ffffff;
      --text-dark:   #1a0000;
      --text-mid:    #4a1010;
      --shadow:      rgba(197, 0, 0, 0.18);
    }

    html, body {
      height: 100%;
      font-family: 'DM Sans', sans-serif;
      background: var(--cream);
      overflow: hidden;
    }

    /* ── SPLIT LAYOUT ── */
    .page {
      display: grid;
      grid-template-columns: 1fr 1fr;
      height: 100vh;
    }

    /* ── LEFT PANEL ── */
    .left {
      position: relative;
      background: var(--red-deep);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 3rem;
      overflow: hidden;
    }

    /* decorative circles */
    .left::before {
      content: '';
      position: absolute;
      top: -120px; right: -120px;
      width: 380px; height: 380px;
      border-radius: 50%;
      background: rgba(255,255,255,0.05);
    }
    .left::after {
      content: '';
      position: absolute;
      bottom: -80px; left: -80px;
      width: 280px; height: 280px;
      border-radius: 50%;
      background: rgba(231,0,0,0.4);
    }

    .left-inner {
      position: relative;
      z-index: 1;
      text-align: center;
      animation: fadeUp 0.9s ease both;
    }

    /* BatStateU seal placeholder */
    .seal-ring {
      width: 110px; height: 110px;
      border-radius: 50%;
      border: 3px solid rgba(255,255,255,0.4);
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 1.4rem;
      background: rgba(255,255,255,0.08);
      backdrop-filter: blur(4px);
    }
    .seal-ring svg {
      width: 68px; height: 68px;
      fill: none;
    }

    .batstateu-label {
      font-family: 'DM Sans', sans-serif;
      font-size: 0.65rem;
      font-weight: 600;
      letter-spacing: 0.25em;
      text-transform: uppercase;
      color: rgba(255,255,255,0.65);
      margin-bottom: 0.35rem;
    }

    .univ-name {
      font-family: 'Playfair Display', serif;
      font-size: 1.35rem;
      font-weight: 700;
      color: var(--white);
      line-height: 1.25;
      margin-bottom: 0.25rem;
    }

    .campus-name {
      font-size: 0.72rem;
      color: rgba(255,255,255,0.55);
      letter-spacing: 0.06em;
      font-weight: 400;
      margin-bottom: 2.5rem;
    }

    /* SmartWash brand */
    .brand-divider {
      width: 48px; height: 2px;
      background: rgba(255,255,255,0.3);
      margin: 0 auto 2rem;
      border-radius: 99px;
    }

    .sw-logo {
      display: flex; align-items: center; justify-content: center;
      gap: 0.65rem;
      margin-bottom: 0.8rem;
    }

    .sw-icon {
      width: 42px; height: 42px;
      background: var(--white);
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }
    .sw-icon svg { width: 24px; height: 24px; }

    .sw-name {
      font-family: 'Playfair Display', serif;
      font-size: 2.2rem;
      font-weight: 900;
      color: var(--white);
      letter-spacing: -0.01em;
    }

    .sw-tagline {
      font-size: 0.75rem;
      color: rgba(255,255,255,0.6);
      font-weight: 300;
      letter-spacing: 0.05em;
      line-height: 1.6;
      max-width: 280px;
      margin: 0 auto;
    }

    /* feature pills */
    .features {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      justify-content: center;
      margin-top: 2rem;
    }
    .feat-pill {
      background: rgba(255,255,255,0.12);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 99px;
      padding: 0.3rem 0.85rem;
      font-size: 0.65rem;
      color: rgba(255,255,255,0.8);
      font-weight: 500;
      letter-spacing: 0.04em;
    }

    /* ── RIGHT PANEL ── */
    .right {
      background: var(--cream);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 3rem 3.5rem;
      position: relative;
    }

    /* subtle grid texture */
    .right::before {
      content: '';
      position: absolute; inset: 0;
      background-image:
        linear-gradient(rgba(197,0,0,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(197,0,0,0.04) 1px, transparent 1px);
      background-size: 32px 32px;
      pointer-events: none;
    }

    .login-card {
      position: relative;
      width: 100%;
      max-width: 400px;
      animation: fadeUp 0.9s 0.15s ease both;
    }

    .login-heading {
      font-family: 'Playfair Display', serif;
      font-size: 2rem;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 0.4rem;
    }

    .login-sub {
      font-size: 0.82rem;
      color: var(--text-mid);
      font-weight: 400;
      margin-bottom: 2.2rem;
    }

    /* role tabs */
    .role-tabs {
      display: flex;
      background: var(--light);
      border-radius: 10px;
      padding: 4px;
      margin-bottom: 1.8rem;
      gap: 4px;
    }
    .role-tab {
      flex: 1;
      padding: 0.5rem 0.5rem;
      border: none;
      border-radius: 7px;
      background: transparent;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.78rem;
      font-weight: 500;
      color: #888;
      cursor: pointer;
      transition: all 0.22s ease;
    }
    .role-tab.active {
      background: var(--white);
      color: var(--red-deep);
      font-weight: 600;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    /* form fields */
    .field {
      margin-bottom: 1.1rem;
    }

    label {
      display: block;
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--text-mid);
      letter-spacing: 0.05em;
      text-transform: uppercase;
      margin-bottom: 0.45rem;
    }

    .input-wrap {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-icon {
      position: absolute;
      left: 14px;
      width: 17px; height: 17px;
      color: #bbb;
      pointer-events: none;
      transition: color 0.2s;
    }

    input[type="text"],
    input[type="password"],
    input[type="email"] {
      width: 100%;
      padding: 0.75rem 1rem 0.75rem 2.75rem;
      border: 1.5px solid #e0d8c8;
      border-radius: 10px;
      background: var(--white);
      font-family: 'DM Sans', sans-serif;
      font-size: 0.88rem;
      color: var(--text-dark);
      outline: none;
      transition: border-color 0.22s, box-shadow 0.22s;
    }

    input:focus {
      border-color: var(--red-deep);
      box-shadow: 0 0 0 3px rgba(197,0,0,0.1);
    }

    input:focus ~ .input-icon,
    .input-wrap:focus-within .input-icon {
      color: var(--red-deep);
    }

    .pw-toggle {
      position: absolute;
      right: 13px;
      background: none; border: none;
      cursor: pointer;
      color: #bbb;
      display: flex; align-items: center;
      padding: 0;
      transition: color 0.2s;
    }
    .pw-toggle:hover { color: var(--red-deep); }
    .pw-toggle svg { width: 17px; height: 17px; }

    /* remember & forgot */
    .form-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .remember {
      display: flex;
      align-items: center;
      gap: 0.45rem;
      font-size: 0.78rem;
      color: var(--text-mid);
      cursor: pointer;
      user-select: none;
    }

    .remember input[type="checkbox"] {
      width: 15px; height: 15px;
      accent-color: var(--red-deep);
      padding: 0;
      border-radius: 4px;
    }

    .forgot {
      font-size: 0.78rem;
      color: var(--red-deep);
      text-decoration: none;
      font-weight: 500;
      transition: opacity 0.2s;
    }
    .forgot:hover { opacity: 0.7; }

    /* submit */
    .btn-login {
      width: 100%;
      padding: 0.85rem;
      background: var(--red-deep);
      color: var(--white);
      border: none;
      border-radius: 10px;
      font-family: 'Playfair Display', serif;
      font-size: 1rem;
      font-weight: 700;
      letter-spacing: 0.03em;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      transition: background 0.22s, transform 0.15s, box-shadow 0.22s;
      box-shadow: 0 4px 18px var(--shadow);
    }

    .btn-login::after {
      content: '';
      position: absolute; inset: 0;
      background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, transparent 60%);
      pointer-events: none;
    }

    .btn-login:hover {
      background: var(--red-vivid);
      transform: translateY(-1px);
      box-shadow: 0 8px 24px var(--shadow);
    }
    .btn-login:active { transform: translateY(0); }

    /* error msg */
    .error-msg {
      display: none;
      background: #fff0f0;
      border: 1.5px solid #f5bcbc;
      border-radius: 8px;
      padding: 0.65rem 0.9rem;
      font-size: 0.78rem;
      color: var(--red-deep);
      font-weight: 500;
      margin-bottom: 1rem;
      align-items: center;
      gap: 0.5rem;
    }
    .error-msg.show { display: flex; }
    .error-msg svg { width: 15px; height: 15px; flex-shrink: 0; }

    /* bottom note */
    .login-note {
      margin-top: 1.5rem;
      text-align: center;
      font-size: 0.7rem;
      color: #aaa;
      line-height: 1.7;
    }
    .login-note span { color: var(--red-deep); font-weight: 600; }

    /* sdg strip */
    .sdg-strip {
      position: absolute;
      bottom: 1.5rem;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 0.4rem;
      align-items: center;
    }
    .sdg-badge {
      font-size: 0.58rem;
      font-weight: 700;
      padding: 0.22rem 0.5rem;
      border-radius: 4px;
      letter-spacing: 0.04em;
    }
    .sdg-3  { background: #4C9F38; color: #fff; }
    .sdg-6  { background: #26BDE2; color: #fff; }
    .sdg-9  { background: #FD6925; color: #fff; }
    .sdg-11 { background: #FD9D24; color: #fff; }

    /* animation */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(22px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* responsive */
    @media (max-width: 780px) {
      .page { grid-template-columns: 1fr; }
      .left  { display: none; }
      .right { padding: 2rem 1.5rem; }
    }
  </style>
</head>
<body>

<div class="page">

  <!-- ══ LEFT ══ -->
  <div class="left">
    <div class="left-inner">

      <!-- BatStateU seal area -->
      <div class="seal-ring">
        <!-- stylized lamp/torch icon echoing BatStateU seal -->
        <svg viewBox="0 0 68 68" xmlns="http://www.w3.org/2000/svg">
          <!-- flame -->
          <path d="M34 8 C28 16 24 22 26 30 C28 36 34 38 34 38 C34 38 40 36 42 30 C44 22 40 16 34 8Z" fill="rgba(255,255,255,0.85)"/>
          <!-- torch body -->
          <rect x="30" y="37" width="8" height="14" rx="2" fill="rgba(255,255,255,0.7)"/>
          <!-- base -->
          <rect x="26" y="50" width="16" height="4" rx="2" fill="rgba(255,255,255,0.6)"/>
          <!-- laurel left hint -->
          <path d="M20 44 C16 40 14 34 18 30" stroke="rgba(255,255,255,0.45)" stroke-width="2" fill="none" stroke-linecap="round"/>
          <path d="M18 36 C14 32 13 26 17 23" stroke="rgba(255,255,255,0.3)" stroke-width="2" fill="none" stroke-linecap="round"/>
          <!-- laurel right hint -->
          <path d="M48 44 C52 40 54 34 50 30" stroke="rgba(255,255,255,0.45)" stroke-width="2" fill="none" stroke-linecap="round"/>
          <path d="M50 36 C54 32 55 26 51 23" stroke="rgba(255,255,255,0.3)" stroke-width="2" fill="none" stroke-linecap="round"/>
        </svg>
      </div>

      <p class="batstateu-label">Republic of the Philippines</p>
      <h1 class="univ-name">Batangas State University</h1>
      <p class="campus-name">ARASOF – Nasugbu Campus</p>

      <div class="brand-divider"></div>

      <!-- SmartWash logo -->
      <div class="sw-logo">
        <div class="sw-icon">
          <!-- droplet + wifi icon -->
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none">
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
    <div class="login-card">

      <h2 class="login-heading">Welcome back</h2>
      <p class="login-sub">Sign in to your SmartWash account to continue.</p>

      <!-- role tabs -->
      <div class="role-tabs" role="tablist">
        <button class="role-tab active" id="tab-supervisor" onclick="setRole('supervisor')" role="tab">Supervisor</button>
        <button class="role-tab" id="tab-maintenance" onclick="setRole('maintenance')" role="tab">Maintenance</button>
      </div>

      <!-- error -->
      <div class="error-msg" id="errorMsg">
        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <span id="errorText">Invalid username or password.</span>
      </div>

      <form method="POST" action="auth/login.php" onsubmit="return validateForm()" novalidate>
        <input type="hidden" name="role" id="roleInput" value="supervisor">

        <!-- Username -->
        <div class="field">
          <label for="username">Username</label>
          <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
            </svg>
            <input type="text" id="username" name="username" placeholder="Enter your username" autocomplete="username" required>
          </div>
        </div>

        <!-- Password -->
        <div class="field">
          <label for="password">Password</label>
          <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
            <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
            <button type="button" class="pw-toggle" onclick="togglePw()" title="Show/hide password">
              <svg id="eyeIcon" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
            </button>
          </div>
        </div>

        <!-- Remember & Forgot -->
        <div class="form-footer">
          <label class="remember">
            <input type="checkbox" name="remember" style="width:15px;height:15px;">
            Remember me
          </label>
          <a href="#" class="forgot">Forgot password?</a>
        </div>

        <button type="submit" class="btn-login">Sign In to SmartWash</button>
      </form>

      <p class="login-note">
        Access is restricted to authorized BatStateU personnel only.<br>
        Contact your administrator if you need an account.<br><br>
        <span>SmartWash</span> © 2025 · BatStateU ARASOF-Nasugbu
      </p>

    </div>

    <!-- SDG badges -->
    <div class="sdg-strip">
      <span class="sdg-badge sdg-3">SDG 3</span>
      <span class="sdg-badge sdg-6">SDG 6</span>
      <span class="sdg-badge sdg-9">SDG 9</span>
      <span class="sdg-badge sdg-11">SDG 11</span>
    </div>
  </div>
</div>

<script>
  function setRole(role) {
    document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + role).classList.add('active');
    document.getElementById('roleInput').value = role;
  }

  function togglePw() {
    const pw = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (pw.type === 'password') {
      pw.type = 'text';
      icon.innerHTML = '<path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>';
    } else {
      pw.type = 'password';
      icon.innerHTML = '<path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>';
    }
  }

  function validateForm() {
    const u = document.getElementById('username').value.trim();
    const p = document.getElementById('password').value;
    if (!u || !p) {
      const err = document.getElementById('errorMsg');
      document.getElementById('errorText').textContent = 'Please enter your username and password.';
      err.classList.add('show');
      return false;
    }
    return true;
  }

  // hide error on input
  ['username','password'].forEach(id => {
    document.getElementById(id).addEventListener('input', () => {
      document.getElementById('errorMsg').classList.remove('show');
    });
  });

  // PHP error injection point — if login.php sets ?error=1
  const params = new URLSearchParams(window.location.search);
  if (params.get('error') === '1') {
    const err = document.getElementById('errorMsg');
    document.getElementById('errorText').textContent = 'Invalid username or password. Please try again.';
    err.classList.add('show');
  }
</script>

</body>
</html>
