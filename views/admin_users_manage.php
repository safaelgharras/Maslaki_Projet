<?php
/**
 * admin_users_manage.php — Superadmin-only user role management.
 *
 * This page allows the superadmin to:
 * - View all registered users (students, admins, superadmins)
 * - Search and filter users by name/email and role
 * - Promote students to admin role
 * - Demote admins back to student role
 *
 * Security:
 * - Only superadmin can access this page (require_superadmin guard)
 * - Cannot modify other superadmins
 * - Cannot modify own role
 * - CSRF protection on all POST actions
 *
 * Data flow:
 * 1. Verify superadmin access
 * 2. Handle POST actions (promote/demote) with CSRF validation
 * 3. Build filtered/sorted user query (superadmin → admin → student)
 * 4. Render user list with action buttons
 */
require_once "../includes/lang_helper.php";
require "../config/DataBase.php";
require_once "../includes/platform_admin.php";
require_once "../includes/csrf.php";

// Only the superadmin can manage user roles
require_superadmin($pdo);

$currentUserId = (int)$_SESSION['user_id'];

// Handle promoting/demoting actions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Re-check superadmin on every POST (double-lock)
    if (!is_superadmin($pdo)) {
        header("Location: admin_users_manage.php?error=" . urlencode(__('admin_users_error_unauthorized')));
        exit();
    }

    if (!verify_csrf_token($_POST["csrf_token"] ?? null)) {
        header("Location: admin_users_manage.php?error=" . urlencode(__('admin_users_error_csrf')));
        exit();
    }

    $targetUserId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
    $action = $_POST['action'] ?? '';

    // Validate that the target user actually exists
    $targetRole = platform_admin_role($pdo, $targetUserId);
    if ($targetRole === null) {
        header("Location: admin_users_manage.php?error=" . urlencode(__('admin_users_error_action')));
        exit();
    }

    if ($targetUserId === $currentUserId) {
        header("Location: admin_users_manage.php?error=" . urlencode(__('admin_users_error_self')));
        exit();
    }

    // Never allow modifying another superadmin
    if ($targetRole === 'superadmin') {
        header("Location: admin_users_manage.php?error=" . urlencode(__('admin_users_error_superadmin')));
        exit();
    }

    if ($action === 'promote') {
        $stmt = $pdo->prepare("UPDATE students SET role = 'admin' WHERE id = ?");
        $stmt->execute([$targetUserId]);
        header("Location: admin_users_manage.php?success=" . urlencode(__('admin_users_success_promote')));
        exit();
    } elseif ($action === 'demote') {
        $stmt = $pdo->prepare("UPDATE students SET role = 'student' WHERE id = ?");
        $stmt->execute([$targetUserId]);
        header("Location: admin_users_manage.php?success=" . urlencode(__('admin_users_success_demote')));
        exit();
    }

    header("Location: admin_users_manage.php?error=" . urlencode(__('admin_users_error_action')));
    exit();
}

$pageTitle = __('platform_admin_users_page_title');
require "../includes/header.php";

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$roleFilter = isset($_GET['role']) ? trim($_GET['role']) : '';

// Build query
$sql = "SELECT id, name, email, role, created_at FROM students WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($roleFilter !== '') {
    $sql .= " AND role = ?";
    $params[] = $roleFilter;
}

