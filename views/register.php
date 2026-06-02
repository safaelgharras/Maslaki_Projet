<?php
require_once "../includes/lang_helper.php";
require_once "../config/google_config.php";
$pageTitle = __('auth_register_title');
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
        'state'         => 'register',
    ];
    $googleAuthUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
}
?>

<style>
/* ── Layout overrides (mirrors login.php) ── */
.navbar { display: block !important; }

@media (min-width: 993px) {
    body { margin: 0 !important; padding: 0 !important; overflow-y: auto !important; height: auto !important; }
    .main-content {
        min-height: calc(100vh - 60px) !important;
        display: flex !important; align-items: center !important; justify-content: center !important;
        padding: 40px 24px !important; background: var(--bg-light) !important;
    }
}
@media (max-width: 992px) and (min-width: 577px) {
    body { margin: 0 !important; padding: 0 !important; overflow-y: auto !important; height: auto !important; }
    .main-content {
        min-height: calc(100vh - 60px) !important;
        display: flex !important; align-items: center !important; justify-content: center !important;
        padding: 32px 20px !important; background: var(--bg-light) !important;
    }
}
@media (max-width: 576px) {
    body { overflow-y: auto !important; height: auto !important; }
    .main-content {
        min-height: calc(100vh - 60px) !important;
        display: flex !important; align-items: center !important; justify-content: center !important;
        padding: 20px 16px !important; background: var(--bg-light) !important;
    }
}

/* ── Split card ── */
.auth-split {
    display: grid;
    grid-template-columns: 1fr 1fr;
    width: 100%; max-width: 980px;
    background: var(--white);
    border: 1px solid var(--border-color);
    border-radius: 24px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.04);
    overflow: hidden;
    margin: 0 auto !important;
}
@media (max-width: 992px) { .auth-split { grid-template-columns: 0.45fr 0.55fr; max-width: 820px; } }
@media (max-width: 576px) {
    .auth-split { grid-template-columns: 1fr; border: none !important; box-shadow: none !important; background: transparent !important; }
}

/* ── Left visual panel ── */
.auth-visual-side {
    background: linear-gradient(135deg, rgba(244,247,254,0.6) 0%, rgba(255,255,255,0.2) 50%, rgba(255,247,237,0.35) 100%) !important;
    position: relative; overflow: hidden;
    display: flex; align-items: center; justify-content: center; padding: 30px;
    border-right: 1px solid var(--border-color);
}
[data-theme="dark"] .auth-visual-side {
    background: linear-gradient(135deg, rgba(15,23,42,0.55) 0%, rgba(30,41,59,0.55) 100%) !important;
}
[dir="rtl"] .auth-visual-side { border-right: none; border-left: 1px solid var(--border-color); }
@media (max-width: 576px) { .auth-visual-side { display: none; } }

.auth-orb { position: absolute; border-radius: 50%; filter: blur(80px); z-index: 1; pointer-events: none; opacity: 0.14; }
.auth-orb-1 { width: 220px; height: 220px; background: var(--primary-light); top: -20px; left: -20px; }
.auth-orb-2 { width: 260px; height: 260px; background: var(--accent); bottom: -50px; right: -50px; }
.auth-students-img {
    max-width: 85%; max-height: 85%; width: auto; height: auto;
    object-fit: contain; z-index: 5;
    filter: drop-shadow(0 15px 30px rgba(0,0,0,0.06));
    animation: float 6s ease-in-out infinite;
}

/* ── Right form panel ── */
.auth-form-side {
    background: var(--white) !important;
    padding: 36px 44px !important;
    display: flex; flex-direction: column; justify-content: center; align-items: center;
}
@media (max-width: 576px) { .auth-form-side { padding: 20px 10px !important; background: transparent !important; } }

.auth-form-container { width: 100%; max-width: 360px; }

.auth-header { margin-bottom: 20px; text-align: center; }
.auth-header h3 {
    color: var(--text-dark) !important; font-family: 'Outfit', sans-serif;
    font-size: 1.75rem !important; font-weight: 800 !important; letter-spacing: -0.5px; margin-bottom: 6px;
}
.auth-header p { color: var(--text-muted) !important; font-size: 0.88rem; }
.auth-header p a { color: var(--primary-light) !important; font-weight: 700 !important; text-decoration: none; }
.auth-header p a:hover { color: var(--accent) !important; text-decoration: underline; }

