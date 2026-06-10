<?php
require_once "../includes/lang_helper.php";
require "../config/DataBase.php";
require_once "../includes/platform_admin.php";
require_once "../includes/csrf.php";
require_platform_admin($pdo);

// Handle approve/reject actions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!verify_csrf_token($_POST["csrf_token"] ?? null)) {
        header("Location: admin_reviews.php?error=" . urlencode(__('admin_reviews_error_csrf')));
        exit();
    }

    $reviewId = isset($_POST['review_id']) ? (int) $_POST['review_id'] : 0;
    $action = $_POST['action'] ?? '';

    if ($reviewId > 0 && $action === 'approve') {
        $stmt = $pdo->prepare("UPDATE reviews SET status = 'approved' WHERE id = ?");
        $stmt->execute([$reviewId]);
        header("Location: admin_reviews.php?success=" . urlencode(__('admin_reviews_success_approved')));
        exit();
    }

    if ($reviewId > 0 && $action === 'reject') {
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$reviewId]);
        header("Location: admin_reviews.php?success=" . urlencode(__('admin_reviews_success_deleted')));
        exit();
    }

    header("Location: admin_reviews.php?error=" . urlencode(__('admin_reviews_error_action')));
    exit();
}

$pageTitle = __("platform_admin_reviews_page_title");
require "../includes/header.php";

// Get all pending reviews
$pendingSql = "SELECT reviews.*, students.name AS author_name, institutions.name AS school_name
               FROM reviews 
               LEFT JOIN students ON reviews.student_id = students.id
               LEFT JOIN institutions ON reviews.institution_id = institutions.id
               WHERE reviews.status = 'pending'
               ORDER BY reviews.created_at DESC";
$pendingStmt = $pdo->query($pendingSql);
$pendingReviews = $pendingStmt->fetchAll();

// Get all approved reviews
$approvedSql = "SELECT reviews.*, students.name AS author_name, institutions.name AS school_name
                FROM reviews 
                LEFT JOIN students ON reviews.student_id = students.id
                LEFT JOIN institutions ON reviews.institution_id = institutions.id
                WHERE reviews.status = 'approved'
                ORDER BY reviews.created_at DESC
                LIMIT 20";
$approvedStmt = $pdo->query($approvedSql);
$approvedReviews = $approvedStmt->fetchAll();
?>

<h1 class="page-title">🛡️ <?php echo __('admin_reviews_title'); ?></h1>

<?php if (isset($_GET['success'])): ?>
    <div class="msg msg-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="msg msg-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<!-- Pending Reviews -->
<h2 style="font-size:1.1rem; color:var(--navy); margin-bottom:12px;">
    ⏳ <?php echo __('admin_reviews_pending'); ?> (<?php echo count($pendingReviews); ?>)
</h2>

<?php if (count($pendingReviews) == 0): ?>
    <div class="msg" style="background:var(--orange-light); color:var(--orange-dark);">
        <?php echo __('admin_reviews_no_pending'); ?>
    </div>
<?php else: ?>
    <?php foreach($pendingReviews as $rev):
        $rating = isset($rev['rating']) ? (int)$rev['rating'] : 0;
    ?>
        <div class="review-item" style="border-left:4px solid var(--orange);">
            <div class="review-header">
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <span class="review-author">👤 <?php echo htmlspecialchars($rev["author_name"] ?? __('admin_reviews_deleted_user')); ?></span>
                    <span style="color:var(--text-muted); font-size:0.8rem;">→ <?php echo htmlspecialchars($rev["school_name"] ?? __('admin_reviews_deleted_institution')); ?></span>
                    <?php if ($rating > 0): ?>
                        <span style="color:#f59e0b; letter-spacing:1px;">
                            <?php for ($i = 1; $i <= 5; $i++) echo $i <= $rating ? '★' : '☆'; ?>
                        </span>
                    <?php endif; ?>
                </div>
                <span class="review-date"><?php echo date("d/m/Y H:i", strtotime($rev["created_at"])); ?></span>
            </div>
            <div class="review-content" style="margin-bottom:12px;">
                <?php echo htmlspecialchars($rev["content"]); ?>
            </div>
            <div class="card-actions">
                <form method="POST" action="admin_reviews.php" style="display:inline;">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="review_id" value="<?php echo $rev['id']; ?>">
                    <input type="hidden" name="action" value="approve">
                    <button type="submit" class="btn btn-primary" style="background:#27ae60;"><?php echo __('admin_reviews_approve'); ?></button>
                </form>
                <form method="POST" action="admin_reviews.php" style="display:inline;" onsubmit="return confirm('<?php echo __('admin_reviews_confirm_delete'); ?>');">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="review_id" value="<?php echo $rev['id']; ?>">
                    <input type="hidden" name="action" value="reject">
                    <button type="submit" class="btn btn-danger"><?php echo __('admin_reviews_reject'); ?></button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Approved Reviews -->
<h2 style="font-size:1.1rem; color:var(--navy); margin:25px 0 12px;">
    ✅ <?php echo __('admin_reviews_approved'); ?> (<?php echo count($approvedReviews); ?>)
</h2>

<?php if (count($approvedReviews) == 0): ?>
    <p style="color:var(--text-muted); font-size:0.85rem;"><?php echo __('admin_reviews_no_approved'); ?></p>
<?php else: ?>
    <?php foreach($approvedReviews as $rev):
        $rating = isset($rev['rating']) ? (int)$rev['rating'] : 0;
    ?>
        <div class="review-item">
            <div class="review-header">
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <span class="review-author">👤 <?php echo htmlspecialchars($rev["author_name"] ?? __('admin_reviews_deleted_user')); ?></span>
                    <span style="color:var(--text-muted); font-size:0.8rem;">→ <?php echo htmlspecialchars($rev["school_name"] ?? __('admin_reviews_deleted_institution')); ?></span>
                    <?php if ($rating > 0): ?>
                        <span style="color:#f59e0b; letter-spacing:1px;">
                            <?php for ($i = 1; $i <= 5; $i++) echo $i <= $rating ? '★' : '☆'; ?>
                        </span>
                    <?php endif; ?>
                </div>
                <span class="review-date"><?php echo date("d/m/Y", strtotime($rev["created_at"])); ?></span>
            </div>
            <div class="review-content"><?php echo htmlspecialchars($rev["content"]); ?></div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require "../includes/footer.php"; ?>
