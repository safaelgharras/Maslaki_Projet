<?php
session_start();
require_once "../includes/lang_helper.php";
require "../config/DataBase.php";

$pageTitle = __('notifications');
$base = "../";
require "../includes/header.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$typeFilter = isset($_GET['type']) ? $_GET['type'] : 'all';

// Fetch notifications
$sql = "SELECT n.*, COALESCE(un.is_read, 0) as is_read
        FROM notifications n
        LEFT JOIN user_notifications un ON n.id = un.notification_id AND un.user_id = ?
        WHERE (n.is_global = 1 OR n.target_user_id = ?)
        AND (un.is_deleted IS NULL OR un.is_deleted = 0)";

if ($typeFilter !== 'all') {
    $sql .= " AND n.type = ?";
}

$sql .= " ORDER BY n.created_at DESC";

$stmt = $pdo->prepare($sql);
if ($typeFilter !== 'all') {
    $stmt->execute([$userId, $userId, $typeFilter]);
} else {
    $stmt->execute([$userId, $userId]);
}
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getNotifIcon($type) {
    switch($type) {
        case 'system': return '⚙️';
        case 'school': return '🏫';
        case 'filiere': return '🎓';
        case 'announcement': return '📢';
        case 'maintenance': return '🛠️';
        case 'orientation': return '🧭';
        case 'deadline': return '⏰';
        default: return '🔔';
    }
}
?>

<div class="container notif-page-container">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
        <h1 class="page-title" style="border:none; margin:0;"><?php echo __('notifications_center'); ?></h1>
        <button class="btn btn-outline-primary" onclick="markAllRead()"><?php echo __('mark_all_read'); ?></button>
    </div>

    <div class="notif-filter-bar">
        <a href="?type=all" class="filter-chip <?php echo $typeFilter === 'all' ? 'active' : ''; ?>"><?php echo __('all_notifs'); ?></a>
        <a href="?type=announcement" class="filter-chip <?php echo $typeFilter === 'announcement' ? 'active' : ''; ?>"><?php echo __('announcements'); ?></a>
        <a href="?type=school" class="filter-chip <?php echo $typeFilter === 'school' ? 'active' : ''; ?>"><?php echo __('schools'); ?></a>
        <a href="?type=deadline" class="filter-chip <?php echo $typeFilter === 'deadline' ? 'active' : ''; ?>"><?php echo __('deadlines'); ?></a>
        <a href="?type=system" class="filter-chip <?php echo $typeFilter === 'system' ? 'active' : ''; ?>"><?php echo __('system'); ?></a>
    </div>

    <div class="notif-full-list">
        <?php if (count($notifications) > 0): ?>
            <?php foreach($notifications as $n): ?>
                <div class="notif-full-item <?php echo $n['is_read'] == 0 ? 'unread' : ''; ?>" id="notif-<?php echo $n['id']; ?>">
                    <div class="notif-icon-circle" style="width:50px; height:50px; font-size:1.5rem;">
                        <?php echo getNotifIcon($n['type']); ?>
                    </div>
                    <div class="notif-full-content">
                        <div class="notif-full-header">
                            <h3><?php echo htmlspecialchars(__($n['title'])); ?></h3>
                            <span class="notif-time"><?php echo date('d M Y, H:i', strtotime($n['created_at'])); ?></span>
                        </div>
                        <div class="notif-full-body">
                            <?php echo nl2br(htmlspecialchars(__($n['message']))); ?>
                        </div>
                        <div class="notif-full-footer">
                            <div class="notif-actions">
                                <?php if ($n['related_link']): ?>
                                    <a href="<?php echo $base . $n['related_link']; ?>" class="btn btn-primary btn-small"><?php echo __('see_more'); ?></a>
                                <?php endif; ?>
                                <?php if ($n['is_read'] == 0): ?>
                                    <button class="btn btn-outline-secondary btn-small" onclick="markRead(<?php echo $n['id']; ?>)"><?php echo __('mark_as_read'); ?></button>
                                <?php endif; ?>
                                <button class="btn btn-danger btn-small" style="background:none; border:none; color:#ef4444;" onclick="deleteNotif(<?php echo $n['id']; ?>)"><?php echo __('delete'); ?></button>
                            </div>
                            <span class="badge" style="text-transform: capitalize; background:rgba(var(--primary-rgb), 0.1); color:var(--primary);"><?php echo __($n['type']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state" style="background:var(--white); border-radius:20px; padding:60px;">
                <div class="icon">📭</div>
                <h3><?php echo __('no_notifications'); ?></h3>
                <p><?php echo __('up_to_date_msg'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function markRead(id) {
    fetch('../mark_notification_read.php?id=' + id)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('notif-' + id).classList.remove('unread');
                // Reload or update badge if needed
            }
        });
}

function markAllRead() {
    fetch('../mark_notification_read.php?all=1')
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            }
        });
}

function deleteNotif(id) {
    if (confirm('<?php echo addslashes(__('confirm_delete_notif')); ?>')) {
        fetch('../delete_notification.php?id=' + id)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const el = document.getElementById('notif-' + id);
                    el.style.opacity = '0';
                    el.style.transform = 'translateX(20px)';
                    setTimeout(() => el.remove(), 400);
                }
            });
    }
}
</script>

<?php require "../includes/footer.php"; ?>
