<?php
require_once "../includes/lang_helper.php";
require_once "../config/google_config.php";
$pageTitle = __('auth_login_title');
require "../includes/header.php";

$googleAuthUrl = null;
if (GOOGLE_CLIENT_ID !== 'YOUR_GOOGLE_CLIENT_ID') {
    $params = [
        'client_id'     => GOOGLE_CLIENT_ID,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'response_type' => 'code',
        'scope'         => 'openid email profile',
        'access_type'   => 'online',
        'prompt'        => 'select_account',
        'state'         => 'login',
    ];
    $googleAuthUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
}
?>

<style>
/* ─── Layout shell ──────────────────────────────────────────────────────────── */
.navbar { display: block !important; }

.main-content {
    min-height: calc(100vh - 60px) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 32px 16px !important;
    background: #f1f5f9 !important;
    overflow-y: auto !important;
}
body { overflow-y: auto !important; height: auto !important; margin: 0 !important; padding: 0 !important; }

/* ─── Outer card ──────────────────────────────────────────────────────────── */
.reg-card {
    display: grid;
    grid-template-columns: 420px 1fr;
    width: 100%;
    max-width: 860px;
    border-radius: 28px;
    overflow: hidden;
    box-shadow: 0 32px 80px rgba(0,0,0,0.12), 0 0 0 1px rgba(0,0,0,0.05);
}
@media (max-width: 800px)  { .reg-card { grid-template-columns: 1fr; max-width: 460px; } }

/* ─── Left panel ────────────────────────────────────────────────────────────── */
.reg-left {
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    min-height: 520px;
    background:
        radial-gradient(ellipse 80% 60% at 30% 110%, rgba(249,115,22,0.55) 0%, transparent 65%),
        radial-gradient(ellipse 90% 70% at 80% -10%, rgba(99,102,241,0.35) 0%, transparent 60%),
        linear-gradient(160deg, #0f172a 0%, #1e3a8a 55%, #1d4ed8 100%);
}
@media (max-width: 800px) { .reg-left { display: none; } }

.reg-left-bg {
    position: absolute;
    inset: 0;
    background:
        linear-gradient(180deg,
            rgba(15,23,42,0.25)  0%,
            rgba(29,78,216,0.18) 30%,
            rgba(249,115,22,0.22) 65%,
            rgba(15,23,42,0.80)  100%
        );
    z-index: 2;
}

.reg-left-img {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -65%);
    width: 78%;
    height: auto;
    z-index: 1;
    -webkit-mask-image: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.6) 20%, black 45%);
    mask-image:         linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.6) 20%, black 45%);
    filter: saturate(1.05) brightness(0.97);
}

.reg-ring {
    position: absolute;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.12);
    pointer-events: none;
    z-index: 3;
}
.reg-ring-1 { width: 320px; height: 320px; top: -80px; right: -80px; }
.reg-ring-2 { width: 200px; height: 200px; top: 60px;  right: -30px; background: rgba(249,115,22,0.06); }
.reg-ring-3 { width: 140px; height: 140px; bottom: 120px; left: -30px; }

