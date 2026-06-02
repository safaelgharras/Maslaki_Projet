<?php
require_once "../includes/lang_helper.php";
require_once "../config/google_config.php";
$pageTitle = __('auth_login_title');
require "../includes/header.php";

// Build the real Google OAuth URL
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
/* Reset and layout overrides for Notion/Stripe style login */

/* Show global navbar, keep it sticky/fixed */
.navbar {
    display: block !important;
}

/* Footer is visible on the login page */

/* Desktop layout — natural scroll so footer is visible */
@media (min-width: 993px) {
    body {
        margin: 0 !important;
        padding: 0 !important;
        overflow-y: auto !important;
        height: auto !important;
    }
    .main-content {
        min-height: calc(100vh - 60px) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 40px 24px !important;
        background: var(--bg-light) !important;
    }
}

/* Tablet layout */
@media (max-width: 992px) and (min-width: 577px) {
    body {
        margin: 0 !important;
        padding: 0 !important;
        overflow-y: auto !important;
        height: auto !important;
    }
    .main-content {
        min-height: calc(100vh - 60px) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 32px 20px !important;
        background: var(--bg-light) !important;
    }
}

/* Mobile layout */
@media (max-width: 576px) {
    body {
        overflow-y: auto !important;
        height: auto !important;
    }
    .main-content {
        min-height: calc(100vh - 60px) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 20px 16px !important;
        background: var(--bg-light) !important;
    }
}

/* Centered modern auth split card */
.auth-split {
    display: grid;
    grid-template-columns: 1fr 1fr;
    width: 100%;
    max-width: 960px;
    height: 100%;
    max-height: 580px;
    background: var(--white);
    border: 1px solid var(--border-color);
    border-radius: 24px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    margin: 0 auto !important;
    transition: max-height 0.3s ease;
}

/* Responsive splits */
@media (max-width: 992px) {
    .auth-split {
        grid-template-columns: 0.45fr 0.55fr;
        max-width: 800px;
        max-height: 520px;
    }
}

@media (max-width: 576px) {
    .auth-split {
        grid-template-columns: 1fr;
        max-width: 400px;
        height: auto;
        max-height: none;
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
    }
}

/* Left panel: clean graphic / illustration side */
.auth-visual-side {
    background: linear-gradient(135deg, rgba(244, 247, 254, 0.5) 0%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 247, 237, 0.3) 100%) !important;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px;
    border-right: 1px solid var(--border-color);
}

[data-theme="dark"] .auth-visual-side {
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.5) 0%, rgba(30, 41, 59, 0.5) 100%) !important;
}

[dir="rtl"] .auth-visual-side {
    border-right: none;
    border-left: 1px solid var(--border-color);
}

/* Subtle graphic decoration */
.auth-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    z-index: 1;
    pointer-events: none;
    opacity: 0.15;
}

.auth-orb-1 {
    width: 220px;
    height: 220px;
    background: var(--primary-light);
    top: -20px;
    left: -20px;
}

.auth-orb-2 {
    width: 250px;
    height: 250px;
    background: var(--accent);
    bottom: -40px;
    right: -40px;
}

.auth-students-img {
    max-width: 85%;
    max-height: 85%;
    width: auto;
    height: auto;
    object-fit: contain;
    z-index: 5;
    filter: drop-shadow(0 15px 30px rgba(0,0,0,0.06));
    animation: float 6s ease-in-out infinite;
}

/* Right panel: Login form side */
.auth-form-side {
    background: var(--white) !important;
    padding: 40px 50px !important;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100%;
}

@media (max-width: 576px) {
    .auth-form-side {
        padding: 20px 10px !important;
        background: transparent !important;
    }
}

.auth-form-container {
    width: 100%;
    max-width: 340px;
}

/* Compact headers and spacing */
.auth-header {
    margin-bottom: 24px;
    text-align: center;
}

.auth-header h3 {
    color: var(--text-dark) !important;
    font-family: 'Outfit', sans-serif;
    font-size: 1.8rem !important;
    font-weight: 800 !important;
    letter-spacing: -0.5px;
    margin-bottom: 6px;
}

.auth-header p {
    color: var(--text-muted) !important;
    font-size: 0.88rem;
}

.auth-header p a {
    color: var(--primary-light) !important;
    font-weight: 700 !important;
    text-decoration: none;
    transition: var(--transition);
}

.auth-header p a:hover {
    color: var(--accent) !important;
    text-decoration: underline;
}

/* Google Sign-in Button layout */
.btn-google {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    background: var(--white) !important;
    border: 1.5px solid var(--border-color) !important;
    color: var(--text-dark) !important;
    height: 46px !important;
    border-radius: 12px !important;
    font-weight: 700 !important;
    font-size: 0.9rem !important;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease !important;
    margin-bottom: 16px;
}

