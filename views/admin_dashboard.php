<?php
require_once '../includes/lang_helper.php';
require '../config/DataBase.php';
require_once '../includes/platform_admin.php';
require_platform_admin($pdo);

// Determine if the current user is superadmin (needed to show/hide role management card)
$isSuperAdmin = is_superadmin($pdo);

$pageTitle = __('platform_admin_dashboard_title');
require '../includes/header.php';
?>

<style>
/* ── Admin Dashboard — self-contained styles ── */
.dashboard-container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 32px 24px 60px;
}

/* ── Banner ── */
.dashboard-banner {
    background: linear-gradient(135deg, var(--primary, #1e3a8a) 0%, #1d4ed8 60%, #1e40af 100%);
    border-radius: 24px;
    padding: 48px 44px;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 44px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 60px -10px rgba(30,58,138,0.45);
    border-left: none !important;
}

/* decorative orbs */
.dashboard-banner::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 260px; height: 260px;
    border-radius: 50%;
    background: rgba(249,115,22,0.12);
    pointer-events: none;
}
.dashboard-banner::after {
    content: '';
    position: absolute;
    bottom: -80px; left: -40px;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
    pointer-events: none;
}

.banner-content { position: relative; z-index: 1; }

.welcome-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2);
    padding: 5px 16px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 16px;
    backdrop-filter: blur(6px);
    display: block;
}

.dashboard-banner h1 {
    font-size: 2.2rem;
    font-weight: 900;
    margin: 0 0 10px;
    letter-spacing: -0.5px;
    line-height: 1.15;
}

.dashboard-banner p {
    color: rgba(255,255,255,0.75);
    font-size: 0.95rem;
    line-height: 1.6;
    max-width: 540px;
    margin: 0;
}

/* ── Section header ── */
.section-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 28px;
}

.section-header h2 {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--primary, #1e3a8a);
    margin: 0;
    position: relative;
}

