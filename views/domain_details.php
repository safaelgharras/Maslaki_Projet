<?php
session_start();
require "../config/DataBase.php";

$domainId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($domainId <= 0) {
    header("Location: orientation_explore.php");
    exit();
}

// Fetch domain info
$stmt = $pdo->prepare("SELECT d.*, c.nom as category_name FROM domains d LEFT JOIN categories c ON d.categorie_id = c.id WHERE d.id = ?");
$stmt->execute([$domainId]);
$domain = $stmt->fetch();

if (!$domain) {
    header("Location: orientation_explore.php");
    exit();
}

$pageTitle = $domain['nom'];
$base = "../";
require "../includes/header.php";

// Fetch filieres for this domain
$stmt = $pdo->prepare("SELECT * FROM filieres WHERE domain_id = ? ORDER BY nom ASC");
$stmt->execute([$domainId]);
$filieres = $stmt->fetchAll();

// Fetch cities for filtering
$villes = $pdo->query("SELECT * FROM villes ORDER BY nom ASC")->fetchAll();

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
.domain-details-page { padding-bottom: 80px; }
.domain-hero { background: linear-gradient(135deg, var(--primary-dark), var(--primary)); color: white; padding: 60px 0 100px; margin-bottom: -60px; border-radius: 0 0 50px 50px; }
.hero-path { font-size: 0.9rem; margin-bottom: 15px; opacity: 0.8; }
.hero-path a { color: white; text-decoration: none; }
.domain-title { font-size: 3rem; font-weight: 800; margin-bottom: 10px; }
.domain-description { font-size: 1.1rem; opacity: 0.9; max-width: 600px; }
.stat-badge { background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 8px 20px; border-radius: 30px; font-weight: 600; margin-top: 20px; display: inline-block; border: 1px solid rgba(255,255,255,0.3); }

.filter-section-modern { background: white; padding: 30px; border-radius: 24px; box-shadow: var(--shadow-lg); margin-bottom: 40px; position: relative; z-index: 10; border: 1px solid var(--border-color); }
.filter-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
.filter-col label { display: block; font-weight: 700; margin-bottom: 8px; font-size: 0.9rem; color: var(--text-muted); }
.modern-select { width: 100%; padding: 12px 15px; border-radius: 12px; border: 1.5px solid var(--border-color); background: #f8fafc; font-weight: 500; }

.filiere-tags-container label { display: block; font-weight: 700; margin-bottom: 12px; font-size: 0.9rem; color: var(--text-muted); }
.tags-scroll { display: flex; flex-wrap: wrap; gap: 10px; }
.tag-chip { background: #f1f5f9; border: 1.5px solid transparent; padding: 8px 18px; border-radius: 50px; cursor: pointer; transition: all 0.3s ease; font-weight: 600; font-size: 0.85rem; color: var(--text-main); }
.tag-chip:hover { background: #e2e8f0; }
.tag-chip.active { background: var(--primary); color: white; border-color: var(--primary); box-shadow: 0 4px 10px rgba(var(--primary-rgb), 0.3); }

.cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px; margin-top: 40px; }

.empty-state-modern { text-align: center; padding: 60px 20px; background: #f8fafc; border-radius: 24px; border: 2px dashed var(--border-color); margin-top: 40px; }
.empty-icon { font-size: 4rem; margin-bottom: 20px; }
.empty-state-modern h3 { font-size: 1.5rem; margin-bottom: 10px; }
.empty-state-modern p { color: var(--text-muted); margin-bottom: 20px; }

/* School Card Enhancements for this page */
.school-card-filieres { margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border-color); }
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