/* Google button */
.btn-google {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    width: 100%; background: var(--white) !important;
    border: 1.5px solid var(--border-color) !important; color: var(--text-dark) !important;
    height: 44px !important; border-radius: 12px !important; font-weight: 700 !important;
    font-size: 0.9rem !important; cursor: pointer; text-decoration: none;
    transition: all 0.2s ease !important; margin-bottom: 14px;
}
.btn-google svg { width: 18px; height: 18px; }
.btn-google:hover { background: var(--bg-light) !important; border-color: var(--primary-light) !important; transform: translateY(-1px) !important; }

/* Divider */
.auth-divider {
    display: flex; align-items: center; color: var(--text-muted) !important;
    margin: 12px 0 !important; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
}
.auth-divider::before, .auth-divider::after { content: ''; flex: 1; border-bottom: 1px solid var(--border-color) !important; }
.auth-divider::before { margin-right: 12px; }
.auth-divider::after  { margin-left: 12px; }

/* Form inputs */
.form-group { margin-bottom: 12px; }
.form-group label { font-size: 0.82rem !important; font-weight: 600 !important; color: var(--text-dark) !important; margin-bottom: 5px !important; display: block; }
.form-group-icon-wrapper { position: relative; display: flex; align-items: center; }
.form-group-icon-wrapper input,
.form-group-icon-wrapper select {
    background: var(--bg-light) !important; border: 1px solid var(--border-color) !important;
    color: var(--text-dark) !important; height: 42px !important; border-radius: 12px !important;
    padding-left: 42px !important; padding-right: 14px !important;
    font-size: 0.88rem !important; font-weight: 500 !important; width: 100%;
    transition: all 0.2s ease !important;
}
.form-group-icon-wrapper input:focus,
.form-group-icon-wrapper select:focus {
    background: var(--white) !important; border-color: var(--accent) !important;
    box-shadow: 0 0 0 3px rgba(249,115,22,0.1) !important; outline: none;
}
.form-group-icon-wrapper .input-icon-left { position: absolute; left: 14px !important; font-size: 0.95rem !important; color: var(--text-muted) !important; pointer-events: none; }
.form-group-icon-wrapper .input-icon-right { position: absolute; right: 14px !important; font-size: 0.95rem !important; color: var(--text-muted) !important; cursor: pointer; }

/* 3-column grid for academic info */
.form-grid-three { display: grid; grid-template-columns: 1.2fr 0.9fr 0.9fr; gap: 8px; }
@media (max-width: 576px) { .form-grid-three { grid-template-columns: 1fr; } }

/* Checkbox */
.auth-checkbox-group { display: flex; align-items: flex-start; gap: 8px; margin: 10px 0; }
.auth-checkbox-group input[type="checkbox"] { margin-top: 2px; flex-shrink: 0; }
.auth-checkbox-group label { font-size: 0.8rem; color: var(--text-muted); line-height: 1.4; }
.auth-checkbox-group a { color: var(--primary-light); text-decoration: none; font-weight: 600; }
.auth-checkbox-group a:hover { text-decoration: underline; }

/* Submit button */
.btn-orange-submit {
    width: 100%; height: 44px !important; border-radius: 12px !important;
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%) !important;
    border: none !important; color: #fff !important; font-size: 0.95rem !important;
    font-weight: 700 !important; box-shadow: 0 8px 20px -6px rgba(249,115,22,0.25) !important;
    cursor: pointer; transition: all 0.2s ease !important; margin-top: 4px;
}
.btn-orange-submit:hover { transform: translateY(-1px) !important; box-shadow: 0 10px 22px -5px rgba(249,115,22,0.35) !important; }

/* RTL */
[dir="rtl"] .form-group-icon-wrapper input,
[dir="rtl"] .form-group-icon-wrapper select { padding-left: 14px !important; padding-right: 42px !important; }
[dir="rtl"] .form-group-icon-wrapper .input-icon-left { left: auto !important; right: 14px !important; }
[dir="rtl"] .form-group-icon-wrapper .input-icon-right { right: auto !important; left: 14px !important; }
</style>

