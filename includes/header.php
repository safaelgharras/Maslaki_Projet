<?php
/**
 * header.php — Shared HTML head, navbar, and notification system.
 *
 * Included by every page via require. Provides:
 * - Session initialization and database connection
 * - Language detection and localization setup
 * - Unread notification count query (via user_notifications pivot)
 * - User profile data (role, avatar, initials)
 * - Platform admin detection for navbar badge
 *
 * HTML structure:
 * - <head> with meta tags, title, CSS link, and theme initialization script
 * - <nav> with: logo, nav links (Home, Institutions, Orientation, AI)
 * - Notification bell with dropdown (AJAX polling every 30s)
 * - User profile dropdown with: admin link, dashboard, theme switch, language selector, logout
 * - Theme switching (light/dark via localStorage)
 * - Language switching (FR/EN/AR with page reload)
 * - Mobile responsive menu toggle
 *
 * JavaScript features:
 * - Notification polling and toast popups
 * - Mark notifications as read (individual or all)
 * - Theme toggle with localStorage persistence
 * - Language change with fade transition
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/DataBase.php";
require_once __DIR__ . "/lang_helper.php";

// Detect base path for assets (different for root vs views/ directory)
$isInViews = strpos($_SERVER['PHP_SELF'], '/views/') !== false;
$base = $isInViews ? '../' : '';

// Get unread notifications count and user profile data
// Uses LEFT JOIN on user_notifications pivot to handle both global and targeted notifications
$unreadCount = 0;
$isPlatformAdmin = false;
if (isset($_SESSION['user_id'])) {
    try {
        $userId = $_SESSION['user_id'];
        $sql = "SELECT COUNT(DISTINCT n.id) FROM notifications n
                LEFT JOIN user_notifications un ON n.id = un.notification_id AND un.user_id = ?
                WHERE (n.is_global = 1 OR n.target_user_id = ?)
                AND (un.is_read IS NULL OR un.is_read = 0)
                AND (un.is_deleted IS NULL OR un.is_deleted = 0)";
        $notifStmt = $pdo->prepare($sql);
        $notifStmt->execute([$userId, $userId]);
        $unreadCount = $notifStmt->fetchColumn();

        $profileStmt = $pdo->prepare("SELECT role, name, avatar FROM students WHERE id = ? LIMIT 1");
        $profileStmt->execute([$userId]);
        $profileRow = $profileStmt->fetch(PDO::FETCH_ASSOC);
        $userRole = $profileRow['role'] ?? 'student';
        $isPlatformAdmin = in_array($userRole, ['admin', 'superadmin'], true);
        $isSuperAdminNav  = ($userRole === 'superadmin');

        // Prefer session avatar (set at login), fall back to DB value
        $navAvatar = $_SESSION['user_avatar'] ?? $profileRow['avatar'] ?? '';
        $navName   = $_SESSION['user_name']  ?? $profileRow['name']   ?? '';
        // Build initials fallback (up to 2 chars)
        $initials = '';
        foreach (explode(' ', trim($navName)) as $part) {
            if ($part !== '') $initials .= strtoupper($part[0]);
            if (strlen($initials) >= 2) break;
        }
    } catch (Exception $e) {
        $navAvatar = '';
        $initials  = '?';
    }
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
                        <div class="user-profile-icon" id="profileBtn" title="<?php echo __('profile'); ?>">
                            <?php if (!empty($navAvatar)): ?>
                                <img src="<?php echo htmlspecialchars($navAvatar); ?>" alt="<?php echo htmlspecialchars($navName); ?>" class="nav-avatar-img">
                            <?php elseif (!empty($initials)): ?>
                                <span class="nav-avatar-initials"><?php echo htmlspecialchars($initials); ?></span>
                            <?php else: ?>
                                👤
                            <?php endif; ?>
                        </div>
                        <div class="profile-dropdown" id="profileDropdown">
                            <?php if (!empty($isPlatformAdmin)): ?>
                                <a href="<?php echo $base; ?>views/admin_dashboard.php" class="dropdown-link" style="color: <?php echo $isSuperAdminNav ? '#92400e' : 'var(--orange, #f97316)'; ?>; font-weight: 700; background: <?php echo $isSuperAdminNav ? 'linear-gradient(135deg,#fef3c7,#fde68a)' : 'rgba(249,115,22,0.06)'; ?>; border-radius: 10px; margin: 4px 8px;">
                                    <span class="dropdown-icon"><?php echo $isSuperAdminNav ? '&#x1F451;' : '&#x1F6E1;&#xFE0F;'; ?></span>
                                    <?php echo $isSuperAdminNav ? 'Superadmin' : __('platform_admin_nav'); ?>
                                </a>
                                <div class="dropdown-divider"></div>
                            <?php endif; ?>
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
.nav-platform-admin {
    background: rgba(249, 115, 22, 0.1);
    color: var(--orange, #f97316) !important;
    border: 1px solid rgba(249, 115, 22, 0.3);
    border-radius: 8px;
    padding: 6px 12px !important;
    font-weight: 700 !important;
    transition: all 0.2s ease;
}
.nav-platform-admin:hover {
    background: var(--orange, #f97316);
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
}

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

        // Build toast DOM safely to prevent XSS
        const toastIcon = document.createElement('div');
        toastIcon.className = 'toast-icon';
        toastIcon.textContent = icon;

        const toastTitle = document.createElement('div');
        toastTitle.className = 'toast-title';
        toastTitle.textContent = notif.title;

        const truncatedMsg = notif.message.length > 80
            ? notif.message.substring(0, 80) + '...'
            : notif.message;
        const toastMsg = document.createElement('div');
        toastMsg.className = 'toast-msg';
        toastMsg.textContent = truncatedMsg;

        const toastBody = document.createElement('div');
        toastBody.className = 'toast-body';
        toastBody.appendChild(toastTitle);
        toastBody.appendChild(toastMsg);

        const toastClose = document.createElement('button');
        toastClose.className = 'toast-close';
        toastClose.textContent = '\u00D7';

        toast.appendChild(toastIcon);
        toast.appendChild(toastBody);
        toast.appendChild(toastClose);

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
            if (profileDropdown) profileDropdown.classList.remove('active');
            const navLinks = document.querySelector('.nav-links');
            if (navLinks) navLinks.classList.remove('mobile-active');
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

                // Build notification items safely to prevent XSS
                notifList.innerHTML = '';
                data.forEach(n => {
                    const item = document.createElement('div');
                    item.className = 'notif-item' + (n.is_read == 0 ? ' unread' : '');
                    item.addEventListener('click', function() {
                        handleNotifClick(parseInt(n.id, 10), n.related_link, this);
                    });

                    const iconCircle = document.createElement('div');
                    iconCircle.className = 'notif-icon-circle';
                    iconCircle.textContent = n.icon;

                    const content = document.createElement('div');
                    content.className = 'notif-content';

                    const titleP = document.createElement('p');
                    const strong = document.createElement('strong');
                    strong.textContent = n.title;
                    titleP.appendChild(strong);

                    const msgP = document.createElement('p');
                    msgP.textContent = n.message.substring(0, 60) + '...';

                    const timeSpan = document.createElement('span');
                    timeSpan.className = 'notif-time';
                    timeSpan.textContent = n.time_ago;

                    content.appendChild(titleP);
                    content.appendChild(msgP);
                    content.appendChild(timeSpan);

                    item.appendChild(iconCircle);
                    item.appendChild(content);
                    notifList.appendChild(item);
                });
            });
    }

    window.handleNotifClick = function(id, link, element) {
        fetch(base + 'mark_notification_read.php?id=' + id)
            .then(res => res.json())
            .then(() => {
                element.classList.remove('unread');
                checkNotifications();
                if (link && link !== 'null') {
                    // Don't prepend base for absolute external URLs
                    if (link.match(/^https?:\/\//i)) {
                        window.open(link, '_blank');
                    } else {
                        window.location.href = base + link;
                    }
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
            if (notifDropdown) notifDropdown.classList.remove('active');
            const navLinks = document.querySelector('.nav-links');
            if (navLinks) navLinks.classList.remove('mobile-active');
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
        // Initialize active state based on current theme
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            themeDarkBtn.classList.add('active');
            themeLightBtn.classList.remove('active');
        } else {
            themeLightBtn.classList.add('active');
            themeDarkBtn.classList.remove('active');
        }

        themeLightBtn.addEventListener('click', (e) => {
            e.preventDefault();
            document.documentElement.setAttribute('data-theme', 'light');
            localStorage.setItem('theme', 'light');
            themeLightBtn.classList.add('active');
            themeDarkBtn.classList.remove('active');
        });
        
        themeDarkBtn.addEventListener('click', (e) => {
            e.preventDefault();
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
            themeDarkBtn.classList.add('active');
            themeLightBtn.classList.remove('active');
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
            if (profileDropdown) profileDropdown.classList.remove('active');
            if (notifDropdown) notifDropdown.classList.remove('active');
            const navLinks = document.querySelector('.nav-links');
            if (navLinks) navLinks.classList.toggle('mobile-active');
        });
    }
});
</script>

<main class="main-content">

