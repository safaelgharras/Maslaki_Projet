<?php
$pageTitle = __("institution_details");
require "../includes/header.php";
require "../config/DataBase.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: institutions.php");
    exit();
}

$id = (int) $_GET["id"];
$isLoggedIn = isset($_SESSION['user_id']);

// Get institution details with city safety
$sql = "SELECT i.*";
$hasVilles = false;
try {
    $pdo->query("SELECT 1 FROM villes LIMIT 1");
    $hasVilles = true;
    $sql .= ", v.nom as ville_nom FROM institutions i LEFT JOIN villes v ON i.ville_id = v.id";
} catch (Exception $e) {
    $sql .= " FROM institutions i";
}
$sql .= " WHERE i.id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$inst = $stmt->fetch();

if (!$inst) {
    header("Location: institutions.php?error=" . urlencode(__('school_not_found')));
    exit();
}

// Get filieres with their domains
$filiereSql = "SELECT f.*, d.nom as domain_nom 
               FROM filieres f 
               JOIN institution_filieres ifil ON f.id = ifil.filiere_id 
               LEFT JOIN domains d ON f.domain_id = d.id
               WHERE ifil.institution_id = ?";
$filiereStmt = $pdo->prepare($filiereSql);
$filiereStmt->execute([$id]);
$filieres = $filiereStmt->fetchAll();

// Get bac requirements
$bacSql = "SELECT bt.*, ibt.min_grade 
           FROM bac_types bt 
           JOIN institution_bac_types ibt ON bt.id = ibt.bac_type_id 
           WHERE ibt.institution_id = ?";
$bacStmt = $pdo->prepare($bacSql);
$bacStmt->execute([$id]);
$bac_requirements = $bacStmt->fetchAll();

// Get gallery images
$imgSql = "SELECT * FROM institution_images WHERE institution_id = ? ORDER BY is_main DESC";
$imgStmt = $pdo->prepare($imgSql);
$imgStmt->execute([$id]);
$images = $imgStmt->fetchAll();

// Get approved reviews
$reviewSql = "SELECT reviews.*, students.name AS author_name 
              FROM reviews 
              JOIN students ON reviews.student_id = students.id
              WHERE reviews.institution_id = ? AND reviews.status = 'approved'
              ORDER BY reviews.created_at DESC";
$reviewStmt = $pdo->prepare($reviewSql);
$reviewStmt->execute([$id]);
$reviews = $reviewStmt->fetchAll();

function translateType($type) {
    return __('type_' . strtolower($type));
}

function resolveDetailImage($path, $name) {
    if (empty($path) || $path === 'default_school.jpg') {
        // Try specific naming convention
        $candidates = [$name . ".webp", $name . ".png", $name . ".jpg"];
        foreach ($candidates as $c) {
            if (file_exists("../assets/images/" . $c)) return "../assets/images/" . $c;
        }
        return "../assets/images/default_school.jpg";
    }
    if (file_exists("../assets/images/" . $path)) return "../assets/images/" . $path;
    return "../assets/images/default_school.jpg";
}

$mainImage = count($images) > 0 ? resolveDetailImage($images[0]['image_path'], $inst['name']) : resolveDetailImage($inst['image'], $inst['name']);
?>

