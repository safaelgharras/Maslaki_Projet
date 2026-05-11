<?php
require_once "../includes/lang_helper.php";
$pageTitle = __("profile");
require "../includes/header.php";
require "../config/DataBase.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["user_id"];

// Get saved schools count
$savedCount = $pdo->prepare("SELECT COUNT(*) FROM saved_schools WHERE student_id = ?");
$savedCount->execute([$userId]);
$savedNum = $savedCount->fetchColumn();

// Get total institutions
$totalSchools = $pdo->query("SELECT COUNT(*) FROM institutions")->fetchColumn();

// Get upcoming deadlines for saved schools
$upcomingDeadlines = [];
try {
    $deadlineSql = "SELECT i.name, i.city, d.deadline_date
                    FROM saved_schools s
                    JOIN institutions i ON s.institution_id = i.id
                    JOIN deadlines d ON i.id = d.institution_id
                    WHERE s.student_id = ?
                    AND d.deadline_date >= CURDATE()
                    ORDER BY d.deadline_date ASC
                    LIMIT 5";
    $deadlineStmt = $pdo->prepare($deadlineSql);
    $deadlineStmt->execute([$userId]);
    $upcomingDeadlines = $deadlineStmt->fetchAll();
} catch (Exception $e) {}
?>

<div class="dashboard-container">
    <header class="dashboard-banner">
        <div class="banner-content">
            <span class="welcome-tag"><?php echo __('dash_welcome'); ?></span>
            <h1><?php echo __('dash_greeting'); ?>, <?php echo htmlspecialchars($_SESSION["user_name"]); ?></h1>
            <p><?php echo __('dash_subtitle'); ?></p>
        </div>
        <div class="banner-stats">
            <div class="b-stat">
                <strong><?php echo $savedNum; ?></strong>
                <span><?php echo __('dash_schools_followed'); ?></span>
            </div>
            <div class="b-stat">
                <strong>92%</strong>
                <span><?php echo __('dash_profile_completed'); ?></span>
            </div>
        </div>
    </header>

    <div class="dashboard-grid">
        <section class="dash-main">
            <div class="section-header">
                <h2><?php echo __('dash_quick_links'); ?></h2>
            </div>
            <div class="quick-links">
                <a href="institutions.php" class="quick-card">
                    <div class="q-icon">🏫</div>
                    <div class="q-info">
                        <h3><?php echo __('institutions'); ?></h3>
                        <p><?php echo sprintf(__('explore_n_schools'), $totalSchools); ?></p>
                    </div>
                    <span class="q-arrow">→</span>
                </a>

                <a href="saved_schools.php" class="quick-card">
                    <div class="q-icon">⭐</div>
                    <div class="q-info">
                        <h3><?php echo __('dash_my_list'); ?></h3>
                        <p><?php echo $savedNum; ?> <?php echo __('dash_list_subtitle'); ?></p>
                    </div>
                    <span class="q-arrow">→</span>
                </a>

                <a href="ai_form.php" class="quick-card q-primary">
                    <div class="q-icon">🤖</div>
                    <div class="q-info">
                        <h3><?php echo __('ai_orientation'); ?></h3>
                        <p><?php echo __('dash_orientation_subtitle'); ?></p>
                    </div>
                    <span class="q-arrow">→</span>
                </a>

                <a href="contests.php" class="quick-card">
                    <div class="q-icon">🎓</div>
                    <div class="q-info">
                        <h3><?php echo __('dash_contests_subtitle'); ?></h3>
                        <p><?php echo __('dash_contests_subtitle'); ?></p>
                    </div>
                    <span class="q-arrow">→</span>
                </a>
            </div>
            
            <div class="contests-section" style="margin-bottom: 40px;">
                <div class="section-header">
                    <h2><?php echo __('dash_important_contests'); ?></h2>
                </div>
                <div class="contest-grid">
                    <?php
                    try {
                        $contestStmt = $pdo->query("SELECT c.*, i.name as institution_name FROM contests c JOIN institutions i ON c.institution_id = i.id WHERE c.is_featured = 1 LIMIT 3");
                        $featuredContests = $contestStmt->fetchAll();
                        
                        foreach($featuredContests as $c): 
                            $statusKey = 'status_' . $c['status'];
                            $statusLabel = __($statusKey);
                        ?>
                            <div class="contest-card">
                                <span class="contest-status status-<?php echo $c['status']; ?>"><?php echo $statusLabel; ?></span>
                                <h4><?php echo htmlspecialchars($c['title']); ?></h4>
                                <div class="contest-details">
                                    <span class="contest-info">🏫 <?php echo htmlspecialchars($c['institution_name']); ?></span>
                                    <span class="contest-info">📅 <?php echo __('exam_label'); ?>: <?php echo date('d M Y', strtotime($c['exam_date'])); ?></span>
                                    <span class="contest-info">⏳ <?php echo __('deadline_label'); ?>: <?php echo date('d M Y', strtotime($c['registration_deadline'])); ?></span>
                                </div>
                                <div class="contest-footer">
                                    <a href="institution_detail.php?id=<?php echo $c['institution_id']; ?>" class="btn-details"><?php echo __('details_arrow'); ?></a>
                                </div>
                            </div>
                        <?php endforeach;
                    } catch (Exception $e) {}
                    ?>
                </div>
            </div>

            <?php if (count($upcomingDeadlines) > 0): ?>
            <div class="deadline-section">
                <div class="section-header">
                    <h2><?php echo __('dash_crucial_deadlines'); ?></h2>
                </div>
                <div class="deadline-grid">
                    <?php foreach($upcomingDeadlines as $d): ?>
                        <div class="deadline-card">
                            <div class="d-date">
                                <span class="d-day"><?php echo date('d', strtotime($d['deadline_date'])); ?></span>
                                <span class="d-month"><?php echo date('M', strtotime($d['deadline_date'])); ?></span>
                            </div>
                            <div class="d-info">
                                <h4><?php echo htmlspecialchars($d['name']); ?></h4>
                                <p>📍 <?php echo htmlspecialchars($d['city']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </section>

        <aside class="dash-sidebar">
            <div class="sidebar-card promo-card">
                <h3><?php echo __('dash_need_more_help'); ?></h3>
                <p><?php echo __('dash_help_subtitle'); ?></p>
                <a href="appointments.php" class="btn btn-white btn-full"><?php echo __('dash_book_appointment'); ?></a>
            </div>

            <div class="sidebar-card">
                <h3><?php echo __('notifications'); ?></h3>
                <div class="notif-list">
                    <?php
                    try {
                        // Using correct column name from typical schema or current query
                        $dashNotifs = $pdo->prepare("SELECT * FROM notifications WHERE (target_user_id = ? OR is_global = 1) ORDER BY created_at DESC LIMIT 5");
                        $dashNotifs->execute([$userId]);
                        $notifs = $dashNotifs->fetchAll();
                        
                        if (count($notifs) > 0):
                            foreach($notifs as $n): ?>
                                <div class="notif-item">
                                    <span class="n-dot"></span>
                                    <p><?php echo htmlspecialchars($n['message']); ?></p>
                                </div>
                            <?php endforeach;
                        else: ?>
                            <p class="text-muted"><?php echo __('no_notifications'); ?></p>
                        <?php endif;
                    } catch (Exception $e) {
                        echo "<p>".__('loading')."</p>";
                    }
                    ?>
                </div>
            </div>
        </aside>
    </div>
</div>

<style>
.dashboard-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
.dashboard-banner { 
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: var(--radius-lg);
    padding: 60px 40px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    box-shadow: var(--shadow-lg);
}
.welcome-tag { display: inline-block; background: rgba(255,255,255,0.1); padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; margin-bottom: 15px; }
.dashboard-banner h1 { font-size: 2.5rem; margin-bottom: 10px; font-weight: 800; }
.dashboard-banner p { color: rgba(255,255,255,0.8); max-width: 500px; line-height: 1.6; }

.banner-stats { display: flex; gap: 30px; text-align: center; }
.b-stat strong { display: block; font-size: 2rem; color: var(--accent); }
.b-stat span { font-size: 0.8rem; color: rgba(255,255,255,0.6); }

.dashboard-grid { display: grid; grid-template-columns: 1fr 320px; gap: 40px; }
.section-header { margin-bottom: 25px; }
.section-header h2 { font-size: 1.4rem; color: var(--primary); font-weight: 800; }

.quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px; }
.quick-card { 
    background: var(--white);
    padding: 24px;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-md);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: var(--transition);
    text-decoration: none;
    color: var(--text-dark);
}
.quick-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); border-color: var(--accent); }
.q-icon { font-size: 2rem; }
.q-info h3 { font-size: 1.1rem; margin-bottom: 5px; color: var(--primary); }
.q-info p { font-size: 0.85rem; color: var(--text-muted); }
.q-arrow { margin-left: auto; color: var(--border-color); font-size: 1.2rem; }
.q-primary { border-left: 4px solid var(--accent); }