<div class="auth-split">
    <!-- Left: visual panel -->
    <div class="auth-visual-side">
        <div class="auth-orb auth-orb-1"></div>
        <div class="auth-orb auth-orb-2"></div>
        <img src="../assets/images/students_illustration.png" alt="Students" class="auth-students-img">
    </div>

    <!-- Right: registration form -->
    <div class="auth-form-side">
        <div class="auth-form-container">

            <div class="auth-header">
                <h3><?php echo __('auth_register_title'); ?></h3>
                <p><?php echo __('auth_have_account'); ?> <a href="login.php"><?php echo __('login'); ?></a></p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="msg msg-error" style="border-radius:12px;margin-bottom:12px;padding:10px;font-size:0.85rem;">⚠️ <?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="msg msg-success" style="border-radius:12px;margin-bottom:12px;padding:10px;font-size:0.85rem;">✓ <?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>

            <!-- Google Sign-Up -->
            <?php if ($googleAuthUrl): ?>
                <a href="<?php echo htmlspecialchars($googleAuthUrl); ?>" class="btn-google" id="googleSignUpBtn">
            <?php else: ?>
                <button type="button" class="btn-google" id="googleSignUpBtn">
            <?php endif; ?>
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                    </svg>
                    <span><?php if(getLang()==='ar') echo 'إنشاء حساب بجوجل'; elseif(getLang()==='en') echo 'Sign up with Google'; else echo "S'inscrire avec Google"; ?></span>
            <?php if ($googleAuthUrl): ?>
                </a>
            <?php else: ?>
                </button>
            <?php endif; ?>

            <div class="auth-divider"><span><?php if(getLang()==='ar') echo 'أو'; elseif(getLang()==='en') echo 'or'; else echo 'ou'; ?></span></div>

            <form method="POST" action="../register_process.php" id="registerForm">
                <div class="form-group">
                    <label><?php echo __('auth_name_label'); ?></label>
                    <div class="form-group-icon-wrapper">
                        <span class="input-icon-left">👤</span>
                        <input type="text" name="name" id="nameInput" placeholder="<?php if(getLang()==='ar') echo 'الاسم الكامل'; elseif(getLang()==='en') echo 'Full name'; else echo 'Nom et prénom'; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label><?php echo __('auth_email_label'); ?></label>
                    <div class="form-group-icon-wrapper">
                        <span class="input-icon-left">✉️</span>
                        <input type="email" name="email" id="emailInput" placeholder="name@example.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label><?php echo __('auth_password_label'); ?></label>
                    <div class="form-group-icon-wrapper">
                        <span class="input-icon-left">🔒</span>
                        <input type="password" id="passwordInput" name="password" placeholder="••••••••" required>
                        <span class="input-icon-right" id="togglePassword">👁️</span>
                    </div>
                    <div style="height:3px;background:#e2e8f0;border-radius:2px;margin-top:5px;overflow:hidden;">
                        <div id="strengthBar" style="height:100%;width:0%;background:#ef4444;transition:width 0.3s ease,background-color 0.3s ease;"></div>
                    </div>
                    <span id="strengthText" style="font-size:0.72rem;color:#94a3b8;display:block;margin-top:2px;font-weight:600;"></span>
                </div>

                <div class="form-grid-three">
                    <div class="form-group">
                        <label><?php echo __('auth_bac_label'); ?></label>
                        <div class="form-group-icon-wrapper">
                            <span class="input-icon-left">🎓</span>
                            <select name="bac_branch" id="bacInput" required>
                                <option value="Sciences Math"><?php echo __('bac_math'); ?></option>
                                <option value="PC"><?php echo __('bac_pc'); ?></option>
                                <option value="SVT"><?php echo __('bac_svt'); ?></option>
                                <option value="Economie"><?php echo __('bac_eco'); ?></option>
                                <option value="Technique"><?php echo __('bac_tech'); ?></option>
                                <option value="Lettres"><?php echo __('bac_letters'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php echo __('auth_average_label'); ?></label>
                        <div class="form-group-icon-wrapper">
                            <span class="input-icon-left">📝</span>
                            <input type="number" step="0.01" min="0" max="20" name="average" id="averageInput" placeholder="15.5" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php echo __('city'); ?></label>
                        <div class="form-group-icon-wrapper">
                            <span class="input-icon-left">📍</span>
                            <input type="text" name="city" id="cityInput" placeholder="<?php if(getLang()==='ar') echo 'مدينتك'; elseif(getLang()==='en') echo 'Your city'; else echo 'Votre ville'; ?>">
                        </div>
                    </div>
                </div>

                <div class="auth-checkbox-group">
                    <input type="checkbox" id="termsConsent" required>
                    <label for="termsConsent">
                        <?php if(getLang()==='ar'): ?>أوافق على <a href="#">شروط الاستخدام</a> و<a href="#">سياسة الخصوصية</a>
                        <?php elseif(getLang()==='en'): ?>I agree to the <a href="#">Terms of Use</a> and <a href="#">Privacy Policy</a>
                        <?php else: ?>J'accepte les <a href="#">Conditions d'utilisation</a> et la <a href="#">Politique de confidentialité</a>
                        <?php endif; ?>
                    </label>
                </div>

                <button type="submit" class="btn-orange-submit"><?php echo __('auth_register_btn'); ?></button>
            </form>
        </div>
    </div>
</div>

<!-- Dev-mode Google modal -->
<?php if (!$googleAuthUrl): ?>
<div id="googleSimModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.6);backdrop-filter:blur(4px);z-index:99999;align-items:center;justify-content:center;padding:16px;">
    <div style="background:var(--white);border:1px solid var(--border-color);border-radius:24px;padding:32px;width:100%;max-width:400px;box-shadow:var(--shadow-lg);text-align:center;position:relative;">
        <button onclick="document.getElementById('googleSimModal').style.display='none'" style="position:absolute;top:14px;right:18px;background:none;border:none;font-size:1.8rem;cursor:pointer;color:var(--text-muted);">&times;</button>
        <svg viewBox="0 0 24 24" width="40" height="40" style="margin:0 auto 12px;display:block;" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
        </svg>
        <h4 style="font-size:1.2rem;font-weight:800;color:var(--text-dark);margin-bottom:4px;font-family:'Outfit',sans-serif;">Choisissez un compte</h4>
        <p style="color:var(--text-muted);font-size:0.82rem;margin-bottom:20px;">pour continuer vers Maslaki <span style="background:var(--bg-light);color:#4285F4;border-radius:4px;padding:1px 6px;font-size:0.75rem;font-weight:700;">Mode Dev</span></p>
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
                <input type="hidden" name="name" value="<?php echo htmlspecialchars($acc['name']); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($acc['email']); ?>">
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
document.getElementById('googleSignUpBtn').addEventListener('click', function(e) { e.preventDefault(); document.getElementById('googleSimModal').style.display = 'flex'; });
document.getElementById('googleSimModal').addEventListener('click', function(e) { if (e.target === this) this.style.display = 'none'; });
</script>
<?php endif; ?>

<script>
// Password visibility toggle
document.getElementById('togglePassword').addEventListener('click', function() {
    const p = document.getElementById('passwordInput');
    p.type = p.type === 'password' ? 'text' : 'password';
    this.textContent = p.type === 'password' ? '👁️' : '🙈';
});

// Password strength meter
document.getElementById('passwordInput').addEventListener('input', function() {
    const val = this.value, bar = document.getElementById('strengthBar'), txt = document.getElementById('strengthText');
    let s = 0;
    if (val.length >= 6) s++;
    if (val.match(/[a-z]/) && val.match(/[A-Z]/)) s++;
    if (val.match(/\d/) || val.match(/[^a-zA-Z\d]/)) s++;
    const states = [
        ['0%', '#ef4444', ''],
        ['33%', '#ef4444', '<?php if(getLang()==="ar") echo "ضعيف 🔴"; elseif(getLang()==="en") echo "Weak 🔴"; else echo "Faible 🔴"; ?>'],
        ['66%', '#f97316', '<?php if(getLang()==="ar") echo "متوسط 🟠"; elseif(getLang()==="en") echo "Medium 🟠"; else echo "Moyen 🟠"; ?>'],
        ['100%', '#10b981', '<?php if(getLang()==="ar") echo "قوي 🟢"; elseif(getLang()==="en") echo "Strong 🟢"; else echo "Fort 🟢"; ?>'],
    ];
    const i = val.length === 0 ? 0 : s;
    bar.style.width = states[i][0]; bar.style.backgroundColor = states[i][1]; txt.textContent = states[i][2]; txt.style.color = states[i][1];
});
</script>

<?php require "../includes/footer.php"; ?>