<div class="detail-container">
    <div class="detail-hero">
        <img src="<?php echo $mainImage; ?>" alt="<?php echo htmlspecialchars($inst['name']); ?>" class="hero-bg">
        <div class="hero-overlay">
            <div class="hero-text">
                <span class="type-badge"><?php echo htmlspecialchars(translateType($inst['type'])); ?></span>
                <h1><?php echo htmlspecialchars($inst['name']); ?></h1>
                <p>📍 <?php echo htmlspecialchars($inst['ville_nom'] ?? $inst['city']); ?> — Maroc</p>
            </div>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-main">
            <section class="info-card">
                <h2><?php echo __('about_institution'); ?></h2>
                <p class="description"><?php echo nl2br(htmlspecialchars($inst['description'])); ?></p>
            </section>

            <?php if (count($images) > 1): ?>
            <section class="info-card">
                <h2><?php echo __('photo_gallery'); ?></h2>
                <div class="image-gallery">
                    <?php foreach($images as $img): ?>
                        <div class="gallery-item">
                            <img src="<?php echo resolveDetailImage($img['image_path'], ''); ?>" alt="Gallery Image">
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <section class="info-card">
                <h2><?php echo __('available_streams'); ?></h2>
                <div class="filieres-list">
                    <?php if (count($filieres) > 0): ?>
                        <?php foreach($filieres as $f): ?>
                            <div class="filiere-item">
                                <div class="filiere-header">
                                    <h3><?php echo htmlspecialchars($f['nom']); ?></h3>
                                    <?php if ($f['domain_nom']): ?>
                                        <span class="domain-tag"><?php echo htmlspecialchars($f['domain_nom']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <p><?php echo htmlspecialchars($f['description'] ?? __('no_description')); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty-msg"><?php echo __('no_streams_listed'); ?></p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="info-card">
                <h2><?php echo __('student_reviews'); ?></h2>
                <?php if ($isLoggedIn): ?>
                    <div class="review-form">
                        <form method="POST" action="../submit_review.php">
                            <input type="hidden" name="institution_id" value="<?php echo $id; ?>">
                            <textarea name="content" placeholder="<?php echo __('share_review'); ?>" required></textarea>
                            <button type="submit" class="btn btn-primary"><?php echo __('submit_review'); ?></button>
                        </form>
                    </div>
                <?php else: ?>
                    <p class="login-msg"><?php echo __('login_to_review'); ?></p>
                <?php endif; ?>

                <div class="reviews-list">
                    <?php foreach($reviews as $rev): ?>
                        <div class="review-item">
                            <div class="rev-head">
                                <strong><?php echo htmlspecialchars($rev['author_name']); ?></strong>
                                <span><?php echo date('d/m/Y', strtotime($rev['created_at'])); ?></span>
                            </div>
                            <p><?php echo htmlspecialchars($rev['content']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <aside class="detail-sidebar">
            <div class="sidebar-card">
                <h3><?php echo __('admission_info'); ?></h3>
                <ul class="info-list">
                    <li>
                        <strong><?php echo __('general_access_threshold'); ?></strong>
                        <span><?php echo $inst['seuil'] ?? $inst['min_average'] ?? '--'; ?> / 20</span>
                    </li>
                    <?php if (count($bac_requirements) > 0): ?>
                    <li>
                        <strong><?php echo __('thresholds_by_bac'); ?></strong>
                        <div class="bac-req-list">
                            <?php foreach($bac_requirements as $bac): ?>
                                <div class="bac-req-item">
                                    <span class="bac-code"><?php echo htmlspecialchars($bac['code']); ?></span>
                                    <span class="bac-min"><?php echo $bac['min_grade']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <?php endif; ?>
                    <li>
                        <strong><?php echo __('delivered_diploma'); ?></strong>
                        <span><?php echo htmlspecialchars($inst['diplome'] ?? __('not_specified')); ?></span>
                    </li>
                    <li>
                        <strong><?php echo __('study_duration'); ?></strong>
                        <span><?php echo htmlspecialchars($inst['duree_etudes'] ?? '--'); ?></span>
                    </li>
                    <li>
                        <strong><?php echo __('prerequisites'); ?></strong>
                        <div class="req-text"><?php echo nl2br(htmlspecialchars($inst['requirements'])); ?></div>
                    </li>
                </ul>
                
                <?php if ($inst['site_web']): ?>
                    <a href="<?php echo htmlspecialchars($inst['site_web']); ?>" target="_blank" class="btn btn-accent btn-full">
                        🌐 <?php echo __('official_website'); ?>
                    </a>
                <?php endif; ?>
                
                <?php if ($isLoggedIn): ?>
                    <a href="../save_school.php?id=<?php echo $id; ?>" class="btn btn-outline btn-full">
                        ❤ <?php echo __('save'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>

<style>
.detail-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
.detail-hero { height: 400px; border-radius: var(--radius-lg); overflow: hidden; position: relative; margin-bottom: 40px; box-shadow: var(--shadow-lg); }
.hero-bg { width: 100%; height: 100%; object-fit: cover; }
.hero-overlay { position: absolute; bottom: 0; left: 0; right: 0; padding: 60px 40px; background: linear-gradient(transparent, rgba(0,0,0,0.8)); color: #fff; }
.hero-text h1 { font-size: 2.5rem; margin: 10px 0; }
.type-badge { background: var(--accent); padding: 5px 15px; border-radius: 20px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; }

.detail-grid { display: grid; grid-template-columns: 1fr 350px; gap: 40px; }
.info-card { background: var(--white); padding: 30px; border-radius: var(--radius-md); box-shadow: var(--shadow-md); margin-bottom: 30px; }
.info-card h2 { color: var(--primary); margin-bottom: 20px; font-size: 1.4rem; border-bottom: 2px solid var(--border-color); padding-bottom: 10px; }

.image-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-top: 20px; }
.gallery-item img { width: 100%; height: 120px; object-fit: cover; border-radius: 12px; cursor: pointer; transition: 0.3s; border: 2px solid transparent; }
.gallery-item img:hover { transform: translateY(-5px); border-color: var(--accent); }

.filieres-list { display: grid; gap: 15px; }
.filiere-item { padding: 15px; border: 1px solid var(--border-color); border-radius: var(--radius-md); transition: var(--transition); }
.filiere-item:hover { border-color: var(--accent); background: rgba(var(--primary-rgb), 0.02); }
.filiere-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }
.filiere-item h3 { font-size: 1.1rem; color: var(--primary); margin: 0; }
.domain-tag { font-size: 0.75rem; background: rgba(var(--primary-rgb), 0.1); color: var(--primary); padding: 2px 8px; border-radius: 4px; }
.filiere-item p { font-size: 0.9rem; color: var(--text-muted); line-height: 1.5; }

.bac-req-list { display: grid; gap: 8px; margin-top: 10px; }
.bac-req-item { display: flex; justify-content: space-between; background: rgba(255,255,255,0.1); padding: 5px 12px; border-radius: 6px; font-size: 0.9rem; }
.bac-code { font-weight: 700; color: var(--accent); }

.detail-sidebar .sidebar-card { background: var(--primary); color: #fff; padding: 30px; border-radius: var(--radius-md); box-shadow: var(--shadow-lg); position: sticky; top: 100px; }
.sidebar-card h3 { margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 10px; }
.info-list { list-style: none; padding: 0; }
.info-list li { margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; }
.info-list li strong { display: block; font-size: 0.8rem; color: rgba(255,255,255,0.6); margin-bottom: 5px; }
.info-list li span { font-weight: 600; font-size: 1.1rem; }
.req-text { font-size: 0.9rem; color: rgba(255,255,255,0.9); }

.btn-full { width: 100%; margin-top: 15px; padding: 12px; }
.btn-accent { background: var(--accent); color: #fff; }
.btn-outline { border: 1.5px solid #fff; background: transparent; color: #fff; }
.btn-outline:hover { background: #fff; color: var(--primary); }

[data-theme="dark"] .info-card { background: #161e31; }
[data-theme="dark"] .filiere-item { border-color: #2a354f; }

@media (max-width: 992px) {
    .detail-grid { grid-template-columns: 1fr; }
    .detail-hero { height: 300px; }
    .hero-text h1 { font-size: 1.8rem; }
}
</style>

<?php require "../includes/footer.php"; ?>
