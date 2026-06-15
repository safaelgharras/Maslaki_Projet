<?php
/**
 * institution_detail.php — Full detail page for a single institution.
 *
 * Displays comprehensive information about one institution including:
 * - Hero banner with main image, name, type badge, city, domain tags
 * - Description section
 * - Connected sub-schools/faculties (if this is a parent university)
 * - Photo gallery (from institution_images table)
 * - Available filières (streams) with domain tags
 * - Student reviews with star ratings and moderation
 * - Sidebar with admission info: threshold, bac requirements, diploma, duration, prerequisites
 * - Links to official website and save/favorite functionality
 *
 * Data flow:
 * 1. Validate institution ID from GET parameter
 * 2. Fetch institution with ville join for city name
 * 3. Fetch parent university (if parent_id is set)
 * 4. Fetch sub-schools (if this institution is a parent)
 * 5. Fetch domain tags from institution_domain pivot
 * 6. Fetch filières with domains from institution_filieres pivot
 * 7. Fetch bac requirements from institution_bac_types pivot
 * 8. Fetch gallery images
 * 9. Fetch approved reviews with author info
 * 10. Render all sections with localized fields
 */
require_once "../includes/lang_helper.php";
$pageTitle = __("institution_details");
require "../includes/header.php";
require "../config/DataBase.php";
require_once "../includes/csrf.php";

// Validate institution ID from URL parameter
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
    $sql .= ", v.nom as ville_nom, v.nom_ar as ville_nom_ar FROM institutions i LEFT JOIN villes v ON i.ville_id = v.id";
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

// Localize main object
$inst['name'] = getLocalizedDbField($inst, 'name');
$inst['city'] = getLocalizedDbField($inst, 'city');
$inst['description'] = getLocalizedDbField($inst, 'description');
$inst['requirements'] = getLocalizedDbField($inst, 'requirements');
$inst['diplome'] = getLocalizedDbField($inst, 'diplome');
$inst['duree_etudes'] = getLocalizedDbField($inst, 'duree_etudes');
$inst['ville_nom'] = isset($inst['ville_nom_ar']) ? getLocalizedDbField($inst, 'ville_nom') : ($inst['ville_nom'] ?? '');

// Check for parent university if this is a sub-school
$parentUniversity = null;
if (!empty($inst['parent_id'])) {
    $parentStmt = $pdo->prepare("SELECT id, name, name_ar, name_en, type FROM institutions WHERE id = ?");
    $parentStmt->execute([$inst['parent_id']]);
    $parentUniversity = $parentStmt->fetch();
    if ($parentUniversity) {
        $parentUniversity['name'] = getLocalizedDbField($parentUniversity, 'name');
    }
}

