<?php
require_once "../includes/lang_helper.php";
$pageTitle = __("all_contests");
require "../includes/header.php";
require "../config/DataBase.php";

$isLoggedIn = isset($_SESSION['user_id']);

// Get all contests
$stmt = $pdo->query("SELECT c.*, i.name, i.name_ar, i.name_en, i.city, i.city_ar, i.city_en, i.image FROM contests c JOIN institutions i ON c.institution_id = i.id ORDER BY c.registration_deadline ASC");
$contests = $stmt->fetchAll();
?>

<div class="contests-page">
    <div class="page-header">
        <h1>🎓 <?php echo __('contests_calendar'); ?></h1>
        <p><?php echo __('contests_subtitle'); ?></p>
    </div>

    <div class="contests-filters">
        <!-- Optional: Add filters here -->
    </div>

    <div class="contests-grid">
        <?php foreach($contests as $c): 
            $statusLabel = __('status_' . $c['status']);
        ?>
            <div class="contest-card-lg">
                <div class="contest-img">
                    <img src="../assets/images/institutions/<?php echo $c['image'] ?? 'default_school.jpg'; ?>" alt="">
                </div>
                <div class="contest-body">
                    <span class="contest-status status-<?php echo $c['status']; ?>"><?php echo $statusLabel; ?></span>
                    <h3><?php echo htmlspecialchars(getLocalizedDbField($c, 'title')); ?></h3>
                    <p class="inst-name">🏫 <?php echo htmlspecialchars(getLocalizedDbField($c, 'name')); ?> — <?php echo htmlspecialchars(getLocalizedDbField($c, 'city')); ?></p>
                    
                    <div class="contest-metrics">
                        <div class="metric">
                            <span class="m-label"><?php echo __('registration_end'); ?></span>
                            <span class="m-value"><?php echo formatLocalizedDate($c['registration_deadline']); ?></span>
                        </div>
                        <div class="metric">
                            <span class="m-label"><?php echo __('contest_date'); ?></span>
                            <span class="m-value"><?php echo formatLocalizedDate($c['exam_date']); ?></span>
                        </div>
                    </div>

                    <div class="contest-desc">
                        <?php echo htmlspecialchars(getLocalizedDbField($c, 'description')); ?>
                    </div>

                    <div class="contest-actions">
                        <a href="institution_detail.php?id=<?php echo $c['institution_id']; ?>" class="btn btn-primary"><?php echo __('school_details'); ?></a>
                        <button class="btn btn-outline"><?php echo __('add_to_calendar'); ?></button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.contests-page { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
.contests-grid { display: grid; gap: 30px; margin-top: 40px; }

.contest-card-lg { 
    background: #fff; 
    border-radius: 24px; 
    overflow: hidden; 
    display: grid; 
    grid-template-columns: 300px 1fr; 
    box-shadow: var(--shadow-md); 
    border: 1px solid var(--border-color);
    transition: var(--transition);
}
.contest-card-lg:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }

.contest-img img { width: 100%; height: 100%; object-fit: cover; }
.contest-body { padding: 30px; }
.contest-body h3 { font-size: 1.5rem; color: var(--primary-dark); margin-bottom: 5px; }
.inst-name { color: var(--text-muted); font-weight: 600; margin-bottom: 20px; }

.contest-metrics { display: flex; gap: 40px; margin-bottom: 20px; background: #f8fafc; padding: 15px 25px; border-radius: 16px; width: fit-content; }
.metric { display: flex; flex-direction: column; }
.m-label { font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 0.5px; }
.m-value { font-size: 1rem; font-weight: 800; color: var(--primary); }

.contest-desc { font-size: 0.95rem; color: var(--text-dark); margin-bottom: 25px; line-height: 1.6; }
.contest-actions { display: flex; gap: 15px; }

.btn-outline { background: transparent; border: 1.5px solid var(--border-color); color: var(--text-dark); }
.btn-outline:hover { background: #f1f5f9; border-color: var(--text-muted); }

@media (max-width: 992px) {
    .contest-card-lg { grid-template-columns: 1fr; }
    .contest-img { height: 200px; }
}
</style>

<?php require "../includes/footer.php"; ?>
