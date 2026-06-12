<?php
require_once "../includes/lang_helper.php";
$pageTitle = __('institutions');
require "../includes/header.php";
require "../config/DataBase.php";
require_once "../includes/csrf.php";

// Get metadata for filters with safety checks
$villes = [];
$categories = [];

try {
    $villes = $pdo->query("SELECT * FROM villes ORDER BY nom ASC")->fetchAll();
    foreach ($villes as &$v) {
        $v['nom'] = getLocalizedDbField($v, 'nom');
    }
    unset($v);
    
    $categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll();
    foreach ($categories as &$c) {
        $c['nom'] = getLocalizedDbField($c, 'nom');
    }
    unset($c);
} catch (Exception $e) {
    // Filter metadata unavailable; page will render without filters.
}

$types = [];
try {
    $types = $pdo->query("SELECT DISTINCT type FROM institutions WHERE type IS NOT NULL ORDER BY type")->fetchAll(PDO::FETCH_COLUMN);
    $bac_types = $pdo->query("SELECT * FROM bac_types ORDER BY nom ASC")->fetchAll();
    foreach ($bac_types as &$bt) {
        $bt['nom'] = getLocalizedDbField($bt, 'nom');
    }
    unset($bt);
} catch (Exception $e) {}

$isLoggedIn = isset($_SESSION['user_id']);

// Initial load
$sql = "SELECT * FROM institutions";
try {
    // Try with is_popular if it exists
    $pdo->query("SELECT is_popular FROM institutions LIMIT 1");
    $sql .= " ORDER BY (id = 131) DESC, is_popular DESC, name ASC";
} catch (Exception $e) {
    $sql .= " ORDER BY (id = 131) DESC, name ASC";
}
$institutions = $pdo->query($sql)->fetchAll();
foreach ($institutions as &$inst) {
    $inst['name'] = getLocalizedDbField($inst, 'name');
    $inst['description'] = getLocalizedDbField($inst, 'description');
    $inst['city'] = getLocalizedDbField($inst, 'city');
    $inst['diplome'] = getLocalizedDbField($inst, 'diplome');
    $inst['duree_etudes'] = getLocalizedDbField($inst, 'duree_etudes');
}
unset($inst);


// Get saved IDs
$savedIds = [];
if ($isLoggedIn) {
    $savedIds = $pdo->query("SELECT institution_id FROM saved_schools WHERE student_id = " . $_SESSION['user_id'])->fetchAll(PDO::FETCH_COLUMN);
}

function resolveInstitutionImagePath($institutionName, $dbImage = null) {
    $name = trim((string) ($institutionName ?? ''));
    $normalizedName = strtolower($name);

    $candidates = [];
    if (!empty($dbImage)) {
        $candidates[] = (string) $dbImage;
    }

    // Special cases
    if ($normalizedName === 'cpge fes' || $normalizedName === 'cpge fez') {
        $candidates[] = 'CPGE Fez.jpg';
    } elseif ($normalizedName === 'emsi casablanca') {
        $candidates[] = 'EMSI Casablanca.webp';
    } elseif ($normalizedName === 'eigsi casablanca') {
        $candidates[] = 'EIGSI Casablanca.webp';
    } elseif ($normalizedName === 'esca ecole de management') {
        $candidates[] = 'ESCA Ecole de Management Casablanca.webp';
    }

    $candidates[] = $name . '.webp';
    $candidates[] = $name . '.png';
    $candidates[] = $name . '.jpg';
    $candidates[] = 'default_school.jpg';

    foreach ($candidates as $candidate) {
        $candidate = trim((string) $candidate);
        if ($candidate === '') continue;

        // Check in assets/images/
        if (file_exists(__DIR__ . '/../assets/images/' . $candidate)) {
            return '../assets/images/' . str_replace(' ', '%20', $candidate);
        }
        // Check in assets/images/institutions/
        if (file_exists(__DIR__ . '/../assets/images/institutions/' . $candidate)) {
            return '../assets/images/institutions/' . str_replace(' ', '%20', $candidate);
        }
    }

    return '../assets/images/default_school.jpg';
}

function translateType($type) {
    $key = 'type_' . strtolower($type);
    $translated = __($key);
    // If translation not found, fallback to original
    if ($translated === $key) {
        return $type;
    }
    return $translated;
}
?>