.section-header h2::after {
    content: '';
    display: block;
    width: 36px;
    height: 3px;
    background: var(--accent, #f97316);
    border-radius: 2px;
    margin-top: 6px;
}

/* ── Tool cards grid ── */
.quick-links {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
    gap: 20px;
}

.quick-card {
    background: var(--white, #fff);
    border-radius: 20px;
    padding: 28px 26px;
    border: 1px solid var(--border-color, #e2e8f0);
    border-left: 4px solid var(--primary-light, #3b82f6);
    display: flex;
    align-items: center;
    gap: 22px;
    text-decoration: none;
    color: var(--text-dark, #0f172a);
    transition: transform 0.25s cubic-bezier(0.4,0,0.2,1),
                box-shadow 0.25s cubic-bezier(0.4,0,0.2,1),
                border-color 0.25s;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    position: relative;
    overflow: hidden;
}

.quick-card::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(30,58,138,0.03) 0%, transparent 60%);
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
}

.quick-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 45px rgba(30,58,138,0.13);
}

.quick-card:hover::after { opacity: 1; }

/* Institution card */
.quick-card.q-institution { border-left-color: #10b981; }
.quick-card.q-institution:hover { box-shadow: 0 20px 45px rgba(16,185,129,0.15); }

/* Reviews card */
.quick-card:nth-child(1) { border-left-color: #6366f1; }
.quick-card:nth-child(1):hover { box-shadow: 0 20px 45px rgba(99,102,241,0.15); }

/* Notifications card */
.quick-card.q-primary { border-left-color: #f97316; }
.quick-card.q-primary:hover { box-shadow: 0 20px 45px rgba(249,115,22,0.18); }

/* Superadmin card */
.quick-card.q-superadmin { border-left-color: #f59e0b; }
.quick-card.q-superadmin:hover { box-shadow: 0 20px 45px rgba(245,158,11,0.18); }

.q-icon {
    font-size: 2rem;
    width: 58px;
    height: 58px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-light, #f8fafc);
    border-radius: 16px;
    flex-shrink: 0;
    transition: transform 0.3s ease;
}

.quick-card:hover .q-icon { transform: scale(1.1) rotate(-4deg); }

.q-info { flex: 1; min-width: 0; }

.q-info h3 {
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--primary, #1e3a8a);
    margin: 0 0 6px;
    letter-spacing: -0.2px;
}

.q-info p {
    font-size: 0.83rem;
    color: var(--text-muted, #64748b);
    margin: 0;
    line-height: 1.5;
}

.q-arrow {
    font-size: 1.3rem;
    color: var(--border-color, #e2e8f0);
    transition: transform 0.25s ease, color 0.25s;
    flex-shrink: 0;
}

.quick-card:hover .q-arrow {
    transform: translateX(6px);
    color: var(--primary-light, #3b82f6);
}

/* ── Dark mode ── */
[data-theme="dark"] .dashboard-banner {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #1e3a8a 100%);
    box-shadow: 0 20px 60px -10px rgba(0,0,0,0.55);
}
[data-theme="dark"] .quick-card {
    background: #151e32;
    border-color: #1e293b;
    color: #f8fafc;
}
[data-theme="dark"] .quick-card:hover {
    box-shadow: 0 20px 45px rgba(0,0,0,0.4);
}
[data-theme="dark"] .q-icon { background: #0b1121; }
[data-theme="dark"] .q-info h3 { color: #93c5fd; }
[data-theme="dark"] .section-header h2 { color: #93c5fd; }

/* ── Responsive ── */
@media (max-width: 768px) {
    .dashboard-banner { padding: 32px 24px; border-radius: 18px; }
    .dashboard-banner h1 { font-size: 1.6rem; }
    .dashboard-container { padding: 20px 16px 40px; }
    .quick-links { grid-template-columns: 1fr; }
    .quick-card { padding: 22px 20px; gap: 16px; }
    .q-icon { width: 50px; height: 50px; font-size: 1.6rem; }
}
</style>

<div class="dashboard-container">
    <header class="dashboard-banner">
        <div class="banner-content">
            <span class="welcome-tag">
                <?php if ($isSuperAdmin): ?>
                    👑 Superadmin
                <?php else: ?>
                    <?php echo __('platform_admin_badge'); ?>
                <?php endif; ?>
            </span>
            <h1><?php echo __('platform_admin_dashboard_title'); ?></h1>
            <p><?php echo __('platform_admin_dashboard_intro'); ?></p>
            <?php if (!$isSuperAdmin): ?>
                <p style="margin-top:12px;font-size:0.82rem;color:rgba(255,255,255,0.5);background:rgba(255,255,255,0.06);display:inline-block;padding:6px 14px;border-radius:8px;border:1px solid rgba(255,255,255,0.1);">
                    ℹ️ <?php echo __('admin_info_role_restricted'); ?>
                </p>
            <?php endif; ?>
        </div>
    </header>

    <div class="dashboard-grid">
        <section class="dash-main" style="grid-column: 1 / -1;">
            <div class="section-header">
                <h2><?php echo __('platform_admin_tools_heading'); ?></h2>
            </div>
            <div class="quick-links">
                <a href="admin_reviews.php" class="quick-card">
                    <div class="q-icon">💬</div>
                    <div class="q-info">
                        <h3><?php echo __('platform_admin_card_reviews_title'); ?></h3>
                        <p><?php echo __('platform_admin_card_reviews_desc'); ?></p>
                    </div>
                    <span class="q-arrow">→</span>
                </a>

                <a href="admin_send_notification.php" class="quick-card q-primary">
                    <div class="q-icon">📢</div>
                    <div class="q-info">
                        <h3><?php echo __('platform_admin_card_notifications_title'); ?></h3>
                        <p><?php echo __('platform_admin_card_notifications_desc'); ?></p>
                    </div>
                    <span class="q-arrow">→</span>
                </a>

                <a href="admin_add_institution.php" class="quick-card q-institution">
                    <div class="q-icon">🏫</div>
                    <div class="q-info">
                        <h3><?php echo __('admin_card_add_institution_title'); ?></h3>
                        <p><?php echo __('admin_card_add_institution_desc'); ?></p>
                    </div>
                    <span class="q-arrow">→</span>
                </a>

                <?php if ($isSuperAdmin): ?>
                <a href="admin_users_manage.php" class="quick-card q-superadmin">
                    <div class="q-icon">👑</div>
                    <div class="q-info">
                        <h3><?php echo __('platform_admin_card_users_title'); ?></h3>
                        <p><?php echo __('platform_admin_card_users_desc'); ?></p>
                    </div>
                    <span class="q-arrow">→</span>
                </a>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