.deadline-grid { display: grid; gap: 15px; }
.deadline-card { 
    background: var(--white);
    padding: 15px 20px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
}
.d-date { background: #f8fafc; padding: 10px; border-radius: 12px; text-align: center; min-width: 60px; }
.d-day { display: block; font-size: 1.2rem; font-weight: 800; color: var(--primary); }
.d-month { font-size: 0.7rem; text-transform: uppercase; color: var(--text-muted); }
.d-info h4 { font-size: 1rem; color: var(--text-dark); }
.d-info p { font-size: 0.8rem; color: var(--text-muted); }

.sidebar-card { background: var(--white); padding: 25px; border-radius: var(--radius-md); box-shadow: var(--shadow-md); margin-bottom: 20px; }
.promo-card { background: var(--primary); color: #fff; }
.promo-card h3 { color: #fff; margin-bottom: 15px; }
.promo-card p { color: rgba(255,255,255,0.8); font-size: 0.9rem; margin-bottom: 20px; }
.btn-white { background: #fff; color: var(--primary); font-weight: 700; }
.btn-full { width: 100%; }

.notif-list { display: grid; gap: 15px; }
.notif-item { display: flex; align-items: flex-start; gap: 12px; font-size: 0.85rem; color: var(--text-dark); }
.n-dot { width: 8px; height: 8px; background: var(--accent); border-radius: 50%; margin-top: 5px; flex-shrink: 0; }

@media (max-width: 992px) {
    .dashboard-grid { grid-template-columns: 1fr; }
    .dashboard-banner { flex-direction: column; text-align: center; gap: 30px; }
    .dashboard-banner p { margin: 0 auto 20px; }
}
</style>

<?php require "../includes/footer.php"; ?>