.reg-brand-pill {
    position: absolute;
    top: 24px; left: 24px;
    z-index: 5;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 100px;
    padding: 6px 14px;
    font-size: 0.78rem;
    font-weight: 800;
    color: rgba(255,255,255,0.9);
    letter-spacing: 0.5px;
}
.reg-brand-pill-dot { width: 7px; height: 7px; background: #f97316; border-radius: 50%; }

.reg-left-copy {
    position: relative;
    z-index: 4;
    margin-top: auto;
    padding: 36px 32px 40px;
    color: #fff;
}
.reg-left-copy h2 {
    font-size: 1.55rem;
    font-weight: 850;
    line-height: 1.3;
    letter-spacing: -0.4px;
    margin-bottom: 8px;
    color: #fff;
}
.reg-left-copy p {
    font-size: 1rem;
    color: rgba(255,255,255,0.72);
    line-height: 1.65;
    margin-bottom: 0;
    max-width: 280px;
}
.reg-features { display: flex; flex-direction: column; gap: 10px; }
.reg-feature {
    display: flex; align-items: center; gap: 10px;
    font-size: 0.82rem; font-weight: 600;
    color: rgba(255,255,255,0.88);
}
.reg-feature-icon {
    width: 28px; height: 28px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 0.8rem;
}

/* ─── Right panel (form) ────────────────────────────────────────────────────── */
.reg-right {
    background: #fff;
    padding: 48px 44px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
@media (max-width: 480px) { .reg-right { padding: 32px 20px; } }

.reg-header { margin-bottom: 24px; text-align: center; }
.reg-header h3 {
    font-size: 1.6rem;
    font-weight: 850;
    color: #0f172a;
    letter-spacing: -0.5px;
    margin-bottom: 5px;
    line-height: 1.2;
}
.reg-header p { font-size: 0.85rem; color: #64748b; margin: 0; }
.reg-header p a { color: #1d4ed8; font-weight: 700; text-decoration: none; }
.reg-header p a:hover { text-decoration: underline; }

/* Google button */
.btn-google {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    width: 100%; height: 44px;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    color: #0f172a;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.88rem;
    cursor: pointer;
    text-decoration: none;
    transition: border-color 0.18s, box-shadow 0.18s, transform 0.18s;
    margin-bottom: 16px;
}
.btn-google svg { width: 18px; height: 18px; flex-shrink: 0; }
.btn-google:hover {
    border-color: #4285F4;
    box-shadow: 0 0 0 3px rgba(66,133,244,0.12);
    transform: translateY(-1px);
}

/* Divider */
.reg-divider {
    display: flex; align-items: center;
    gap: 12px;
    color: #94a3b8;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 18px;
}
.reg-divider::before, .reg-divider::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }

/* Form groups */
.reg-field { margin-bottom: 14px; }
.reg-field label {
    display: block;
    font-size: 0.78rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 5px;
}
.reg-input-wrap { position: relative; }
.reg-input-wrap svg.field-icon-left {
    position: absolute;
    left: 12px; top: 50%; transform: translateY(-50%);
    width: 15px; height: 15px;
    color: #94a3b8;
    pointer-events: none;
}
.reg-input-wrap svg.field-icon-right {
    position: absolute;
    right: 12px; top: 50%; transform: translateY(-50%);
    width: 15px; height: 15px;
    color: #94a3b8;
    cursor: pointer;
    padding: 2px;
}
.reg-input-wrap input {
    width: 100%;
    height: 44px;
    padding: 0 40px 0 38px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 11px;
    font-size: 0.88rem;
    font-weight: 500;
    color: #0f172a;
    font-family: inherit;
    transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
}
.reg-input-wrap input::placeholder { color: #94a3b8; }
.reg-input-wrap input:focus {
    background: #fff;
    border-color: #1d4ed8;
    box-shadow: 0 0 0 3px rgba(29,78,216,0.1);
    outline: none;
}

/* Submit */
.btn-login {
    width: 100%; height: 46px;
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    border: none; border-radius: 12px;
    color: #fff; font-size: 0.95rem; font-weight: 800;
    letter-spacing: 0.2px;
    box-shadow: 0 6px 20px -4px rgba(249,115,22,0.45);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: transform 0.18s, box-shadow 0.18s;
    margin-top: 6px;
}
.btn-login:hover { transform: translateY(-2px); box-shadow: 0 10px 24px -4px rgba(249,115,22,0.5); }

/* Dark mode */
[data-theme="dark"] .reg-right { background: #161e31; }
[data-theme="dark"] .reg-header h3 { color: #e2e8f0; }
[data-theme="dark"] .reg-header p { color: #94a3b8; }
[data-theme="dark"] .reg-field label { color: #94a3b8; }
[data-theme="dark"] .reg-input-wrap input {
    background: #0f172a; border-color: #2d3f60; color: #e2e8f0;
}
[data-theme="dark"] .reg-input-wrap input:focus { background: #0b1121; }
[data-theme="dark"] .btn-google { background: #1e293b; border-color: #2d3f60; color: #e2e8f0; }
[data-theme="dark"] .reg-divider::before,
[data-theme="dark"] .reg-divider::after { background: #2d3f60; }

/* RTL */
[dir="rtl"] .reg-input-wrap svg.field-icon-left  { left: auto; right: 12px; }
[dir="rtl"] .reg-input-wrap svg.field-icon-right { right: auto; left: 12px; }
[dir="rtl"] .reg-input-wrap input { padding: 0 38px 0 40px; }
</style>

<div class="reg-card">

    <!-- ── Left visual ──────────────────────────────────────────────── -->
    <div class="reg-left">
        <img src="../assets/images/students_illustration.png" alt="" class="reg-left-img">
        <div class="reg-left-bg"></div>
        <div class="reg-ring reg-ring-1"></div>
        <div class="reg-ring reg-ring-2"></div>
        <div class="reg-ring reg-ring-3"></div>

        <div class="reg-brand-pill">
            <div class="reg-brand-pill-dot"></div>
            Maslaki
        </div>

        <div class="reg-left-copy">
            <h2><?php if(getLang()==='ar') echo 'مرحباً بعودتك'; elseif(getLang()==='en') echo 'Welcome back'; else echo 'Bon retour parmi nous'; ?></h2>
            <p><?php if(getLang()==='ar') echo 'تابع رحلتك نحو مؤسسة أحلامك'; elseif(getLang()==='en') echo 'Continue your journey toward your dream institution'; else echo 'Continuez votre parcours vers l\'établissement de vos rêves'; ?></p>
        </div>
    </div>

    <!-- ── Right form ────────────────────────────────────────────────── -->
    <div class="reg-right">

        <div class="reg-header">
            <h3><?php echo __('auth_login_title'); ?></h3>
            <p><?php echo __('auth_no_account'); ?> <a href="register.php"><?php echo __('register'); ?></a></p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="msg msg-error" style="border-radius:12px;margin-bottom:14px;padding:10px 14px;font-size:0.83rem;">⚠️ <?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="msg msg-success" style="border-radius:12px;margin-bottom:14px;padding:10px 14px;font-size:0.83rem;">✓ <?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>

        <!-- Google Sign-In -->
        <?php if ($googleAuthUrl): ?>
            <a href="<?php echo htmlspecialchars($googleAuthUrl); ?>" class="btn-google" id="googleLoginBtn">
        <?php else: ?>
            <button type="button" class="btn-google" id="googleLoginBtn">
        <?php endif; ?>
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                </svg>
                <span><?php if(getLang()==='ar') echo 'متابعة باستخدام جوجل'; elseif(getLang()==='en') echo 'Continue with Google'; else echo 'Continuer avec Google'; ?></span>
        <?php if ($googleAuthUrl): ?>
            </a>
        <?php else: ?>
            </button>
        <?php endif; ?>

        <div class="reg-divider"><span><?php if(getLang()==='ar') echo 'أو'; elseif(getLang()==='en') echo 'or'; else echo 'ou'; ?></span></div>

        <form method="POST" action="../login_process.php" id="loginForm">

            <div class="reg-field">
                <label><?php echo __('auth_email_label'); ?></label>
                <div class="reg-input-wrap">
                    <svg class="field-icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <input type="email" name="email" placeholder="nom@example.com" required autocomplete="email">
                </div>
            </div>

            <div class="reg-field">
                <label><?php echo __('auth_password_label'); ?></label>
                <div class="reg-input-wrap">
                    <svg class="field-icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input type="password" id="passwordInput" name="password" placeholder="••••••••" required autocomplete="current-password">
                    <svg class="field-icon-right" id="togglePassword" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                <?php echo __('auth_login_btn'); ?>
            </button>

        </form>
    </div>
</div>

<!-- Dev-mode Google modal (unchanged logic) -->
<?php if (!$googleAuthUrl): ?>
<div id="googleSimModalLogin" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.6);backdrop-filter:blur(4px);z-index:99999;align-items:center;justify-content:center;padding:16px;">
    <div style="background:var(--white);border:1px solid var(--border-color);border-radius:24px;padding:32px;width:100%;max-width:400px;box-shadow:var(--shadow-lg);text-align:center;position:relative;">
        <button onclick="document.getElementById('googleSimModalLogin').style.display='none'" style="position:absolute;top:14px;right:18px;background:none;border:none;font-size:1.8rem;cursor:pointer;color:var(--text-muted);">&times;</button>
        <svg viewBox="0 0 24 24" width="40" height="40" style="margin:0 auto 12px;display:block;" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
        </svg>
        <h4 style="font-size:1.2rem;font-weight:800;color:var(--text-dark);margin-bottom:4px;font-family:'Outfit',sans-serif;"><?php if(getLang()==='ar') echo 'اختر حسابًا'; elseif(getLang()==='en') echo 'Choose an account'; else echo 'Choisissez un compte'; ?></h4>
        <p style="color:var(--text-muted);font-size:0.82rem;margin-bottom:20px;"><?php if(getLang()==='ar') echo 'للمتابعة إلى مسلكي'; elseif(getLang()==='en') echo 'to continue to Maslaki'; else echo 'pour continuer vers Maslaki'; ?> <span style="background:var(--bg-light);color:#4285F4;border-radius:4px;padding:1px 6px;font-size:0.75rem;font-weight:700;">Mode Dev</span></p>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <?php
            $devAccounts = [
                ['initials'=>'SE','bg'=>'#eef2ff','color'=>'#4285F4','name'=>'Safa El Gharras','email'=>'safa@example.com'],
                ['initials'=>'AA','bg'=>'#fff7ed','color'=>'#f97316','name'=>'Ahmed Al Idrissi','email'=>'ahmed@test.ma'],
                ['initials'=>'IM','bg'=>'#ecfdf5','color'=>'#10b981','name'=>'Invité Maslaki','email'=>'invite@maslaki.ma'],
            ];
            foreach ($devAccounts as $acc): ?>
            <form method="POST" action="../google_callback.php">
                <input type="hidden" name="dev_mode" value="1">
                <input type="hidden" name="name"   value="<?php echo htmlspecialchars($acc['name']); ?>">
                <input type="hidden" name="email"  value="<?php echo htmlspecialchars($acc['email']); ?>">
                <input type="hidden" name="avatar" value="">
                <button type="submit" style="width:100%;display:flex;align-items:center;gap:14px;padding:12px 15px;border:1px solid var(--border-color);border-radius:14px;background:var(--white);cursor:pointer;text-align:left;transition:all 0.2s;" onmouseover="this.style.borderColor='#f97316'" onmouseout="this.style.borderColor='var(--border-color)'">
                    <div style="background:<?php echo $acc['bg']; ?>;color:<?php echo $acc['color']; ?>;width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;flex-shrink:0;"><?php echo $acc['initials']; ?></div>
                    <div><div style="font-weight:600;font-size:0.9rem;color:var(--text-dark);"><?php echo $acc['name']; ?></div><div style="font-size:0.75rem;color:var(--text-muted);"><?php echo $acc['email']; ?></div></div>
                </button>
            </form>
            <?php endforeach; ?>
        </div>
        <p style="margin-top:16px;font-size:0.78rem;color:var(--text-muted);">💡 Ajoutez vos clés dans <code>config/google_config.php</code> pour activer Google OAuth réel.</p>
    </div>
</div>
<script>
document.getElementById('googleLoginBtn').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('googleSimModalLogin').style.display = 'flex';
});
document.getElementById('googleSimModalLogin').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>
<?php endif; ?>

<script>
(function () {
    const btn = document.getElementById('togglePassword');
    const inp = document.getElementById('passwordInput');
    if (!btn || !inp) return;
    btn.addEventListener('click', function () {
        const show = inp.type === 'password';
        inp.type = show ? 'text' : 'password';
        this.innerHTML = show
            ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>'
            : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    });
})();
</script>

<?php require "../includes/footer.php"; ?>