<div class="institutions-layout">
    <!-- Filter Sidebar -->

    <aside class="filter-sidebar">
        <div class="sidebar-header">
            <h3><?php echo __('filters'); ?></h3>
        </div>

        <div class="filter-group">
            <label><?php echo __('quick_search'); ?></label>
            <input type="text" id="searchInput" placeholder="<?php echo __('search_placeholder'); ?>" class="search-input">
        </div>

        <div class="filter-group">
            <label>📍 <?php echo __('city'); ?></label>
            <select id="filterCity" class="filter-select">
                <option value=""><?php echo __('all_cities'); ?></option>
                <?php foreach($villes as $v): ?>
                    <option value="<?php echo $v['id']; ?>"><?php echo htmlspecialchars($v['nom']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label>📚 <?php echo __('category'); ?></label>
            <select id="filterCategory" class="filter-select">
                <option value=""><?php echo __('all_categories'); ?></option>
                <?php foreach($categories as $c): ?>
                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nom']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group" id="domainGroup" style="display:none;">
            <label>🔍 <?php echo __('domain'); ?></label>
            <select id="filterDomain" class="filter-select">
                <option value=""><?php echo __('all_categories'); ?></option>
            </select>
        </div>

        <div class="filter-group">
            <label>🎓 <?php echo __('bac_type'); ?></label>
            <select id="filterBac" class="filter-select">
                <option value=""><?php echo __('all_bac_types'); ?></option>
                <?php foreach($bac_types as $bt): ?>
                    <option value="<?php echo $bt['id']; ?>"><?php echo htmlspecialchars($bt['nom']); ?> (<?php echo $bt['code']; ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label>🏢 <?php echo __('type'); ?></label>
            <select id="filterType" class="filter-select">
                <option value=""><?php echo __('all_types'); ?></option>
                <?php foreach($types as $t): ?>
                    <option value="<?php echo htmlspecialchars($t); ?>"><?php echo htmlspecialchars(__('type_' . strtolower($t))); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button id="resetFilters" class="btn btn-outline btn-full" style="margin-top: 20px; border-color: var(--border-color); color: var(--text-muted);">
            <?php echo __('reset_filters'); ?>
        </button>
    </aside>


    <!-- Results Main -->
    <main class="results-main">
        <div class="results-header" style="display: block; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <h1 class="page-title" style="margin-bottom: 5px; border-bottom: none; padding-bottom: 0;"><?php echo __('find_school'); ?></h1>
                    <div style="width: 60px; height: 3px; background: var(--orange); margin-top: 8px; margin-bottom: 8px;"></div>
                    <p id="resultsCount" class="results-count" style="margin-bottom: 0;"><?php echo count($institutions); ?> <?php echo __('schools_found'); ?></p>
                </div>
            </div>
        </div>

        <div class="cards-grid" id="resultsGrid">
            <?php foreach($institutions as $inst): ?>
                <?php
                    $imagePath = resolveInstitutionImagePath($inst['name'] ?? '', $inst['image'] ?? null);
                ?>
                <div class="card">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" class="card-img" alt="<?php echo htmlspecialchars($inst['name']); ?>">
                    <div class="card-body">
                        <div class="badge"><?php echo htmlspecialchars(__('type_' . strtolower($inst['type']))); ?></div>
                        <h3><?php echo htmlspecialchars($inst['name']); ?></h3>
                        <p class="school-location">📍 <?php echo htmlspecialchars($inst['city'] ?? __('morocco')); ?></p>
                        
                        <div class="card-info-row">
                            <span class="info-item">🎓 <?php echo htmlspecialchars($inst['diplome'] ?? __('diploma')); ?></span>
                            <span class="info-item">⏳ <?php echo htmlspecialchars($inst['duree_etudes'] ?? '--'); ?></span>
                        </div>

                        <div class="card-footer">
                            <span class="seuil"><?php echo __('seuil'); ?>: <strong><?php echo $inst['seuil'] ?? $inst['min_average'] ?? '--'; ?></strong></span>
                            <div class="card-actions">
                                <a href="institution_detail.php?id=<?php echo $inst['id']; ?>" class="btn-link"><?php echo __('details_arrow'); ?></a>
                                <?php if ($isLoggedIn): ?>
                                    <button class="btn-icon-save <?php echo in_array($inst['id'], $savedIds) ? 'active' : ''; ?>" 
                                            onclick="toggleSave(<?php echo $inst['id']; ?>, this)">
                                        ❤
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>

<style>
.institutions-layout { display: grid; grid-template-columns: 320px 1fr; gap: 40px; padding: 40px 0; align-items: start; }
.filter-sidebar { background: var(--white); padding: 30px; border-radius: 24px; box-shadow: var(--shadow-md); height: fit-content; position: sticky; top: 100px; border: 1px solid var(--border-color); max-height: calc(100vh - 140px); overflow-y: auto; }
.sidebar-header h3 { font-size: 1.3rem; font-weight: 800; color: var(--primary); margin-bottom: 25px; border-bottom: 2px solid var(--bg-light); padding-bottom: 15px; }

.filter-group { margin-bottom: 25px; }
.filter-group label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
.filter-select, .search-input { width: 100%; padding: 14px 18px; border-radius: 12px; border: 1.5px solid var(--border-color); background: #f8fafc; font-size: 0.95rem; transition: var(--transition); color: var(--text-dark); }
.filter-select:focus, .search-input:focus { border-color: var(--primary); background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.1); }

.results-header { margin-bottom: 30px; }
.results-header h2 { font-size: 1.8rem; font-weight: 800; color: var(--primary-dark); }
.results-count { color: var(--text-muted); font-weight: 600; font-size: 0.95rem; }

.cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px; }
.institutions-layout .card-img {
    height: 240px;
    object-fit: cover;
    object-position: center;
    background: transparent;
    padding: 0;
}
.institutions-layout .card:hover .card-img {
    transform: scale(1.03);
}

@media (max-width: 992px) {
    .institutions-layout { grid-template-columns: 1fr; }
    .filter-sidebar { position: static; margin-bottom: 30px; max-height: none; overflow-y: visible; }
}

.card-info-row {
    display: flex;
    gap: 12px;
    margin: 10px 0;
    font-size: 0.8rem;
    color: var(--text-muted);
}

.btn-icon-save {
    background: none;
    border: 1px solid var(--border-color);
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    color: #cbd5e1;
}

.btn-icon-save.active {
    background: #fff1f2;
    border-color: #fda4af;
    color: #e11d48;
}

.tag-chip {
    display: none;
}

[data-theme="dark"] .search-input,
[data-theme="dark"] .filter-select {
    background: #0b1121;
    color: var(--text-dark);
}

@media (max-width: 992px) {
    .institutions-layout {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
const langTranslations = {
    schools_found: <?php echo json_encode(__('schools_found')); ?>,
    no_institutions_criteria: <?php echo json_encode(__('no_institutions_criteria')); ?>,
    diploma: <?php echo json_encode(__('diploma')); ?>,
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
    type_digital: <?php echo json_encode(__('type_digital')); ?>,
    type_art: <?php echo json_encode(__('type_art')); ?>,
    type_management: <?php echo json_encode(__('type_management')); ?>,
    type_medical: <?php echo json_encode(__('type_medical')); ?>,
    morocco: <?php echo json_encode(__('morocco')); ?>
};

const searchInput = document.getElementById('searchInput');
const filterCity = document.getElementById('filterCity');
const filterCategory = document.getElementById('filterCategory');
const filterDomain = document.getElementById('filterDomain');
const domainGroup = document.getElementById('domainGroup');
const filterBac = document.getElementById('filterBac');
const filterType = document.getElementById('filterType');
const resultsGrid = document.getElementById('resultsGrid');
const resultsCount = document.getElementById('resultsCount');
const resetBtn = document.getElementById('resetFilters');

let debounceTimer;

const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
let savedIds = <?php echo json_encode($savedIds); ?>;
const csrfToken = <?php echo json_encode(csrf_token()); ?>;

function doSearch() {
    const params = new URLSearchParams();
    let searchVal = searchInput.value;

    if (searchVal.trim()) params.set('search', searchVal.trim());
    if (filterCity.value) params.set('city_id', filterCity.value);
    if (filterCategory.value) params.set('cat_id', filterCategory.value);
    if (filterDomain.value) params.set('domain_id', filterDomain.value);
    if (filterBac.value) params.set('bac_id', filterBac.value);
    if (filterType.value && !params.has('type')) params.set('type', filterType.value);

    // If we came from orientation_explore.php directly to a filiere
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('filiere_id') && !filterDomain.value && !filterCategory.value) {
        params.set('filiere_id', urlParams.get('filiere_id'));
    }

    fetch('../search_ajax.php?' + params.toString())
        .then(res => res.json())
        .then(data => {
            resultsCount.textContent = data.length + ' ' + langTranslations.schools_found;
            renderResults(data);
        });
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
        'University': langTranslations.type_university,
        'Digital': langTranslations.type_digital,
        'Art': langTranslations.type_art,
        'Management': langTranslations.type_management,
        'Medical': langTranslations.type_medical
    };
    return map[type] || type;
}

function renderResults(data) {
    if (data.length === 0) {
        resultsGrid.innerHTML = `<div class="empty-state">${langTranslations.no_institutions_criteria}</div>`;
        return;
    }

    resultsGrid.innerHTML = data.map(inst => {
        const isSaved = savedIds.includes(inst.id.toString()) || savedIds.includes(parseInt(inst.id));
        const cardImageSrc = resolveCardImage(inst);
        return `
            <div class="card">
                <img src="${cardImageSrc}" class="card-img" alt="${inst.name}">
                <div class="card-body">
                    <div class="badge">${translateType(inst.type)}</div>
                    <h3>${inst.name}</h3>
                    <p class="school-location">📍 ${inst.city || langTranslations.morocco}</p>
                    <div class="card-info-row">
                        <span>🎓 ${inst.diplome || langTranslations.diploma}</span>
                        <span>⏳ ${inst.duree_etudes || '--'}</span>
                    </div>
                    <div class="card-footer">
                        <span class="seuil">${langTranslations.seuil}: <strong>${inst.seuil || inst.min_average || '--'}</strong></span>
                        <div class="card-actions">
                            <a href="institution_detail.php?id=${inst.id}" class="btn-link">${langTranslations.details_arrow}</a>
                            ${isLoggedIn ? `
                                <button class="btn-icon-save ${isSaved ? 'active' : ''}" 
                                        onclick="toggleSave(${inst.id}, this)">
                                    ❤
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function resolveCardImage(inst) {
    const name = (inst.name || '').trim();
    const normalizedName = name.toLowerCase();

    // Map of known images in the institutions/ subfolder
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
        // If the DB image has a path, don't double up
        if (filename.includes('/')) return `../assets/images/${filename}`;
    } else {
        return '../assets/images/default_school.jpg';
    }

    // URL encode spaces for browser safety
    const safeFilename = filename.replace(/ /g, '%20');
    return `../assets/images/${folder}${safeFilename}`;
}



searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(doSearch, 300);
});

[filterCity, filterCategory, filterDomain, filterBac, filterType].forEach(el => {
    if (el) el.addEventListener('change', doSearch);
});

filterCategory.addEventListener('change', function() {
    const catId = this.value;
    if (catId) {
        fetch('../get_domains.php?cat_id=' + catId)
            .then(res => res.json())
            .then(data => {
                filterDomain.innerHTML = '<option value="">' + <?php echo json_encode(__('all_categories')); ?> + '</option>';
                data.forEach(d => {
                    filterDomain.innerHTML += `<option value="${d.id}">${d.nom}</option>`;
                });
                domainGroup.style.display = 'block';
            });
    } else {
        domainGroup.style.display = 'none';
        filterDomain.value = '';
    }
});

// Initialize from URL
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('cat_id')) {
    filterCategory.value = urlParams.get('cat_id');
    // Trigger category change to load domains
    const event = new Event('change');
    filterCategory.dispatchEvent(event);
    
    // If domain_id is also present, we need to wait for domains to load
    if (urlParams.has('domain_id')) {
        const domainId = urlParams.get('domain_id');
        setTimeout(() => {
            filterDomain.value = domainId;
            doSearch();
        }, 500);
    } else {
        doSearch();
    }
} else if (urlParams.has('filiere_id')) {
    // If we have a filiere_id, we need to pass it to doSearch
    // We can use a global variable or just rely on the fact that doSearch reads URL params if we modify it
    doSearch();
} else {
    doSearch();
}

if (resetBtn) {
    resetBtn.addEventListener('click', () => {
        searchInput.value = '';
        filterCity.value = '';
        filterCategory.value = '';
        filterDomain.value = '';
        domainGroup.style.display = 'none';
        filterBac.value = '';
        filterType.value = '';
        
        doSearch();
    });
}
</script>

<?php require "../includes/footer.php"; ?>