// Fetch sub-schools / child faculties if this is a parent university container
$subSchools = [];
$subStmt = $pdo->prepare("SELECT i.*, v.nom as ville_nom, v.nom_ar as ville_nom_ar 
                         FROM institutions i 
                         LEFT JOIN villes v ON i.ville_id = v.id 
                         WHERE i.parent_id = ? 
                         ORDER BY i.name ASC");
$subStmt->execute([$id]);
$subSchools = $subStmt->fetchAll();
foreach ($subSchools as &$sub) {
    $sub['name'] = getLocalizedDbField($sub, 'name');
    $sub['ville_nom'] = isset($sub['ville_nom_ar']) ? getLocalizedDbField($sub, 'ville_nom') : ($sub['ville_nom'] ?? '');
}
unset($sub);

// Fetch direct-tagged domains from pivot table
$domainTags = [];
try {
    $domainTagsSql = "SELECT d.id, d.nom, d.nom_ar, d.nom_en 
                      FROM domains d
                      JOIN institution_domain idom ON d.id = idom.domain_id
                      WHERE idom.institution_id = ?";
    $domainTagsStmt = $pdo->prepare($domainTagsSql);
    $domainTagsStmt->execute([$id]);
    $domainTags = $domainTagsStmt->fetchAll();
    foreach ($domainTags as &$dt) {
        $dt['nom'] = getLocalizedDbField($dt, 'nom');
    }
    unset($dt);
} catch (Exception $e) {}

// Check for specialized school types to show custom UI elements
// CPGE: Classes Préparatoires aux Grandes Écoles — show excellence ribbon
// Alternative: coding schools like 1337, YouCode — show tech badge
$isCPGE = (strpos(strtolower($inst['name']), 'cpge') !== false || $inst['type'] === 'Preparatory');
$isAlternativeTech = (strpos(strtolower($inst['name']), '1337') !== false || strpos(strtolower($inst['name']), 'youcode') !== false || ($inst['sector_type'] ?? '') === 'alternative');

// Get filieres with their domains
$filiereSql = "SELECT f.*, d.nom as domain_nom, d.nom_ar as domain_nom_ar, d.nom_en as domain_nom_en
               FROM filieres f 
               JOIN institution_filieres ifil ON f.id = ifil.filiere_id 
               LEFT JOIN domains d ON f.domain_id = d.id
               WHERE ifil.institution_id = ?";
$filiereStmt = $pdo->prepare($filiereSql);
$filiereStmt->execute([$id]);
$filieres = $filiereStmt->fetchAll();

foreach ($filieres as &$f) {
    $f['nom'] = getLocalizedDbField($f, 'nom');
    $f['description'] = getLocalizedDbField($f, 'description');
    $f['domain_nom'] = getLocalizedDbField($f, 'domain_nom');
}
unset($f);

// Get bac requirements
$bacSql = "SELECT bt.*, bt.nom_ar, ibt.min_grade 
           FROM bac_types bt 
           JOIN institution_bac_types ibt ON bt.id = ibt.bac_type_id 
           WHERE ibt.institution_id = ?";
$bacStmt = $pdo->prepare($bacSql);
$bacStmt->execute([$id]);
$bac_requirements = $bacStmt->fetchAll();

foreach ($bac_requirements as &$br) {
    $br['nom'] = getLocalizedDbField($br, 'nom');
}

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

/**
 * Resolve the detail page image for an institution.
 * Uses a hardcoded map for known institutions, then checks DB image,
 * then tries standard name.ext patterns in multiple directories.
 * Falls back to default_school.jpg.
 */
function resolveDetailImage($path, $name) {
    $name = trim($name);
    $normalizedName = strtolower($name);
    
    // Map of known images in the institutions/ subfolder
    $institutionSubfolderImages = [
        'cpge fes' => 'CPGE Fez.jpg',
        'cpge fez' => 'CPGE Fez.jpg',
        'cpge kenitra' => 'CPGE Kenitra .jpg',
        'cpge marrakech' => 'CPGE Marrakech.WEBP',
        'cpge oujda' => 'CPGE Oujda.PNG',
        'eigsi casablanca' => 'EIGSI Casablanca.webp',
        'emi rabat' => 'EMI Rabat.webp',
        'emsi casablanca' => 'EMSI Casablanca.webp',
        'emsi rabat' => 'EMSI Rabat.PNG',
        'encg agadir' => 'ENCG Agadir.webp',
        'encg kenitra' => 'ENCG Kenitra.png',
        'encg marrakech' => 'ENCG Marrakech.webp',
        'encg oujda' => 'ENCG Oujda.webp',
        'encg settat' => 'ENCG Settat.webp',
        'encg tanger' => 'ENCG Tanger.png',
        'encg casablanca' => 'ENCG casablanca.png',
        'ens rabat' => 'ENS Rabat.png',
        'ensa casablanca' => 'ENSA Casablanca.png',
        'ensa fes' => 'ENSA Fes.png',
        'ensa kenitra' => 'ENSA Kenitra.png',
        'ensa marrakech' => 'ENSA Marrakech.png',
        'ensa oujda' => 'ENSA Oujda.png',
        'ensa tanger' => 'ENSA Tanger.png',
        'enset mohammedia' => 'ENSET Mohammedia.webp',
        'ensias rabat' => 'ENSIAS Rabat.png',
        'esca ecole de management' => 'ESCA Ecole de Management Casablanca.webp',
        'est agadir' => 'EST Agadir.png',
        'est casablanca' => 'EST Casablanca.png',
        'est fes' => 'EST Fes.png',
        'est kenitra' => 'EST Kenitra.webp',
        'est laayoune' => 'EST Laayoune.png',
        'est oujda' => 'EST Oujda.webp',
        'fs beni mellal' => 'FS Beni Mellal.png',
        'fs casablanca' => 'FS Casablanca.png',
        'fs errachidia' => 'FS Errachidia.png',
        'fs meknes' => 'FS Meknes.png',
        'fs oujda' => 'FS Oujda.png',
        'fs rabat' => 'FS Rabat.png',
        'fst al hoceima' => 'FST Al Hoceima.jpg',
        'fst casablanca' => 'FST Casablanca.png',
        'fst mohammedia' => 'FST Mohammedia.png',
        'fst settat' => 'FST Settat - Hassan 1er.png',
        'fst tanger' => 'FST Tanger.png',
        'heci casablanca' => 'HECI Casablanca.png',
        'hem casablanca' => 'HEM Casablanca.png',
        'iga casablanca' => 'IGA Casablanca.png',
        'inpt rabat' => 'INPT Rabat.png',
        'iscae casablanca' => 'ISCAE Casablanca.png',
        'isga marrakech' => 'ISGA Marrakech.png',
        'ofppt agadir' => 'OFPPT Agadir.png',
        'supmti casablanca' => 'SUPMTI Casablanca.png',
        'université hassan i' => 'Université Hassan I Setat.PNG',
        'université hassan ii' => 'Université Hassan II Casablanca.PNG',
        'université mohammed v' => 'Université Mohammed V Rabat.PNG',
        'université sidi mohamed ben abdellah' => 'Université Sidi Mohamed Ben Abdellah Fes.png',
        'université sultan moulay slimane' => 'Université Sultan Moulay Slimane Bni melal.PNG',
        'solicode tanger' => 'Solicode-Tanger.png',
        'solicode – centre solidaire digital' => 'Solicode-Tanger.png',
        'solicode – centre solidaire' => 'Solicode-Tanger.png'
    ];

    if (isset($institutionSubfolderImages[$normalizedName])) {
        $filename = $institutionSubfolderImages[$normalizedName];
        return "../assets/images/institutions/" . $filename;
    }

    if (!empty($path) && $path !== 'default_school.jpg') {
        if (strpos($path, '/') !== false) {
            return "../assets/images/" . $path;
        }
        if (file_exists("../assets/images/institutions/" . $path)) {
            return "../assets/images/institutions/" . $path;
        }
        if (file_exists("../assets/images/" . $path)) {
            return "../assets/images/" . $path;
        }
    }

    $extensions = ['.webp', '.png', '.jpg', '.jpeg', '.WEBP', '.PNG', '.JPG', '.JPEG'];
    foreach ($extensions as $ext) {
        $candidate = $name . $ext;
        if (file_exists("../assets/images/institutions/" . $candidate)) {
            return "../assets/images/institutions/" . $candidate;
        }
        if (file_exists("../assets/images/" . $candidate)) {
            return "../assets/images/" . $candidate;
        }
    }

    return "../assets/images/default_school.jpg";
}

$mainImage = count($images) > 0 ? resolveDetailImage($images[0]['image_path'], $inst['name']) : resolveDetailImage($inst['image'], $inst['name']);
?>

<div class="detail-container">
    <?php if ($isCPGE): ?>
        <div class="excellence-ribbon">
            <span class="ribbon-icon">✨</span>
            <span class="ribbon-text"><?php echo __('excellence_track'); ?></span>
        </div>
    <?php endif; ?>

    <div class="detail-hero">
        <img src="<?php echo $mainImage; ?>" alt="<?php echo htmlspecialchars($inst['name']); ?>" class="hero-bg">
        <div class="hero-overlay">
            <div class="hero-text">
                <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 10px;">
                    <span class="type-badge"><?php echo htmlspecialchars(translateType($inst['type'])); ?></span>
                    <?php if ($isAlternativeTech): ?>
                        <span class="tech-badge">🚀 <?php echo __('alternative_school'); ?></span>
                    <?php endif; ?>
                </div>

                <h1><?php echo htmlspecialchars($inst['name']); ?></h1>
                
                <?php if ($parentUniversity): ?>
                    <p class="parent-link-banner" style="margin-top: 10px; font-weight: 600; font-size: 1.05rem;">
                        🏢 <?php echo __('member_of'); ?> : 
                        <a href="institution_detail.php?id=<?php echo $parentUniversity['id']; ?>" style="color: var(--orange); font-weight: 800; text-decoration: underline; transition: color 0.3s ease;">
                            <?php echo htmlspecialchars($parentUniversity['name']); ?>
                        </a>
                    </p>
                <?php else: ?>
                    <p>📍 <?php echo htmlspecialchars($inst['ville_nom'] ?? $inst['city']); ?> — <?php echo __('morocco'); ?></p>
                <?php endif; ?>

                <?php if (count($domainTags) > 0): ?>
                    <div class="hero-domains-tags" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px;">
                        <?php foreach ($domainTags as $dt): ?>
                            <span class="hero-domain-tag" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(5px); border: 1px solid rgba(255, 255, 255, 0.25); color: #fff; padding: 6px 14px; border-radius: 100px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                                🏷️ <?php echo htmlspecialchars($dt['nom']); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-main">
            <section class="info-card">
                <h2><?php echo __('about_institution'); ?></h2>
                <p class="description"><?php echo nl2br(htmlspecialchars($inst['description'])); ?></p>
            </section>

            <?php if (count($subSchools) > 0): ?>
            <section class="info-card">
                <h2>🏢 <?php echo __('connected_faculties'); ?></h2>
                <div class="sub-schools-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 20px;">
                    <?php foreach($subSchools as $sub): ?>
                        <?php
                            $subImage = count($images) > 0 ? resolveDetailImage('', $sub['name']) : resolveDetailImage($sub['image'], $sub['name']);
                        ?>
                        <div class="sub-school-card" onclick="location.href='institution_detail.php?id=<?php echo $sub['id']; ?>'" style="cursor: pointer; border: 1.5px solid var(--border-color); border-radius: 16px; overflow: hidden; background: var(--bg-card); transition: all 0.3s ease; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; justify-content: space-between;">
                            <img src="<?php echo $subImage; ?>" style="width:100%; height:140px; object-fit:cover;" alt="<?php echo htmlspecialchars($sub['name']); ?>">
                            <div style="padding: 15px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <span style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:var(--orange);"><?php echo htmlspecialchars(translateType($sub['type'])); ?></span>
                                    <h4 style="margin: 8px 0 5px 0; font-size:1rem; font-weight:800; color:var(--primary); line-height:1.3;"><?php echo htmlspecialchars($sub['name']); ?></h4>
                                    <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom:10px;">📍 <?php echo htmlspecialchars($sub['ville_nom'] ?? $sub['city']); ?></p>
                                </div>
                                <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border-color); padding-top:10px; font-size:0.8rem; margin-top: auto;">
                                    <span style="color:var(--text-muted);"><?php echo __('seuil'); ?> : <strong style="color:var(--primary);"><?php echo $sub['seuil'] ?? $sub['min_average'] ?? '--'; ?></strong></span>
                                    <span style="font-weight:700; color:var(--orange);"><?php echo __('details_arrow'); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

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

            <section class="info-card reviews-section" id="reviews">
                <div class="reviews-section-header">
                    <h2><?php echo __('student_reviews'); ?></h2>
                    <span class="reviews-count-badge"><?php echo count($reviews); ?> <?php echo __('review_count_label'); ?></span>
                </div>

                <?php if (isset($_GET['review_success'])): ?>
                    <div class="review-flash review-flash-success">
                        ✅ <?php echo __('review_success_msg'); ?>
                    </div>
                <?php elseif (isset($_GET['review_error'])): ?>
                    <div class="review-flash review-flash-error">
                        ⚠️ <?php echo htmlspecialchars($_GET['review_error']); ?>
                    </div>
                <?php endif; ?>

                <?php if ($isLoggedIn): ?>
                    <div class="review-compose">
                        <div class="compose-avatar">
                            <?php
                                $authorInitial = strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1));
                            ?>
                            <span><?php echo htmlspecialchars($authorInitial); ?></span>
                        </div>
                        <div class="compose-body">
                            <form method="POST" action="../submit_review.php">
                                <?php echo csrf_input(); ?>
                                <input type="hidden" name="institution_id" value="<?php echo $id; ?>">
                                <div class="compose-rating" aria-label="Note">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>">
                                        <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> <?php echo __('star_label'); ?>">★</label>
                                    <?php endfor; ?>
                                </div>
                                <textarea name="content" class="compose-textarea" placeholder="<?php echo htmlspecialchars(__('share_review')); ?>" required rows="3"></textarea>
                                <div class="compose-footer">
                                    <span class="compose-hint"><?php echo __('review_moderation_hint'); ?></span>
                                    <button type="submit" class="btn-submit-review">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                        <?php echo __('submit_review'); ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="review-login-cta">
                        <span>💬</span>
                        <p><?php echo __('login_to_review'); ?></p>
                        <a href="login.php" class="btn btn-primary btn-sm"><?php echo __('login'); ?></a>
                    </div>
                <?php endif; ?>

                <div class="reviews-list">
                    <?php if (empty($reviews)): ?>
                        <div class="reviews-empty">
                            <div class="reviews-empty-icon">🎓</div>
                            <p><?php echo __('no_reviews_yet'); ?></p>
                            <span><?php echo __('be_first_review'); ?></span>
                        </div>
                    <?php else: ?>
                        <?php foreach($reviews as $rev):
                            $initials = strtoupper(substr($rev['author_name'], 0, 1));
                            $rating   = isset($rev['rating']) ? (int)$rev['rating'] : 0;
                            $daysAgo  = (int)floor((time() - strtotime($rev['created_at'])) / 86400);
                            if ($daysAgo === 0)      $timeLabel = __('today');
                            elseif ($daysAgo === 1)  $timeLabel = __('yesterday');
                            elseif ($daysAgo < 30)   $timeLabel = sprintf(__('time_ago_days'), $daysAgo);
                            elseif ($daysAgo < 365)  $timeLabel = sprintf(__('time_ago_months'), floor($daysAgo / 30));
                            else                     $timeLabel = date('d/m/Y', strtotime($rev['created_at']));
                        ?>
                            <div class="review-card">
                                <div class="review-card-avatar">
                                    <span><?php echo htmlspecialchars($initials); ?></span>
                                </div>
                                <div class="review-card-body">
                                    <div class="review-card-meta">
                                        <span class="review-card-author"><?php echo htmlspecialchars($rev['author_name']); ?></span>
                                        <?php if ($rating > 0): ?>
                                            <span class="review-card-stars" aria-label="<?php echo $rating; ?> <?php echo __('star_label'); ?>">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <span class="<?php echo $i <= $rating ? 'star-filled' : 'star-empty'; ?>">★</span>
                                                <?php endfor; ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="review-card-time"><?php echo $timeLabel; ?></span>
                                    </div>
                                    <p class="review-card-text"><?php echo nl2br(htmlspecialchars($rev['content'])); ?></p>
                                    <div class="review-card-actions">
                                        <button class="review-helpful-btn" aria-label="<?php echo __('review_helpful'); ?>">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                                            <?php echo __('review_helpful'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
                
                <?php 
                if (!empty($inst['site_web'])): 
                    $site_web = trim($inst['site_web']);
                    if (!preg_match("~^(?:f|ht)tps?://~i", $site_web)) {
                        $site_web = "https://" . $site_web;
                    }
                ?>
                    <a href="<?php echo htmlspecialchars($site_web); ?>" target="_blank" class="btn btn-accent btn-full" style="display: block; text-align: center; border-radius: 30px; font-weight: 800; color: #0f172a; padding: 14px; text-decoration: none;">
                        🌐 <?php echo __('official_website'); ?>
                    </a>
                <?php endif; ?>
                
                <?php if ($isLoggedIn): ?>
                    <form method="POST" action="../save_school.php">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <button type="submit" class="btn btn-outline btn-full" style="display: block; text-align: center; border-radius: 30px; font-weight: 800; padding: 14px; text-decoration: none; border-width: 2px;">
                        ❤ <?php echo __('save'); ?>
                        </button>
                    </form>
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
.hero-text h1 { font-size: 2.5rem; margin: 10px 0; font-weight: 850; line-height: 1.2; text-shadow: 0 2px 4px rgba(0,0,0,0.4); }
.type-badge { background: var(--accent); padding: 6px 16px; border-radius: 20px; font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; }

/* Tech Badge for Alternative coding schools */
.tech-badge { 
    background: linear-gradient(135deg, #10b981 0%, #059669 100%); /* Neon green gradient */
    color: #fff;
    padding: 6px 16px;
    border-radius: 20px;
    font-weight: 800;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

/* CPGE Excellence Ribbon */
.excellence-ribbon {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border: 2px solid #eab308; /* Premium Gold border */
    border-radius: 16px;
    padding: 16px 24px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 10px 25px rgba(234, 179, 8, 0.15);
    animation: goldPulse 3s infinite alternate;
}
@keyframes goldPulse {
    0% { box-shadow: 0 10px 25px rgba(234, 179, 8, 0.15); }
    100% { box-shadow: 0 10px 30px rgba(234, 179, 8, 0.35); }
}
.ribbon-icon {
    font-size: 1.5rem;
}
.ribbon-text {
    font-weight: 850;
    font-size: 1.25rem;
    color: #fef08a; /* Light gold text */
    letter-spacing: 1.5px;
    text-transform: uppercase;
}

.detail-grid { display: grid; grid-template-columns: 1fr 350px; gap: 40px; }
.info-card { background: var(--white); padding: 30px; border-radius: var(--radius-md); box-shadow: var(--shadow-md); margin-bottom: 30px; border: 1px solid var(--border-color); }
.info-card h2 { color: var(--primary); margin-bottom: 20px; font-size: 1.4rem; border-bottom: 2px solid var(--border-color); padding-bottom: 10px; font-weight: 800; }

.image-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-top: 20px; }
.gallery-item img { width: 100%; height: 120px; object-fit: cover; border-radius: 12px; cursor: pointer; transition: 0.3s; border: 2px solid transparent; }
.gallery-item img:hover { transform: translateY(-5px); border-color: var(--accent); }

.filieres-list { display: grid; gap: 15px; }
.filiere-item { padding: 18px; border: 1px solid var(--border-color); border-radius: var(--radius-md); transition: var(--transition); background: #f8fafc; }
.filiere-item:hover { border-color: var(--accent); background: rgba(var(--primary-rgb), 0.02); transform: translateX(5px); }
.filiere-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }
.filiere-item h3 { font-size: 1.1rem; color: var(--primary); margin: 0; font-weight: 800; }
.domain-tag { font-size: 0.75rem; background: rgba(var(--primary-rgb), 0.08); color: var(--primary); padding: 4px 10px; border-radius: 6px; font-weight: 700; }
.filiere-item p { font-size: 0.9rem; color: var(--text-muted); line-height: 1.5; }

.bac-req-list { display: grid; gap: 8px; margin-top: 10px; }
.bac-req-item { display: flex; justify-content: space-between; background: rgba(255,255,255,0.06); padding: 8px 14px; border-radius: 8px; font-size: 0.9rem; border: 1px solid rgba(255,255,255,0.1); }
.bac-code { font-weight: 700; color: var(--accent); }

.detail-sidebar .sidebar-card { background: var(--primary); color: #fff; padding: 30px; border-radius: var(--radius-md); box-shadow: var(--shadow-lg); position: sticky; top: 100px; }
.sidebar-card h3 { margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 10px; font-weight: 800; }
.info-list { list-style: none; padding: 0; }
.info-list li { margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; }
.info-list li strong { display: block; font-size: 0.8rem; color: rgba(255,255,255,0.6); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; }
.info-list li span { font-weight: 600; font-size: 1.1rem; }
.req-text { font-size: 0.9rem; color: rgba(255,255,255,0.9); line-height: 1.5; }

.btn-full { width: 100%; margin-top: 15px; padding: 12px; font-weight: 700; border-radius: 12px; }
.btn-accent { background: var(--accent); color: #fff; transition: var(--transition); }
.btn-accent:hover { background: #ea580c; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2); }
.btn-outline { border: 1.5px solid #fff; background: transparent; color: #fff; transition: var(--transition); }
.btn-outline:hover { background: #fff; color: var(--primary); transform: translateY(-2px); }

/* ── Reviews Section ─────────────────────────────────────────────────────── */
#reviews { scroll-margin-top: 90px; } /* offset for sticky navbar */
.reviews-section { padding: 30px; }

.reviews-section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--border-color);
}
.reviews-section-header h2 {
    margin: 0;
    border: none;
    padding: 0;
}
.reviews-count-badge {
    background: rgba(var(--primary-rgb), 0.08);
    color: var(--primary);
    font-size: 0.8rem;
    font-weight: 800;
    padding: 4px 12px;
    border-radius: 20px;
    letter-spacing: 0.3px;
}

/* Compose box */
.review-compose {
    display: flex;
    gap: 14px;
    align-items: flex-start;
    background: #f8fafc;
    border: 1.5px solid var(--border-color);
    border-radius: 18px;
    padding: 20px;
    margin-bottom: 28px;
    transition: border-color 0.2s;
}
.review-compose:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.06);
}
.compose-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), #3b5bdb);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-weight: 800;
    font-size: 1rem;
    color: #fff;
}
.compose-body { flex: 1; display: flex; flex-direction: column; gap: 10px; }

/* Star rating widget */
.compose-rating {
    display: flex;
    flex-direction: row-reverse;
    gap: 3px;
    width: fit-content;
}
.compose-rating input { display: none; }
.compose-rating label {
    font-size: 1.5rem;
    color: #d1d5db;
    cursor: pointer;
    transition: color 0.15s;
    line-height: 1;
}
/* When hovering: fill hovered star and all stars to its right (= higher values in row-reverse) */
.compose-rating label:hover,
.compose-rating label:hover ~ label {
    color: #f59e0b;
}
/* When a radio is checked: fill it and all its preceding siblings (higher values)
   In row-reverse those are visually to the left (higher numbers = left = filled) */
.compose-rating input:checked ~ label {
    color: #f59e0b;
}

.compose-textarea {
    width: 100%;
    border: none;
    background: transparent;
    font-size: 0.97rem;
    color: var(--text-dark);
    resize: none;
    outline: none;
    font-family: inherit;
    line-height: 1.6;
}
.compose-textarea::placeholder { color: var(--text-muted); }

.compose-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 10px;
    border-top: 1px solid var(--border-color);
}
.compose-hint {
    font-size: 0.78rem;
    color: var(--text-muted);
}
.btn-submit-review {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: var(--primary);
    color: #fff;
    border: none;
    padding: 9px 20px;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s, transform 0.15s;
}
.btn-submit-review:hover {
    background: #1e3a8a;
    transform: translateY(-1px);
}