.btn-google svg {
    width: 18px;
    height: 18px;
}

.btn-google:hover {
    background: var(--bg-light) !important;
    border-color: var(--primary-light) !important;
    transform: translateY(-1px) !important;
}

/* Divider 'Ou' */
.auth-divider {
    display: flex;
    align-items: center;
    text-align: center;
    color: var(--text-muted) !important;
    margin: 16px 0 !important;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.auth-divider::before, .auth-divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid var(--border-color) !important;
}

.auth-divider::before {
    margin-right: 12px;
}

.auth-divider::after {
    margin-left: 12px;
}

/* Compact Input Groups */
.form-group {
    margin-bottom: 16px;
}

.form-group label {
    font-size: 0.82rem !important;
    font-weight: 600 !important;
    color: var(--text-dark) !important;
    margin-bottom: 6px !important;
    display: block;
}

.form-group-icon-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.form-group-icon-wrapper input {
    background: var(--bg-light) !important;
    border: 1px solid var(--border-color) !important;
    color: var(--text-dark) !important;
    height: 44px !important;
    border-radius: 12px !important;
    padding-left: 44px !important;
    padding-right: 16px !important;
    font-size: 0.9rem !important;
    font-weight: 500 !important;
    width: 100%;
    transition: all 0.2s ease !important;
}

.form-group-icon-wrapper input:focus {
    background: var(--white) !important;
    border-color: var(--accent) !important;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1) !important;
    outline: none;
}

.form-group-icon-wrapper .input-icon-left {
    position: absolute;
    left: 16px !important;
    font-size: 1rem !important;
    color: var(--text-muted) !important;
    pointer-events: none;
}

.form-group-icon-wrapper .input-icon-right {
    position: absolute;
    right: 16px !important;
    font-size: 1rem !important;
    color: var(--text-muted) !important;
    cursor: pointer;
}

.btn-orange-submit {
    width: 100%;
    height: 44px !important;
    border-radius: 12px !important;
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%) !important;
    border: none !important;
    color: #ffffff !important;
    font-size: 0.95rem !important;
    font-weight: 700 !important;
    box-shadow: 0 8px 20px -6px rgba(249, 115, 22, 0.25) !important;
    cursor: pointer;
    transition: all 0.2s ease !important;
    margin-top: 4px;
}

.btn-orange-submit:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 10px 22px -5px rgba(249, 115, 22, 0.35) !important;
}

/* RTL overrides */
[dir="rtl"] .form-group-icon-wrapper input {
    padding-left: 16px !important;
    padding-right: 44px !important;
}

[dir="rtl"] .form-group-icon-wrapper .input-icon-left {
    left: auto !important;
    right: 16px !important;
}

[dir="rtl"] .form-group-icon-wrapper .input-icon-right {
    right: auto !important;
    left: 16px !important;
}

[dir="rtl"] .google-sim-btn {
    text-align: right;
}
</style>

<div class="auth-split">
    <!-- ===== Left Visual Side (Modern Educational Theme) ===== -->
    <div class="auth-visual-side">
        <div class="auth-orb auth-orb-1"></div>
        <div class="auth-orb auth-orb-2"></div>
        <img src="../assets/images/students_illustration.png" alt="Students Illustration" class="auth-students-img">
    </div>

    <!-- ===== Right Form Side (Clean modern login card) ===== -->
    <div class="auth-form-side">
        <div class="auth-form-container">
            <div class="auth-header">
                <h3><?php echo __('auth_login_title'); ?></h3>
                <p><?php echo __('auth_no_account'); ?> <a href="register.php"><?php echo __('register'); ?></a></p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="msg msg-error" style="border-radius: 12px; margin-bottom: 15px; padding: 10px; font-size: 0.85rem;">⚠️ <?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="msg msg-success" style="border-radius: 12px; margin-bottom: 15px; padding: 10px; font-size: 0.85rem;">✓ <?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>

            <!-- Google Sign-In Button -->
            <?php if ($googleAuthUrl): ?>
                <a href="<?php echo htmlspecialchars($googleAuthUrl); ?>" class="btn-google" id="googleLoginBtn">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                    </svg>
                    <span>
                        <?php 
                        if (getLang() === 'ar') echo 'متابعة باستخدام جوجل';
                        elseif (getLang() === 'en') echo 'Continue with Google';
                        else echo 'Continuer avec Google';
                        ?>
                    </span>
                </a>
            <?php else: ?>
                <!-- Dev mode simulator button -->
                <button type="button" class="btn-google" id="googleLoginBtn">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                    </svg>
                    <span>
                        <?php 
                        if (getLang() === 'ar') echo 'متابعة باستخدام جوجل';
                        elseif (getLang() === 'en') echo 'Continue with Google';
                        else echo 'Continuer avec Google';
                        ?>
                    </span>
                </button>
            <?php endif; ?>

            <div class="auth-divider">
                <span>
                    <?php 
                    if (getLang() === 'ar') echo 'أو';
                    elseif (getLang() === 'en') echo 'or';
                    else echo 'ou';
                    ?>
                </span>
            </div>

            <!-- Standard Credentials Form -->
            <form method="POST" action="../login_process.php" id="loginForm">
                
                <!-- Email address -->
                <div class="form-group">
                    <label><?php echo __('auth_email_label'); ?></label>
                    <div class="form-group-icon-wrapper">
                        <span class="input-icon-left">✉️</span>
                        <input type="email" name="email" placeholder="<?php 
                            if (getLang() === 'ar') echo 'email@example.com';
                            else echo 'name@example.com';
                        ?>" required>
                    </div>
                </div>

                <!-- Password and show/hide toggle -->
                <div class="form-group">
                    <label><?php echo __('auth_password_label'); ?></label>
                    <div class="form-group-icon-wrapper">
                        <span class="input-icon-left">🔒</span>
                        <input type="password" id="passwordInput" name="password" placeholder="••••••••" required>
                        <span class="input-icon-right" id="togglePassword">👁️</span>
                    </div>
                </div>

                <button type="submit" class="btn-orange-submit">
                    <?php echo __('auth_login_btn'); ?>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Dev-mode Google Simulator Modal -->
