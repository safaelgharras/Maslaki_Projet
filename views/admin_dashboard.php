<?php
require_once '../includes/lang_helper.php';
require '../config/DataBase.php';
require_once '../includes/platform_admin.php';
require_platform_admin($pdo);

$pageTitle = __('platform_admin_dashboard_title');
require '../includes/header.php';
?>

<div class="dashboard-container">
    <header class="dashboard-banner" style="border-left: 4px solid var(--orange, #f97316);">
        <div class="banner-content">
            <span class="welcome-tag"><?php echo __('platform_admin_badge'); ?></span>
            <h1><?php echo __('platform_admin_dashboard_title'); ?></h1>
            <p><?php echo __('platform_admin_dashboard_intro'); ?></p>
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

                <a href="admin_users_manage.php" class="quick-card" style="border-left: 4px solid #10b981;">
                    <div class="q-icon">👥</div>
                    <div class="q-info">
                        <h3><?php echo __('platform_admin_card_users_title'); ?></h3>
                        <p><?php echo __('platform_admin_card_users_desc'); ?></p>
                    </div>
                    <span class="q-arrow">→</span>
                </a>
            </div>
        </section>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
