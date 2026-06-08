<?php
require_once "../includes/lang_helper.php";
require "../config/DataBase.php";
require_once "../includes/platform_admin.php";
require_once "../includes/csrf.php";
require_platform_admin($pdo);

// Handle approve/reject actions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!verify_csrf_token($_POST["csrf_token"] ?? null)) {
        header("Location: admin_reviews.php?error=Requete%20invalide");
        exit();
    }

    $reviewId = isset($_POST['review_id']) ? (int) $_POST['review_id'] : 0;
    $action = $_POST['action'] ?? '';

    if ($reviewId > 0 && $action === 'approve') {
        $stmt = $pdo->prepare("UPDATE reviews SET status = 'approved' WHERE id = ?");
        $stmt->execute([$reviewId]);
        header("Location: admin_reviews.php?success=Avis%20approuve");
        exit();
    }

    if ($reviewId > 0 && $action === 'reject') {
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$reviewId]);
        header("Location: admin_reviews.php?success=Avis%20supprime");
        exit();
    }

    header("Location: admin_reviews.php?error=Action%20invalide");
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

<h1 class="page-title">🛡️ Gestion des avis</h1>

<?php if (isset($_GET['success'])): ?>
    <div class="msg msg-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="msg msg-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<!-- Pending Reviews -->
<h2 style="font-size:1.1rem; color:var(--navy); margin-bottom:12px;">
    ⏳ En attente (<?php echo count($pendingReviews); ?>)
</h2>

<?php if (count($pendingReviews) == 0): ?>
    <div class="msg" style="background:var(--orange-light); color:var(--orange-dark);">
        Aucun avis en attente de validation.
    </div>
<?php else: ?>
    <?php foreach($pendingReviews as $rev): ?>
        <div class="review-item" style="border-left:4px solid var(--orange);">
            <div class="review-header">
                <div>
                    <span class="review-author">👤 <?php echo htmlspecialchars($rev["author_name"] ?? "Utilisateur supprime"); ?></span>
                    <span style="color:var(--text-muted); font-size:0.8rem;"> → <?php echo htmlspecialchars($rev["school_name"] ?? "Etablissement supprime"); ?></span>
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
                    <button type="submit" class="btn btn-primary" style="background:#27ae60;">Approuver</button>
                </form>
                <form method="POST" action="admin_reviews.php" style="display:inline;" onsubmit="return confirm('Supprimer cet avis ?');">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="review_id" value="<?php echo $rev['id']; ?>">
                    <input type="hidden" name="action" value="reject">
                    <button type="submit" class="btn btn-danger">Rejeter</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Approved Reviews -->
<h2 style="font-size:1.1rem; color:var(--navy); margin:25px 0 12px;">
    ✅ Approuvés (<?php echo count($approvedReviews); ?>)
</h2>

<?php if (count($approvedReviews) == 0): ?>
    <p style="color:var(--text-muted); font-size:0.85rem;">Aucun avis approuvé.</p>
<?php else: ?>
    <?php foreach($approvedReviews as $rev): ?>
        <div class="review-item">
            <div class="review-header">
                <div>
                    <span class="review-author">👤 <?php echo htmlspecialchars($rev["author_name"] ?? "Utilisateur supprime"); ?></span>
                    <span style="color:var(--text-muted); font-size:0.8rem;"> → <?php echo htmlspecialchars($rev["school_name"] ?? "Etablissement supprime"); ?></span>
                </div>
                <span class="review-date"><?php echo date("d/m/Y", strtotime($rev["created_at"])); ?></span>
            </div>
            <div class="review-content"><?php echo htmlspecialchars($rev["content"]); ?></div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require "../includes/footer.php"; ?>
