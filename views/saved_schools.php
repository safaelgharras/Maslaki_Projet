<?php
/**
 * saved_schools.php — Display the logged-in student's saved/favorite schools.
 *
 * Queries the saved_schools + institutions tables, then renders a card grid.
 * Each card shows the school image, name, type, city, seuil, a link to the
 * detail page, and a CSRF-protected delete button.
 */

require_once "../includes/lang_helper.php";
$pageTitle = __('saved_schools_page_title');
require "../includes/header.php";
require "../config/DataBase.php";
require_once "../includes/csrf.php";

// Redirect guests to login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION["user_id"];

// Fetch all institutions the student has saved via the saved_schools pivot table
$sql = "SELECT institutions.*
        FROM saved_schools
        JOIN institutions 
        ON saved_schools.institution_id = institutions.id
        WHERE saved_schools.student_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$student_id]);
$schools = $stmt->fetchAll();
?>

<h1 class="page-title"><?php echo __('saved_schools_page_title'); ?></h1>

<!-- Flash messages (success / error from redirects) -->
<?php if (isset($_GET['success'])): ?>
    <div class="msg msg-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="msg msg-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<!-- Empty state: no saved schools yet -->
<?php if (count($schools) == 0): ?>
    <div class="empty-state">
        <div class="icon">📭</div>
        <p><?php echo __('saved_schools_empty'); ?></p>
        <a href="institutions.php" class="btn btn-orange btn-lg" style="margin-top:15px;"><?php echo __('explore_universities'); ?></a>
    </div>
<?php else: ?>
    <!-- Render school cards grid -->
    <div class="cards-grid">
        <?php foreach($schools as $s): 
            // Localize school fields based on current language
            $schoolName = getLocalizedDbField($s, 'name');
            $schoolCity = getLocalizedDbField($s, 'city');
            $schoolTypeKey = 'type_' . strtolower($s['type']);
            $schoolType = __($schoolTypeKey) !== $schoolTypeKey ? __($schoolTypeKey) : $s['type'];
        ?>
            <div class="card">
                <img src="../assets/images/institutions/<?php echo $s['image'] ?? 'default_school.jpg'; ?>" class="card-img" alt="<?php echo htmlspecialchars($schoolName); ?>">
                <div class="card-body">
                    <div class="card-tag"><?php echo htmlspecialchars($schoolType); ?></div>
                    <h3><?php echo htmlspecialchars($schoolName); ?></h3>
                    <p class="school-location">📍 <?php echo htmlspecialchars($schoolCity ?: __('morocco')); ?></p>
                    
                    <div class="card-footer">
                        <span class="seuil"><?php echo __('seuil'); ?>: <strong><?php echo $s['min_average'] ?? '--'; ?></strong></span>
                        <div class="card-actions">
                            <a href="institution_detail.php?id=<?php echo $s['id']; ?>" class="btn-link"><?php echo __('details_arrow'); ?></a>
                            <!-- CSRF-protected delete form with confirmation dialog -->
                            <form method="POST" action="../remove_school.php" style="display:inline;" onsubmit="return confirm('<?php echo addslashes(__('confirm_delete_school')); ?>');">
                                <?php echo csrf_input(); ?>
                                <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                <button type="submit" class="btn btn-danger"><?php echo __('delete'); ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>

<?php require "../includes/footer.php"; ?>
