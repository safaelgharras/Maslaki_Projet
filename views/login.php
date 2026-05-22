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

<div class="auth-split">
    <!-- ===== Left Visual Side (Modern Educational Theme) ===== -->
    <div class="auth-visual-side">
        <div class="auth-visual-bg-lines"></div>

        <!-- Floating education decoration boxes -->
        <div class="auth-floating-box box-cap">🎓</div>
        <div class="auth-floating-box box-trophy">🏆</div>
        <div class="auth-floating-box box-school">🏛️</div>

        <!-- Top visual branding logo -->
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 30px; position: relative; z-index: 10;">
            <img src="../assets/images/logo.png" alt="Maslaki" style="height: 32px; object-fit: contain;">
            <span style="font-weight: 900; font-size: 1.3rem; color: #0f172a; font-family: 'Outfit', sans-serif;">Maslaki</span>
        </div>

        <div class="auth-visual-content">
            <h1>Rejoignez <span class="orange-highlight">Maslaki</span><br>et construisez votre avenir</h1>
            <p>Découvrez les meilleurs établissements, trouvez la formation qui vous correspond et réalisez vos ambitions.</p>
            <div class="accent-bar"></div>
        </div>

        <div class="auth-illustration-container">
            <img src="../assets/images/students_illustration.png" alt="Students Illustration" class="auth-students-img">
            
            <!-- Horizontal Features Bar at the bottom -->
            <div class="auth-features-bar">
                <div class="auth-feature-item">
                    <div class="auth-feature-icon">🏢</div>
                    <span class="auth-feature-label">Les meilleurs établissements</span>
                </div>
                <div class="auth-feature-item">
                    <div class="auth-feature-icon">📄</div>
                    <span class="auth-feature-label">Formations variées</span>
                </div>
                <div class="auth-feature-item">
                    <div class="auth-feature-icon">🎯</div>
                    <span class="auth-feature-label">Un avenir réussi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Right Form Side (Clean modern login card) ===== -->
    <div class="auth-form-side">
        <div class="auth-form-container">
            <div class="auth-header">
                <h3>Connexion</h3>
                <p>Pas encore de compte ? <a href="register.php">Créer un compte</a></p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="msg msg-error" style="border-radius: 12px; margin-bottom: 15px;">⚠️ <?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="msg msg-success" style="border-radius: 12px; margin-bottom: 15px;">✓ <?php echo htmlspecialchars($_GET['success']); ?></div>
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
                    <span>Continuer avec Google</span>
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
                    <span>Continuer avec Google</span>
                </button>
            <?php endif; ?>

            <div class="auth-divider"><span>ou</span></div>

            <!-- Standard Credentials Form -->
            <form method="POST" action="../login_process.php" id="loginForm">
                
                <!-- Email address -->
                <div class="form-group">
                    <label>Email</label>
                    <div class="form-group-icon-wrapper">
                        <span class="input-icon-left">✉️</span>
                        <input type="email" name="email" placeholder="nom@exemple.com" required>
                    </div>
                </div>

                <!-- Password and show/hide toggle -->
                <div class="form-group">
                    <label>Mot de passe</label>
                    <div class="form-group-icon-wrapper">
                        <span class="input-icon-left">🔒</span>
                        <input type="password" id="passwordInput" name="password" placeholder="••••••••" required>
                        <span class="input-icon-right" id="togglePassword">👁️</span>
                    </div>
                </div>

                <button type="submit" class="btn-orange-submit">
                    Se connecter
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Dev-mode Google Simulator Modal -->
<?php if (!$googleAuthUrl): ?>
<div id="googleSimModalLogin" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);z-index:99999;align-items:center;justify-content:center;padding:16px;font-family:'Inter',sans-serif;">
    <div style="background:#ffffff;border:1px solid #cbd5e1;border-radius:24px;padding:32px;width:100%;max-width:400px;box-shadow:0 20px 40px rgba(0,0,0,0.1);text-align:center;position:relative;animation:reveal 0.3s ease-out;">
        <button onclick="document.getElementById('googleSimModalLogin').style.display='none'" style="position:absolute;top:14px;right:18px;background:none;border:none;font-size:1.8rem;cursor:pointer;color:#94a3b8;">&times;</button>
        <svg viewBox="0 0 24 24" width="40" height="40" style="margin:0 auto 12px;display:block;" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
        </svg>
        <h4 style="font-size:1.2rem;font-weight:800;color:#0f172a;margin-bottom:4px;font-family:'Outfit',sans-serif;">Choisissez un compte</h4>
        <p style="color:#64748b;font-size:0.82rem;margin-bottom:20px;">pour continuer vers Maslaki <span style="background:#eef2ff;color:#4285F4;border-radius:4px;padding:1px 6px;font-size:0.75rem;font-weight:700;">Mode Dev</span></p>
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
                <button type="submit" style="width:100%;display:flex;align-items:center;gap:14px;padding:12px 15px;border:1px solid #cbd5e1;border-radius:14px;background:#ffffff;cursor:pointer;text-align:left;transition:all 0.2s;" onmouseover="this.style.borderColor='#f97316'" onmouseout="this.style.borderColor='#cbd5e1'">
                    <div style="background:<?php echo $acc['bg']; ?>;color:<?php echo $acc['color']; ?>;width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;flex-shrink:0;"><?php echo $acc['initials']; ?></div>
                    <div style="flex:1;">
                        <div style="font-weight:600;font-size:0.9rem;color:#0f172a;"><?php echo $acc['name']; ?></div>
                        <div style="font-size:0.75rem;color:#64748b;"><?php echo $acc['email']; ?></div>
                    </div>
                </button>
            </form>
            <?php endforeach; ?>
        </div>
        <p style="margin-top:16px;font-size:0.78rem;color:#64748b;">💡 Ajoutez vos clés dans <code>config/google_config.php</code> pour activer Google OAuth réel.</p>
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