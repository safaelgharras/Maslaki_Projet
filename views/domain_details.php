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
?>

<div class="domain-details-page">
    <div class="domain-hero">
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
                    <label>🏢 <?php echo __('type'); ?></label>
                    <select id="filterType" class="modern-select">
                        <option value=""><?php echo __('all_types'); ?></option>
                        <option value="Public"><?php echo __('type_public'); ?></option>
                        <option value="Private"><?php echo __('type_private'); ?></option>
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
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); 
    color: white; 
    padding: 140px 0 200px; 
    margin-bottom: -100px; 
    border-radius: 0 0 80px 80px; 
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
    type_public: <?php echo json_encode(__('type_public')); ?>
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
        if (inst.image && inst.image !== 'default_school.jpg') {
            return `../assets/images/institutions/${inst.image}`;
        }
        // Basic resolution by name
        return `../assets/images/default_school.jpg`; 
    }

    function translateType(type) {
        const map = {
            'Engineering': langTranslations.type_engineering,
            'Business': langTranslations.type_business,
            'Science': langTranslations.type_science,
            'Technical': langTranslations.type_technical,
            'Preparatory': langTranslations.type_preparatory,
            'Private': langTranslations.type_private,
            'Public': langTranslations.type_public,
            'Education': langTranslations.type_education,
            'University': langTranslations.type_university
        };
        return map[type] || type;
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