// Superadmin first, then admins, then students
$sql .= " ORDER BY FIELD(role, 'superadmin', 'admin', 'student'), name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="user-manage-container" style="max-width: 1000px; margin: 40px auto; padding: 0 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; gap: 20px; flex-wrap: wrap;">
        <div>
            <h1 class="page-title" style="margin-bottom: 5px; border-bottom: none; padding-bottom: 0;">👥 <?php echo __('admin_users_title'); ?></h1>
            <div style="width: 60px; height: 3px; background: var(--orange); margin-top: 8px; margin-bottom: 8px;"></div>
            <p style="color: var(--text-muted); margin: 0;"><?php echo __('admin_users_subtitle'); ?></p>
        </div>
        <a href="admin_dashboard.php" class="btn btn-outline" style="font-size: 0.9rem; padding: 10px 20px; border-radius: 12px; height: fit-content; text-decoration: none;">
                        <?php echo __('admin_users_back_dashboard'); ?>
        </a>
    </div>

    <!-- Alert notifications -->
    <?php if (isset($_GET['success'])): ?>
        <div class="msg msg-success" style="margin-bottom: 25px; border-radius: 12px; padding: 15px 20px; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">
            ✅ <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="msg msg-error" style="margin-bottom: 25px; border-radius: 12px; padding: 15px 20px; background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5;">
            ⚠️ <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Search / Filter form -->
    <form method="GET" action="admin_users_manage.php" style="background: var(--white); padding: 25px; border-radius: 20px; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); display: flex; gap: 15px; margin-bottom: 35px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 2; min-width: 250px;">
            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;"><?php echo __('admin_users_search_label'); ?></label>
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="<?php echo __('admin_users_search_ph'); ?>" class="search-input" style="width: 100%; box-sizing: border-box; border-radius: 12px;">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;"><?php echo __('admin_users_filter_role'); ?></label>
            <select name="role" class="filter-select" style="width: 100%; border-radius: 12px;">
                <option value=""><?php echo __('admin_users_all_roles'); ?></option>
                <option value="student" <?php echo $roleFilter === 'student' ? 'selected' : ''; ?>><?php echo __('admin_users_role_student'); ?></option>
                <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>><?php echo __('admin_users_role_admin'); ?></option>
                <option value="superadmin" <?php echo $roleFilter === 'superadmin' ? 'selected' : ''; ?>><?php echo __('admin_users_role_superadmin'); ?></option>
            </select>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary" style="padding: 14px 24px; border-radius: 12px;"><?php echo __('admin_users_filter_btn'); ?></button>
            <?php if ($search !== '' || $roleFilter !== ''): ?>
                <a href="admin_users_manage.php" class="btn btn-outline" style="padding: 14px 20px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;"><?php echo __('admin_users_reset'); ?></a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Users list -->
    <div style="display: flex; flex-direction: column; gap: 15px;">
        <h2 style="font-size: 1.1rem; color: var(--navy); margin-bottom: 5px;">👥 <?php echo __('admin_users_found'); ?> (<?php echo count($users); ?>)</h2>
        
        <?php if (count($users) === 0): ?>
            <div class="msg" style="background: var(--bg-light); color: var(--text-muted); text-align: center; padding: 40px; border-radius: 20px; border: 1px dashed var(--border-color);">
                <?php echo __('admin_users_no_results'); ?>
            </div>
        <?php else: ?>
            <?php foreach ($users as $user): ?>
                <?php $isSelf = ($user['id'] == $currentUserId); ?>
                <div class="user-item-card" style="background: var(--white); padding: 20px; border-radius: 16px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap; box-shadow: var(--shadow-sm); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--bg-light); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: var(--primary);">
                            👤
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <h3 style="margin: 0; font-size: 1.05rem; color: var(--text-dark);"><?php echo htmlspecialchars($user['name'] ?? __('admin_users_unknown')); ?></h3>
                            <?php if ($user['role'] === 'superadmin'): ?>
                                <span style="background: linear-gradient(135deg,#fef3c7,#fde68a); color: #92400e; font-size: 0.75rem; font-weight: 800; padding: 4px 12px; border-radius: 20px; border: 1px solid #fbbf24;">👑 Superadmin</span>
                            <?php elseif ($user['role'] === 'admin'): ?>
                                <span style="background: #e0f2fe; color: #0369a1; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; border: 1px solid #bae6fd;">🛡️ Admin</span>
                            <?php else: ?>
                                <span style="background: #f1f5f9; color: #475569; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; border: 1px solid #cbd5e1;">👤 <?php echo __('admin_users_role_student'); ?></span>
                            <?php endif; ?>
                            <?php if ($isSelf): ?>
                                <span style="background: #fef3c7; color: #b45309; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; border: 1px solid #fde68a;"><?php echo __('admin_users_you'); ?></span>
                            <?php endif; ?>
                            <p style="margin: 4px 0 0 0; font-size: 0.85rem; color: var(--text-muted); width: 100%;"><?php echo htmlspecialchars($user['email']); ?></p>
                            <p style="margin: 2px 0 0 0; font-size: 0.75rem; color: var(--text-muted); font-style: italic;"><?php echo __('admin_users_registered_on'); ?> <?php echo date("d/m/Y", strtotime($user['created_at'])); ?></p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <?php if ($isSelf): ?>
                            <span style="font-size: 0.85rem; color: var(--text-muted); font-style: italic;"><?php echo __('admin_users_connected_account'); ?></span>
                        <?php elseif ($user['role'] === 'superadmin'): ?>
                            <span style="font-size: 0.82rem; color: #92400e; background: #fef3c7; border: 1px solid #fde68a; padding: 6px 14px; border-radius: 10px; font-weight: 600;">👑 <?php echo __('admin_users_protected_account'); ?></span>
                        <?php elseif ($user['role'] === 'student'): ?>
                            <form method="POST" action="admin_users_manage.php" onsubmit="return confirm('<?php echo addslashes(__('admin_users_confirm_promote')); ?>');">
                                <?php echo csrf_input(); ?>
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="action" value="promote">
                                <button type="submit" class="btn btn-primary" style="background: #10b981; border-color: #10b981; border-radius: 10px; font-size: 0.85rem; padding: 8px 16px;">
                                    <?php echo __('admin_users_promote_btn'); ?>
                                </button>
                            </form>
                        <?php elseif ($user['role'] === 'admin'): ?>
                            <form method="POST" action="admin_users_manage.php" onsubmit="return confirm('<?php echo addslashes(__('admin_users_confirm_demote')); ?>');">
                                <?php echo csrf_input(); ?>
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="action" value="demote">
                                <button type="submit" class="btn btn-danger" style="background: #ef4444; border-color: #ef4444; border-radius: 10px; font-size: 0.85rem; padding: 8px 16px;">
                                    <?php echo __('admin_users_demote_btn'); ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.user-item-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
[data-theme="dark"] .user-item-card {
    background: #0b1121;
    border-color: #1e293b;
}
[data-theme="dark"] .user-manage-container form {
    background: #0b1121 !important;
    border-color: #1e293b !important;
}
</style>

<?php require "../includes/footer.php"; ?>
