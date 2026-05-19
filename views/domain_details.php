<?php
session_start();
require "../config/DataBase.php";
require_once "../includes/lang_helper.php";

$domainId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($domainId <= 0) {
    header("Location: orientation_explore.php");
    exit();
}

// Fetch domain info
$stmt = $pdo->prepare("SELECT d.*, c.nom as category_name, c.nom_ar as category_name_ar, c.nom_en as category_name_en 
                       FROM domains d 
                       LEFT JOIN categories c ON d.categorie_id = c.id 
                       WHERE d.id = ?");
$stmt->execute([$domainId]);
$domain = $stmt->fetch();

if (!$domain) {
    header("Location: orientation_explore.php");
    exit();
}

// Localize domain and category names
$domain['nom'] = getLocalizedDbField($domain, 'nom');
$domain['category_name'] = getLocalizedDbField($domain, 'category_name');

$pageTitle = $domain['nom'];
$base = "../";
require "../includes/header.php";

// Fetch filieres for this domain
$stmt = $pdo->prepare("SELECT * FROM filieres WHERE domain_id = ? ORDER BY nom ASC");
$stmt->execute([$domainId]);
$filieres = $stmt->fetchAll();
foreach ($filieres as &$f) {
    $f['nom'] = getLocalizedDbField($f, 'nom');
}
unset($f);

// Fetch cities for filtering
$villes = $pdo->query("SELECT * FROM villes ORDER BY nom ASC")->fetchAll();
foreach ($villes as &$v) {
    $v['nom'] = getLocalizedDbField($v, 'nom');
}
unset($v);

$isLoggedIn = isset($_SESSION['user_id']);
$savedIds = [];
if ($isLoggedIn) {
    $savedIds = $pdo->query("SELECT institution_id FROM saved_schools WHERE student_id = " . $_SESSION['user_id'])->fetchAll(PDO::FETCH_COLUMN);
}

// Helper to resolve images (reusing logic from institutions.php)
function resolveDomainCardImage($name, $dbImage = null) {
    $name = trim((string) $name);
    $normalizedName = strtolower($name);
    
    if (!empty($dbImage) && file_exists(__DIR__ . '/../assets/images/institutions/' . $dbImage)) {
        return '../assets/images/institutions/' . $dbImage;
    }
    
    $extensions = ['webp', 'png', 'jpg'];
    foreach ($extensions as $ext) {
        if (file_exists(__DIR__ . "/../assets/images/institutions/$name.$ext")) {
            return "../assets/images/institutions/$name.$ext";
        }
        if (file_exists(__DIR__ . "/../assets/images/$name.$ext")) {
            return "../assets/images/$name.$ext";
        }
    }
    return '../assets/images/default_school.jpg';
}

$domainStyles = [
    1 => 'background: linear-gradient(135deg, rgba(15,23,42,0.92) 0%, rgba(14,165,233,0.85) 100%), url("data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.06\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") center/cover;',
    2 => 'background: linear-gradient(135deg, rgba(24,24,27,0.95) 0%, rgba(234,88,12,0.85) 100%), url("data:image/svg+xml,%3Csvg width=\'52\' height=\'26\' viewBox=\'0 0 52 26\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M10 10c0-2.21-1.79-4-4-4-3.314 0-6-2.686-6-6h2c0 2.21 1.79 4 4 4 3.314 0 6 2.686 6 6 0 2.21 1.79 4 4 4 3.314 0 6 2.686 6 6 0 2.21 1.79 4 4 4v2c-3.314 0-6-2.686-6-6 0-2.21-1.79-4-4-4-3.314 0-6-2.686-6-6zm25.464-1.95l8.486 8.486-1.414 1.414-8.486-8.486 1.414-1.414z\' /%3E%3C/g%3E%3C/g%3E%3C/svg%3E") center/cover;',
    3 => 'background: linear-gradient(135deg, rgba(6,78,59,0.92) 0%, rgba(20,184,166,0.85) 100%), url("data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.06\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'3\'/%3E%3Ccircle cx=\'13\' cy=\'13\' r=\'3\'/%3E%3C/g%3E%3C/svg%3E") center/cover;',
    4 => 'background: linear-gradient(135deg, rgba(39,39,42,0.95) 0%, rgba(101,163,13,0.85) 100%), url("data:image/svg+xml,%3Csvg viewBox=\'0 0 200 200\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cfilter id=\'noiseFilter\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.65\' numOctaves=\'3\' stitchTiles=\'stitch\'/%3E%3C/filter%3E%3Crect width=\'100%25\' height=\'100%25\' filter=\'url(%23noiseFilter)\' opacity=\'0.08\'/%3E%3C/svg%3E") center/cover;',
    5 => 'background: linear-gradient(135deg, rgba(30,58,138,0.92) 0%, rgba(202,138,4,0.85) 100%), url("data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.06\' fill-rule=\'evenodd\'%3E%3Cpath d=\'M0 40L40 0H20L0 20M40 40V20L20 40\'/%3E%3C/g%3E%3C/svg%3E") center/cover;',
    6 => 'background: linear-gradient(135deg, rgba(67,20,7,0.92) 0%, rgba(185,28,28,0.85) 100%), url("data:image/svg+xml,%3Csvg width=\'100\' height=\'100\' viewBox=\'0 0 100 100\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.04\' fill-rule=\'evenodd\'%3E%3Cpath d=\'M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z\'/%3E%3C/g%3E%3C/svg%3E") center/cover;',
    7 => 'background: linear-gradient(135deg, rgba(88,28,135,0.92) 0%, rgba(219,39,119,0.85) 100%), url("data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.06\' fill-rule=\'evenodd\'%3E%3Cpath d=\'M0 20L20 40H40L20 0z\'/%3E%3C/g%3E%3C/svg%3E") center/cover;',
    8 => 'background: linear-gradient(135deg, rgba(14,116,144,0.92) 0%, rgba(14,165,233,0.85) 100%), url("data:image/svg+xml,%3Csvg width=\'100\' height=\'20\' viewBox=\'0 0 100 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M21.184 20c.302-1.354.724-2.67 1.258-3.922A19.89 19.89 0 0 1 32 6.004V0H0v20h21.184zM60 20h40V0H68A19.89 19.89 0 0 0 60 6.004v13.996z\' fill=\'%23ffffff\' fill-opacity=\'0.06\' fill-rule=\'evenodd\'/%3E%3C/svg%3E") center/cover;',
    9 => 'background: linear-gradient(135deg, rgba(120,53,15,0.92) 0%, rgba(217,119,6,0.85) 100%), url("data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.06\' fill-rule=\'evenodd\'%3E%3Cpath d=\'M0 0h20v20H0V0zm10 17L3 10l7-7 7 7-7 7z\'/%3E%3C/g%3E%3C/svg%3E") center/cover;',
    10 => 'background: linear-gradient(135deg, rgba(51,65,85,0.92) 0%, rgba(71,85,105,0.85) 100%), url("data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.06\' fill-rule=\'evenodd\'%3E%3Cpath d=\'M0 0h40v40H0V0zm20 20h20v20H20V20zM0 20h20v20H0V20z\'/%3E%3C/g%3E%3C/svg%3E") center/cover;'
];
$heroBgStyle = isset($domainStyles[$domain['id']]) ? $domainStyles[$domain['id']] : 'background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);';
?>

<div class="domain-details-page">
    <div class="domain-hero" style="<?php echo $heroBgStyle; ?>">
        <div class="container">
            <div class="hero-path">
                <a href="orientation_explore.php"><?php echo __("orientation"); ?></a> / 
                <span><?php echo htmlspecialchars($domain['category_name']); ?></span>
            </div>
            <h1 class="domain-title"><?php echo htmlspecialchars($domain['nom']); ?></h1>
            <p class="domain-description">
                <?php echo __('discover_schools_in'); ?> <?php echo htmlspecialchars($domain['nom']); ?>.
            </p>
            <div class="domain-stats">
                <span id="schoolCountBadge" class="stat-badge"><?php echo __('loading'); ?></span>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="filter-section-modern glassmorphism">
            <div class="filter-row">
                <div class="filter-col">
                    <label>📍 <?php echo __("city"); ?></label>
                    <select id="filterCity" class="modern-select">
                        <option value=""><?php echo __("all_cities"); ?></option>
                        <?php foreach($villes as $v): ?>
                            <option value="<?php echo $v['id']; ?>"><?php echo htmlspecialchars($v['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-col">
                    <label>🏢 <?php echo __('sector'); ?></label>
                    <select id="filterType" class="modern-select">
                        <option value=""><?php echo __('all_types'); ?></option>
                        <option value="public"><?php echo __('sector_public'); ?></option>
                        <option value="private"><?php echo __('sector_private'); ?></option>
                        <option value="semi-public"><?php echo __('sector_semi_public'); ?></option>
                        <option value="alternative"><?php echo __('sector_alternative'); ?></option>
                    </select>
                </div>
            </div>
            
            <div class="filiere-tags-container">
                <label>📚 <?php echo __("filieres"); ?> :</label>
                <div class="tags-scroll">
                    <button class="tag-chip active" data-filiere=""><?php echo __("all"); ?></button>
                    <?php foreach($filieres as $f): ?>
                        <button class="tag-chip" data-filiere="<?php echo $f['id']; ?>">
                            <?php echo htmlspecialchars($f['nom']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div id="resultsGrid" class="cards-grid">
            <!-- Loaded via AJAX -->
            <div class="loading-spinner"></div>
        </div>

        <div id="emptyState" class="empty-state-modern" style="display:none;">
            <div class="empty-icon">🔍</div>
            <h3><?php echo __('no_school_found'); ?></h3>
            <p><?php echo __('try_modify_filters'); ?></p>
            <button onclick="resetDomainFilters()" class="btn btn-primary"><?php echo __('reset'); ?></button>
        </div>
    </div>
</div>

<style>
.domain-details-page { padding-bottom: 120px; background: #fdfdfd; }
.domain-hero { 
    color: white; 
    padding: 110px 0 140px; 
    margin-bottom: -70px; 
    border-radius: 0 0 50px 50px; 
    position: relative;
    overflow: hidden;
}
.domain-hero::after {
    content: '';
    position: absolute;
    top: -50%; left: -10%;
    width: 80%; height: 200%;
    background: radial-gradient(circle, rgba(var(--primary-rgb), 0.15) 0%, transparent 70%);
    pointer-events: none;
    filter: blur(100px);
}
.hero-path { 
    font-size: 0.9rem; 
    margin-bottom: 35px; 
    opacity: 0.6; 
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
}
.domain-title { 
    font-size: clamp(3rem, 6vw, 5rem); 
    font-weight: 950; 
    margin-bottom: 30px; 
    line-height: 1; 
    letter-spacing: -3px;
    background: linear-gradient(to bottom, #fff, #cbd5e1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.domain-description { font-size: 1.4rem; opacity: 0.8; max-width: 850px; line-height: 1.7; margin-bottom: 45px; }
.stat-badge { 
    background: rgba(255,255,255,0.08); 
    padding: 14px 35px; 
    border-radius: 100px; 
    font-weight: 800; 
    font-size: 1.1rem; 
    border: 1px solid rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
}

.filter-section-modern { 
    background: rgba(255, 255, 255, 0.8); 
    padding: 50px; 
    border-radius: 40px; 
    box-shadow: 0 40px 100px rgba(0,0,0,0.1); 
    margin-bottom: 80px; 
    position: relative; 
    z-index: 10; 
    border: 1px solid rgba(255, 255, 255, 0.6); 
    backdrop-filter: blur(30px) saturate(180%);
}
.filter-row { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; margin-bottom: 50px; }
.filter-col label { 
    display: flex; 
    align-items: center; 
    gap: 12px;
    font-weight: 900; 
    margin-bottom: 20px; 
    font-size: 0.85rem; 
    color: var(--text-muted); 
    text-transform: uppercase; 
    letter-spacing: 2.5px; 
}
.modern-select { 
    width: 100%; 
    padding: 18px 25px; 
    border-radius: 20px; 
    border: 2px solid #e2e8f0; 
    background: #f8fafc; 
    font-weight: 700; 
    font-size: 1.05rem;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 25px center;
    background-size: 20px;
    cursor: pointer;
}
[dir="rtl"] .modern-select, body.rtl .modern-select {
    background-position: left 25px center;
    padding-right: 25px;
    padding-left: 55px;
}
.modern-select:hover { 
    transform: translateY(-3px); 
    border-color: #cbd5e1; 
    box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
    background-color: white;
}
.modern-select:focus { 
    border-color: var(--primary); 
    box-shadow: 0 0 0 8px rgba(var(--primary-rgb), 0.12); 
    outline: none; 
    background-color: white; 
}

.filiere-tags-container label { 
    display: flex; 
    align-items: center; 
    gap: 12px;
    font-weight: 900; 
    margin-bottom: 25px; 
    font-size: 0.85rem; 
    color: var(--text-muted); 
    text-transform: uppercase; 
    letter-spacing: 2.5px; 
}
.tags-scroll { display: flex; flex-wrap: wrap; gap: 18px; }
.tag-chip { 
    background: #f1f5f9; 
    border: 2px solid transparent; 
    padding: 14px 32px; 
    border-radius: 22px; 
    cursor: pointer; 
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
    font-weight: 800; 
    font-size: 1rem; 
    color: var(--text-main); 
}
.tag-chip:hover { transform: translateY(-5px) scale(1.05); background: white; box-shadow: 0 15px 35px rgba(0,0,0,0.08); }
.tag-chip.active { 
    background: var(--primary); 
    color: white; 
    border-color: var(--primary); 
    box-shadow: 0 20px 45px rgba(var(--primary-rgb), 0.35); 
}

.cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 50px; margin-top: 80px; }

.empty-state-modern { 
    text-align: center; 
    padding: 120px 60px; 
    background: var(--bg-card); 
    border-radius: 50px; 
    border: 2px dashed #cbd5e1; 
    margin-top: 80px; 
    animation: fadeIn 1s cubic-bezier(0.23, 1, 0.32, 1);
}
@keyframes fadeIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.empty-icon { font-size: 7rem; margin-bottom: 40px; filter: grayscale(1); opacity: 0.25; }
.empty-state-modern h3 { font-size: 2.2rem; font-weight: 900; margin-bottom: 20px; color: var(--text-main); }
.empty-state-modern p { color: var(--text-muted); font-size: 1.3rem; margin-bottom: 50px; }

[data-theme="dark"] .filter-section-modern { background: rgba(15, 23, 42, 0.75); border-color: rgba(255,255,255,0.12); }
[data-theme="dark"] .modern-select { background: #0b1121; border-color: #1e293b; color: white; }
[data-theme="dark"] .tag-chip { background: #1e293b; color: #94a3b8; }
[data-theme="dark"] .tag-chip:hover { background: #334155; color: white; }

@media (max-width: 768px) {
    .domain-hero { padding: 100px 0 140px; }
    .domain-hero .container { padding: 0 30px; }
    .domain-title { font-size: 3rem; }
    .filter-row { grid-template-columns: 1fr; gap: 35px; }
    .filter-section-modern { padding: 40px 30px; border-radius: 35px; }
}

/* School Card Enhancements for this page */
.school-card-filieres { margin-top: 25px; padding-top: 25px; border-top: 1.5px solid var(--border-color); }
.school-card-filieres label { display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 5px; text-transform: uppercase; }
.filiere-list-mini { font-size: 0.85rem; color: var(--text-main); font-weight: 500; line-height: 1.4; }
[data-theme="dark"] .filter-section-modern { background: #1a233a; }
[data-theme="dark"] .modern-select { background: #0b1121; color: white; }
[data-theme="dark"] .tag-chip { background: #2d3748; color: #cbd5e0; }
[data-theme="dark"] .empty-state-modern { background: #161e31; }

@media (max-width: 768px) {
    .domain-title { font-size: 2.2rem; }
    .filter-row { grid-template-columns: 1fr; }
}
</style>

<script>
const langTranslations = {
    schools_found: <?php echo json_encode(__('schools_found')); ?>,
    no_school_found: <?php echo json_encode(__('no_school_found')); ?>,
    available_filieres: <?php echo json_encode(__('available_filieres')); ?>,
    multiple_filieres: <?php echo json_encode(__('multiple_filieres')); ?>,
    seuil: <?php echo json_encode(__('seuil')); ?>,
    details_arrow: <?php echo json_encode(__('details_arrow')); ?>,
    type_university: <?php echo json_encode(__('type_university')); ?>,
    type_preparatory: <?php echo json_encode(__('type_preparatory')); ?>,
    type_engineering: <?php echo json_encode(__('type_engineering')); ?>,
    type_business: <?php echo json_encode(__('type_business')); ?>,
    type_science: <?php echo json_encode(__('type_science')); ?>,
    type_technical: <?php echo json_encode(__('type_technical')); ?>,
    type_education: <?php echo json_encode(__('type_education')); ?>,
    type_private: <?php echo json_encode(__('type_private')); ?>,
    type_public: <?php echo json_encode(__('type_public')); ?>,
    type_digital: <?php echo json_encode(__('type_digital')); ?>
};

document.addEventListener('DOMContentLoaded', function() {
    const domainId = <?php echo $domainId; ?>;
    const filterCity = document.getElementById('filterCity');
    const filterType = document.getElementById('filterType');
    const tagChips = document.querySelectorAll('.tag-chip');
    const resultsGrid = document.getElementById('resultsGrid');
    const emptyState = document.getElementById('emptyState');
    const schoolCountBadge = document.getElementById('schoolCountBadge');
    
    let activeFiliere = "";

    function updateResults() {
        const params = new URLSearchParams();
        params.set('domain_id', domainId);
        if (filterCity.value) params.set('city_id', filterCity.value);
        if (filterType.value) params.set('type', filterType.value);
        if (activeFiliere) params.set('filiere_id', activeFiliere);

        resultsGrid.innerHTML = '<div style="grid-column: 1/-1; text-align:center; padding: 50px;"><div class="loading-spinner"></div></div>';
        
        fetch(`../search_ajax.php?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    resultsGrid.style.display = 'none';
                    emptyState.style.display = 'block';
                    schoolCountBadge.textContent = langTranslations.no_school_found;
                } else {
                    resultsGrid.style.display = 'grid';
                    emptyState.style.display = 'none';
                    schoolCountBadge.textContent = data.length + ' ' + langTranslations.schools_found;
                    renderSchools(data);
                }
            });
    }

    function renderSchools(schools) {
        resultsGrid.innerHTML = schools.map(inst => `
            <div class="card hover-lift">
                <img src="${resolveImage(inst)}" class="card-img" alt="${inst.name}">
                <div class="card-body">
                    <div class="badge">${translateType(inst.type)}</div>
                    <h3>${inst.name}</h3>
                    <p class="school-location">📍 ${inst.city || 'Maroc'}</p>
                    
                    <div class="school-card-filieres">
                        <label>${langTranslations.available_filieres}</label>
                        <div class="filiere-list-mini">${inst.filieres_list || langTranslations.multiple_filieres}</div>
                    </div>

                    <div class="card-footer" style="margin-top: 20px;">
                        <span class="seuil">${langTranslations.seuil}: <strong>${inst.seuil || inst.min_average || '--'}</strong></span>
                        <a href="institution_detail.php?id=${inst.id}" class="btn-link">${langTranslations.details_arrow}</a>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function resolveImage(inst) {
        const name = (inst.name || '').trim();
        const normalizedName = name.toLowerCase();

        const institutionSubfolderImages = {
            'cpge fes': 'CPGE Fez.jpg',
            'cpge fez': 'CPGE Fez.jpg',
            'cpge kenitra': 'CPGE Kenitra .jpg',
            'cpge marrakech': 'CPGE Marrakech.WEBP',
            'cpge oujda': 'CPGE Oujda.PNG',
            'eigsi casablanca': 'EIGSI Casablanca.webp',
            'emi rabat': 'EMI Rabat.webp',
            'emsi casablanca': 'EMSI Casablanca.webp',
            'emsi rabat': 'EMSI Rabat.PNG',
            'encg agadir': 'ENCG Agadir.webp',
            'encg kenitra': 'ENCG Kenitra.png',
            'encg marrakech': 'ENCG Marrakech.webp',
            'encg oujda': 'ENCG Oujda.webp',
            'encg settat': 'ENCG Settat.webp',
            'ens rabat': 'ENS Rabat.png',
            'ensa casablanca': 'ENSA Casablanca.png',
            'ensa fes': 'ENSA Fes.png',
            'ensa kenitra': 'ENSA Kenitra.png',
            'ensa marrakech': 'ENSA Marrakech.png',
            'ensa oujda': 'ENSA Oujda.png',
            'ensa tanger': 'ENSA Tanger.png',
            'enset mohammedia': 'ENSET Mohammedia.webp',
            'ensias rabat': 'ENSIAS Rabat.png',
            'esca ecole de management': 'ESCA Ecole de Management Casablanca.webp',
            'est agadir': 'EST Agadir.png',
            'est casablanca': 'EST Casablanca.png',
            'est fes': 'EST Fes.png',
            'est kenitra': 'EST Kenitra.webp',
            'est laayoune': 'EST Laayoune.png',
            'est oujda': 'EST Oujda.webp',
            'fs beni mellal': 'FS Beni Mellal.png',
            'fs casablanca': 'FS Casablanca.png',
            'fs errachidia': 'FS Errachidia.png',
            'fs meknes': 'FS Meknes.png',
            'fs oujda': 'FS Oujda.png',
            'fs rabat': 'FS Rabat.png',
            'fst al hoceima': 'FST Al Hoceima.jpg',
            'fst casablanca': 'FST Casablanca.png',
            'fst mohammedia': 'FST Mohammedia.png',
            'fst settat': 'FST Settat - Hassan 1er.png',
            'fst tanger': 'FST Tanger.png',
            'heci casablanca': 'HECI Casablanca.png',
            'hem casablanca': 'HEM Casablanca.png',
            'iga casablanca': 'IGA Casablanca.png',
            'inpt rabat': 'INPT Rabat.png',
            'iscae casablanca': 'ISCAE Casablanca.png',
            'isga marrakech': 'ISGA Marrakech.png',
            'ofppt agadir': 'OFPPT Agadir.png',
            'supmti casablanca': 'SUPMTI Casablanca.png',
            'université hassan i': 'Université Hassan I Setat.PNG',
            'université hassan ii': 'Université Hassan II Casablanca.PNG',
            'université mohammed v': 'Université Mohammed V Rabat.PNG',
            'université sidi mohamed ben abdellah': 'Université Sidi Mohamed Ben Abdellah Fes.png',
            'université sultan moulay slimane': 'Université Sultan Moulay Slimane Bni melal.PNG'
        };

        let filename = '';
        let folder = 'institutions/';

        if (institutionSubfolderImages[normalizedName]) {
            filename = institutionSubfolderImages[normalizedName];
        } else if (inst.image && inst.image !== 'default_school.jpg') {
            filename = inst.image;
            if (filename.includes('/')) return `../assets/images/${filename}`;
        } else {
            return '../assets/images/default_school.jpg';
        }

        const safeFilename = filename.replace(/ /g, '%20');
        return `../assets/images/${folder}${safeFilename}`;
    }

    function translateType(type) {
        if (!type) return '';
        const map = {
            'Engineering': langTranslations.type_engineering,
            'Business': langTranslations.type_business,
            'Science': langTranslations.type_science,
            'Technical': langTranslations.type_technical,
            'Preparatory': langTranslations.type_preparatory,
            'Private': langTranslations.type_private,
            'Public': langTranslations.type_public,
            'Education': langTranslations.type_education,
            'University': langTranslations.type_university,
            'Digital': langTranslations.type_digital,
            'public': <?php echo json_encode(__('sector_public')); ?>,
            'private': <?php echo json_encode(__('sector_private')); ?>,
            'semi-public': <?php echo json_encode(__('sector_semi_public')); ?>,
            'alternative': <?php echo json_encode(__('sector_alternative')); ?>
        };
        const key = type.toLowerCase();
        return map[type] || map[key] || type;
    }

    filterCity.addEventListener('change', updateResults);
    filterType.addEventListener('change', updateResults);

    tagChips.forEach(chip => {
        chip.addEventListener('click', function() {
            tagChips.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            activeFiliere = this.dataset.filiere;
            updateResults();
        });
    });

    window.resetDomainFilters = function() {
        filterCity.value = "";
        filterType.value = "";
        tagChips.forEach(c => c.classList.remove('active'));
        tagChips[0].classList.add('active');
        activeFiliere = "";
        updateResults();
    };

    // Initial load
    updateResults();
});
</script>

<?php require "../includes/footer.php"; ?>