<?php if (!$googleAuthUrl): ?>
<div id="googleSimModalLogin" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.6);backdrop-filter:blur(4px);z-index:99999;align-items:center;justify-content:center;padding:16px;font-family:'Inter',sans-serif;">
    <div style="background:var(--white);border:1px solid var(--border-color);border-radius:24px;padding:32px;width:100%;max-width:400px;box-shadow:var(--shadow-lg);text-align:center;position:relative;animation:reveal 0.3s ease-out;">
        <button onclick="document.getElementById('googleSimModalLogin').style.display='none'" style="position:absolute;top:14px;right:18px;background:none;border:none;font-size:1.8rem;cursor:pointer;color:var(--text-muted);">&times;</button>
        <svg viewBox="0 0 24 24" width="40" height="40" style="margin:0 auto 12px;display:block;" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
        </svg>
        <h4 style="font-size:1.2rem;font-weight:800;color:var(--text-dark);margin-bottom:4px;font-family:'Outfit',sans-serif;">
            <?php 
            if (getLang() === 'ar') echo 'اختر حسابًا';
            elseif (getLang() === 'en') echo 'Choose an account';
            else echo 'Choisissez un compte';
            ?>
        </h4>
        <p style="color:var(--text-muted);font-size:0.82rem;margin-bottom:20px;">
            <?php 
            if (getLang() === 'ar') echo 'للمتابعة إلى مسلكي';
            elseif (getLang() === 'en') echo 'to continue to Maslaki';
            else echo 'pour continuer vers Maslaki';
            ?> 
            <span style="background:var(--bg-light);color:#4285F4;border-radius:4px;padding:1px 6px;font-size:0.75rem;font-weight:700;">
                <?php 
                if (getLang() === 'ar') echo 'وضع المطور';
                elseif (getLang() === 'en') echo 'Dev Mode';
                else echo 'Mode Dev';
                ?>
            </span>
        </p>
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
                <button type="submit" class="google-sim-btn">
                    <div style="background:<?php echo $acc['bg']; ?>;color:<?php echo $acc['color']; ?>;width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;flex-shrink:0;"><?php echo $acc['initials']; ?></div>
                    <div class="account-info">
                        <div style="font-weight:600;font-size:0.9rem;color:var(--text-dark);"><?php echo $acc['name']; ?></div>
                        <div style="font-size:0.75rem;color:var(--text-muted);"><?php echo $acc['email']; ?></div>
                    </div>
                </button>
            </form>
            <?php endforeach; ?>
        </div>
        <p style="margin-top:16px;font-size:0.78rem;color:var(--text-muted);">
            <?php 
            if (getLang() === 'ar') echo '💡 أضف مفاتيحك في <code>config/google_config.php</code> لتفعيل تسجيل الدخول الحقيقي من جوجل.';
            elseif (getLang() === 'en') echo '💡 Add your keys in <code>config/google_config.php</code> to enable real Google OAuth login.';
            else echo '💡 Ajoutez vos clés dans <code>config/google_config.php</code> pour activer Google OAuth réel.';
            ?>
        </p>
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
// Toggle Password visibility
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('passwordInput');

togglePassword.addEventListener('click', function () {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    this.textContent = type === 'password' ? '👁️' : '🙈';
});
</script>

<?php require "../includes/footer.php"; ?>