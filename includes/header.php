<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/DataBase.php";
require_once __DIR__ . "/lang_helper.php";

// Detect base path
$isInViews = strpos($_SERVER['PHP_SELF'], '/views/') !== false;
$base = $isInViews ? '../' : '';

// Get unread notifications count (New Schema); detect platform admin (organizer staff)
$unreadCount = 0;
$isPlatformAdmin = false;
if (isset($_SESSION['user_id'])) {
    try {
        $userId = $_SESSION['user_id'];
        $sql = "SELECT COUNT(*) FROM notifications n
                LEFT JOIN user_notifications un ON n.id = un.notification_id AND un.user_id = ?
                WHERE (n.is_global = 1 OR n.target_user_id = ?)
                AND (un.is_read IS NULL OR un.is_read = 0)
                AND (un.is_deleted IS NULL OR un.is_deleted = 0)";
        $notifStmt = $pdo->prepare($sql);
        $notifStmt->execute([$userId, $userId]);
        $unreadCount = $notifStmt->fetchColumn();

        $roleStmt = $pdo->prepare("SELECT role FROM students WHERE id = ? LIMIT 1");
        $roleStmt->execute([$userId]);
        $isPlatformAdmin = ($roleStmt->fetchColumn() === 'admin');
    } catch (Exception $e) {}
}
?>

<!DOCTYPE html>
<html lang="<?php echo getLang(); ?>" dir="<?php echo getLang() === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maslaki — <?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : __('find_school'); ?></title>
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</head>
<body>

    <div class="toast-container" id="toastContainer"></div>

    <nav class="navbar">
        <div class="nav-container">
            <a href="<?php echo $base; ?>index.php" class="brand">
                <img src="<?php echo $base; ?>assets/images/logo.png" alt="Maslaki Logo" class="brand-logo" style="height: 48px; margin-right: 12px; object-fit: contain;">
                <span class="logo-text">Maslaki</span>
            </a>
            
            <ul class="nav-links">
                <li><a href="<?php echo $base; ?>index.php"><?php echo __('home'); ?></a></li>
                <li><a href="<?php echo $base; ?>views/institutions.php"><?php echo __('institutions'); ?></a></li>
                <li><a href="<?php echo $base; ?>views/orientation_explore.php"><?php echo __('orientation'); ?></a></li>
                <?php if (!empty($isPlatformAdmin)): ?>
                    <li><a href="<?php echo $base; ?>views/admin_dashboard.php" class="nav-platform-admin"><?php echo __('platform_admin_nav'); ?></a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="<?php echo $base; ?>views/ai_form.php" class="btn btn-accent ai-btn-nav"><?php echo __('ai_orientation'); ?> 🤖</a></li>
                    <li class="notif-menu-item">
                        <div class="notif-icon-wrapper" id="notifBtn" title="<?php echo __('notifications'); ?>">
                            <span class="notif-bell">🔔</span>
                            <?php if ($unreadCount > 0): ?>
                                <span class="notif-badge" id="notifBadge"><?php echo $unreadCount; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="notif-dropdown" id="notifDropdown">
                            <div class="notif-dropdown-header">
                                <h3><?php echo __('notifications'); ?></h3>
                                <button id="markAllRead" class="btn-text-only"><?php echo __('mark_all_read'); ?></button>
                            </div>
                            <div class="notif-dropdown-list" id="notifList">
                                <div class="notif-loading"><?php echo __('loading'); ?></div>
                            </div>
                            <a href="<?php echo $base; ?>views/notifications.php" class="notif-see-all"><?php echo __('see_all_notifications'); ?></a>
                        </div>
                    </li>
                    <li class="user-menu-item">
                        <div class="user-profile-icon" id="profileBtn" title="<?php echo __('profile'); ?>">👤</div>
                        <div class="profile-dropdown" id="profileDropdown">
                            <a href="<?php echo $base; ?>views/dashboard.php" class="dropdown-link">
                                <span class="dropdown-icon">👤</span> <?php echo __('profile'); ?>
                            </a>
                            <div class="dropdown-divider"></div>
                            
                            <div class="dropdown-section">
                                <label><?php echo __('appearance'); ?></label>
                                <div class="theme-switch">
                                    <button id="themeLightBtn" class="theme-switch-btn active">☀️ <?php echo __('light'); ?></button>
                                    <button id="themeDarkBtn" class="theme-switch-btn">🌙 <?php echo __('dark'); ?></button>
                                </div>
                            </div>
                            
                            <div class="dropdown-section">
                                <label><?php echo __('language'); ?></label>
                                <div class="lang-selector">
                                    <?php 
                                    $current_params = $_GET;
                                    function getLangUrl($l, $params) {
                                        $params['lang'] = $l;
                                        return '?' . http_build_query($params);
                                    }
                                    ?>
                                    <a href="<?php echo getLangUrl('fr', $current_params); ?>" class="<?php echo getLang() == 'fr' ? 'active' : ''; ?>">FR</a>
                                    <a href="<?php echo getLangUrl('en', $current_params); ?>" class="<?php echo getLang() == 'en' ? 'active' : ''; ?>">EN</a>
                                    <a href="<?php echo getLangUrl('ar', $current_params); ?>" class="<?php echo getLang() == 'ar' ? 'active' : ''; ?>">AR</a>
                                </div>
                            </div>
                            
                            <div class="dropdown-divider"></div>
                            <a href="<?php echo $base; ?>views/logout.php" class="dropdown-link logout-red">
                                <span class="dropdown-icon">🚪</span> <?php echo __('logout'); ?>
                            </a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="<?php echo $base; ?>views/login.php"><?php echo __('login'); ?></a></li>
                    <li><a href="<?php echo $base; ?>views/register.php" class="btn btn-accent"><?php echo __('register'); ?></a></li>
                <?php endif; ?>
            </ul>

        </div>
    </nav>