/* Login CTA */
.review-login-cta {
    display: flex;
    align-items: center;
    gap: 14px;
    background: #f8fafc;
    border: 1.5px dashed var(--border-color);
    border-radius: 14px;
    padding: 18px 22px;
    margin-bottom: 28px;
    font-size: 0.95rem;
    color: var(--text-muted);
}
.review-login-cta span { font-size: 1.5rem; }
.review-login-cta p { flex: 1; margin: 0; }
.btn-sm { padding: 8px 18px; font-size: 0.85rem; font-weight: 700; border-radius: 10px; }

/* Review cards */
.reviews-list { display: flex; flex-direction: column; gap: 0; }

.review-card {
    display: flex;
    gap: 14px;
    padding: 20px 0;
    border-bottom: 1px solid var(--border-color);
    animation: fadeIn 0.3s ease;
}
.review-card:last-child { border-bottom: none; }

@keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

.review-card-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-weight: 800;
    font-size: 1rem;
    color: #fff;
}
/* Rotate colours per avatar */
.review-card:nth-child(2n)  .review-card-avatar { background: linear-gradient(135deg, var(--primary), #3b5bdb); }
.review-card:nth-child(3n)  .review-card-avatar { background: linear-gradient(135deg, #f59e0b, #ef4444); }
.review-card:nth-child(4n)  .review-card-avatar { background: linear-gradient(135deg, #10b981, #0891b2); }

.review-card-body { flex: 1; }

.review-card-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 8px;
}
.review-card-author {
    font-weight: 800;
    font-size: 0.97rem;
    color: var(--primary-dark);
}
.review-card-time {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-left: auto;
}

/* Stars */
.review-card-stars { display: inline-flex; gap: 1px; }
.star-filled { color: #f59e0b; font-size: 0.95rem; }
.star-empty  { color: #d1d5db; font-size: 0.95rem; }

.review-card-text {
    font-size: 0.95rem;
    line-height: 1.65;
    color: var(--text-dark);
    margin: 0 0 10px;
}

.review-card-actions { display: flex; gap: 12px; }
.review-helpful-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: none;
    border: 1px solid var(--border-color);
    color: var(--text-muted);
    font-size: 0.78rem;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.2s;
}
.review-helpful-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: rgba(var(--primary-rgb), 0.04);
}

/* Flash messages */
.review-flash {
    padding: 13px 18px;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 20px;
}
.review-flash-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.review-flash-error   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

/* Empty state */
.reviews-empty {
    text-align: center;
    padding: 50px 20px;
    color: var(--text-muted);
}
.reviews-empty-icon { font-size: 3rem; margin-bottom: 12px; opacity: 0.6; }
.reviews-empty p { font-weight: 700; font-size: 1rem; margin-bottom: 4px; color: var(--text-dark); }
.reviews-empty span { font-size: 0.88rem; }

/* Dark mode */
[data-theme="dark"] .review-compose,
[data-theme="dark"] .review-login-cta {
    background: #0f172a;
    border-color: #242f49;
}
[data-theme="dark"] .review-card { border-color: #242f49; }
[data-theme="dark"] .review-card-author { color: #e2e8f0; }
[data-theme="dark"] .review-card-text { color: #cbd5e1; }
[data-theme="dark"] .compose-textarea { color: #e2e8f0; }

@media (max-width: 640px) {
    .compose-footer { flex-direction: column; align-items: flex-start; gap: 10px; }
    .review-card-time { margin-left: 0; }
}
/* ────────────────────────────────────────────────────────────────────────── */

[data-theme="dark"] .info-card { background: #161e31; border-color: #242f49; }
[data-theme="dark"] .filiere-item { border-color: #242f49; background: #0f172a; }
[data-theme="dark"] .filiere-item:hover { border-color: var(--accent); }

@media (max-width: 992px) {
    .detail-grid { grid-template-columns: 1fr; }
    .detail-hero { height: 300px; }
    .hero-text h1 { font-size: 1.8rem; }
    .excellence-ribbon { flex-direction: column; text-align: center; gap: 8px; }
}
</style>

<?php require "../includes/footer.php"; ?>