<style>
.notif-see-all { display: block; text-align: center; padding: 12px; font-size: 0.85rem; font-weight: 700; color: var(--primary); background: var(--bg-light); text-decoration: none; border-top: 1px solid var(--border-color); transition: var(--transition); }
.notif-see-all:hover { background: var(--primary); color: #fff; }

/* Branding Styles */
.brand { display: flex; align-items: center; gap: 12px; text-decoration: none; transition: transform 0.2s ease; }
.brand:hover { transform: scale(1.02); }
[data-theme="dark"] .brand-logo { filter: drop-shadow(0 0 2px rgba(255, 255, 255, 0.9)) drop-shadow(0 0 10px rgba(255, 255, 255, 0.3)); }
.logo-text { font-size: 1.6rem; font-weight: 900; color: var(--primary-dark, var(--primary)); letter-spacing: -0.5px; }

/* Notification Dropdown Animation */
.notif-dropdown {
    opacity: 0;
    transform: translateY(10px) scale(0.98);
    pointer-events: none;
    transition: opacity 0.3s ease, transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    display: block; /* Ensure it stays in DOM for transition */
    visibility: hidden;
}
.notif-dropdown.active {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
    visibility: visible;
}

/* RTL Global Adjustments */
html[dir="rtl"] { text-align: right; }
html[dir="rtl"] body { direction: rtl; }
html[dir="rtl"] .nav-links { margin-right: auto; margin-left: 0; }
html[dir="rtl"] .hero-text h1, html[dir="rtl"] .hero-text p { text-align: right; }
html[dir="rtl"] .filter-sidebar { text-align: right; }
html[dir="rtl"] .card-info-row { justify-content: flex-start; }
html[dir="rtl"] .card-actions { flex-direction: row-reverse; }
html[dir="rtl"] .details-arrow { transform: rotate(180deg); }
html[dir="rtl"] .btn-link { float: right; }
html[dir="rtl"] .nav-container { flex-direction: row; }
html[dir="rtl"] .profile-dropdown { left: 0; right: auto; }
html[dir="rtl"] .notif-dropdown { left: 0; right: auto; }
html[dir="rtl"] .toast { right: auto; left: 20px; }
html[dir="rtl"] .sidebar-card { text-align: right; }
html[dir="rtl"] .auth-container { text-align: right; }
/* Refinements for exactly matching LTR visual logic */
html[dir="rtl"] .notif-full-item:hover { transform: translateX(-5px); }
html[dir="rtl"] .notif-full-item.unread { border-right: 4px solid var(--primary); border-left: 1px solid var(--border-color); }
html[dir="rtl"] .q-primary { border-right: 4px solid var(--accent); border-left: none; }
html[dir="rtl"] .q-arrow { transform: rotate(180deg); display: inline-block; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const base = '<?php echo $base; ?>';
    const notifBtn = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifList = document.getElementById('notifList');
    const notifBadge = document.getElementById('notifBadge');
    const toastContainer = document.getElementById('toastContainer');
    
    let lastNotifId = localStorage.getItem('lastNotifId') || 0;

    // Polling function
    function checkNotifications() {
        fetch(base + 'check_new_notifications.php')
            .then(res => res.json())
            .then(data => {
                // Update badge
                if (data.unread_count > 0) {
                    if (!document.getElementById('notifBadge')) {
                        const newBadge = document.createElement('span');
                        newBadge.id = 'notifBadge';
                        newBadge.className = 'notif-badge notif-badge-anim';
                        newBadge.textContent = data.unread_count;
                        document.getElementById('notifBtn').appendChild(newBadge);
                    } else {
                        const badge = document.getElementById('notifBadge');
                        if (badge.textContent != data.unread_count) {
                            badge.textContent = data.unread_count;
                            badge.classList.add('notif-badge-anim');
                            setTimeout(() => badge.classList.remove('notif-badge-anim'), 500);
                        }
                    }
                } else if (document.getElementById('notifBadge')) {
                    document.getElementById('notifBadge').remove();
                }

                // Check for new latest notification to show toast
                if (data.latest && data.latest.id > lastNotifId) {
                    showToast(data.latest);
                    lastNotifId = data.latest.id;
                    localStorage.setItem('lastNotifId', lastNotifId);
                }
            });
    }

    function showToast(notif) {
        const toast = document.createElement('div');
        toast.className = `toast type-${notif.type}`;
        
        let icon = '🔔';
        switch(notif.type) {
            case 'system': icon = '⚙️'; break;
            case 'school': icon = '🏫'; break;
            case 'maintenance': icon = '🛠️'; break;
            case 'announcement': icon = '📢'; break;
        }

        toast.innerHTML = `
            <div class="toast-icon">${icon}</div>
            <div class="toast-body">
                <div class="toast-title">${notif.title}</div>
                <div class="toast-msg">${notif.message.substring(0, 80)}${notif.message.length > 80 ? '...' : ''}</div>
            </div>
            <button class="toast-close">&times;</button>
        `;

        toastContainer.appendChild(toast);
        setTimeout(() => toast.classList.add('active'), 100);

        toast.querySelector('.toast-close').addEventListener('click', () => {
            toast.classList.remove('active');
            setTimeout(() => toast.remove(), 400);
        });

        // Auto remove after 6 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.remove('active');
                setTimeout(() => toast.remove(), 400);
            }
        }, 6000);
    }

    if (notifBtn) {
        // Initial check
        checkNotifications();
        // Poll every 30 seconds
        setInterval(checkNotifications, 30000);

        notifBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('active');
            if (notifDropdown.classList.contains('active')) {
                loadNotifications();
                
                // Auto mark all as read instantly
                fetch(base + 'mark_notification_read.php?all=1')
                    .then(res => res.json())
                    .then(() => {
                        const badge = document.getElementById('notifBadge');
                        if (badge) {
                            badge.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            badge.style.opacity = '0';
                            badge.style.transform = 'scale(0)';
                            setTimeout(() => badge.remove(), 300);
                        }
                    });
            }
        });
    }

    function loadNotifications() {
        fetch(base + 'get_notifications.php')
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    notifList.innerHTML = '<div class="notif-empty"><?php echo __("no_notifications"); ?></div>';
                    return;
                }

                notifList.innerHTML = data.map(n => `
                    <div class="notif-item ${n.is_read == 0 ? 'unread' : ''}" onclick="handleNotifClick(${n.id}, '${n.related_link}', this)">
                        <div class="notif-icon-circle">${n.icon}</div>
                        <div class="notif-content">
                            <p><strong>${n.title}</strong></p>
                            <p>${n.message.substring(0, 60)}...</p>
                            <span class="notif-time">${n.time_ago}</span>
                        </div>
                    </div>
                `).join('');
            });
    }

    window.handleNotifClick = function(id, link, element) {
        fetch(base + 'mark_notification_read.php?id=' + id)
            .then(res => res.json())
            .then(() => {
                element.classList.remove('unread');
                checkNotifications();
                if (link && link !== 'null') {
                    window.location.href = base + link;
                }
            });
    }

    const markAllReadBtn = document.getElementById('markAllRead');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            fetch(base + 'mark_notification_read.php?all=1')
                .then(res => res.json())
                .then(() => {
                    loadNotifications();
                    checkNotifications();
                });
        });
    }

    // Profile Dropdown
    const profileBtn = document.getElementById('profileBtn');
    const profileDropdown = document.getElementById('profileDropdown');
    if (profileBtn) {
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
        });
    }

    document.addEventListener('click', () => {
        if (profileDropdown) profileDropdown.classList.remove('active');
        if (notifDropdown) notifDropdown.classList.remove('active');
    });

    // Theme Switching
    const themeLightBtn = document.getElementById('themeLightBtn');
    const themeDarkBtn = document.getElementById('themeDarkBtn');
    if (themeLightBtn && themeDarkBtn) {
        themeLightBtn.addEventListener('click', (e) => {
            e.preventDefault();
            document.documentElement.setAttribute('data-theme', 'light');
            localStorage.setItem('theme', 'light');
        });
        themeDarkBtn.addEventListener('click', (e) => {
            e.preventDefault();
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        });
    }

    // Language Change Animation
    const langLinks = document.querySelectorAll('.lang-selector a');
    langLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            document.body.style.transition = 'opacity 0.3s ease';
            document.body.style.opacity = '0';
            setTimeout(() => {
                window.location.href = url;
            }, 300);
        });
    });

    // Mobile Menu
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    if (menuToggle) {
        menuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            navLinks.classList.toggle('mobile-active');
        });
    }
});
</script>

<main class="main-content">

