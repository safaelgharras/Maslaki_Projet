<?php
session_start();
require "../config/DataBase.php";
require_once "../includes/lang_helper.php";

$filiereId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($filiereId <= 0) {
    header("Location: orientation_explore.php");
    exit();
}

// Fetch filiere info
$stmt = $pdo->prepare("SELECT f.*, c.nom as category_name, c.nom_ar as category_name_ar, c.nom_en as category_name_en 
                       FROM filieres f 
                       LEFT JOIN categories c ON f.categorie_id = c.id 
                       WHERE f.id = ?");
$stmt->execute([$filiereId]);
$filiere = $stmt->fetch();

if (!$filiere) {
    header("Location: orientation_explore.php");
    exit();
}

// Localize
$filiere['nom'] = getLocalizedDbField($filiere, 'nom');
$filiere['description'] = getLocalizedDbField($filiere, 'description');
$filiere['category_name'] = getLocalizedDbField($filiere, 'category_name');

$pageTitle = $filiere['nom'];
$base = "../";
require "../includes/header.php";

// Fetch cities for filtering
$villes = $pdo->query("SELECT * FROM villes ORDER BY nom ASC")->fetchAll();
foreach ($villes as &$v) {
    $v['nom'] = getLocalizedDbField($v, 'nom');
}
unset($v);

$heroBg = '../assets/images/cs_hero_bg.png';
$useCustomBg = false;
$filiereLower = strtolower($filiere['nom_en']);
if (str_contains($filiereLower, 'computer') || str_contains($filiereLower, 'informatique') || str_contains($filiereLower, 'software')) {
    $useCustomBg = true;
}
?>

<div class="filiere-details-page">
    <div class="filiere-hero" style="<?php echo $useCustomBg ? "background-image: url('$heroBg'); background-size: cover; background-position: center;" : ""; ?>">
        <div class="container">
            <div class="hero-path">
                <a href="orientation_explore.php"><?php echo __("orientation"); ?></a> / 
                <span><?php echo htmlspecialchars($filiere['category_name']); ?></span>
            </div>
            <h1 class="filiere-title animate-title"><?php echo htmlspecialchars($filiere['nom']); ?></h1>
            <p class="filiere-description">
                <?php echo !empty($filiere['description']) ? htmlspecialchars($filiere['description']) : (__('discover_schools_in') . ' ' . htmlspecialchars($filiere['nom'])); ?>
            </p>
            <div class="filiere-stats">
                <span id="schoolCountBadge" class="stat-badge"><span class="pulse"></span> <?php echo __('loading'); ?></span>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="filter-bar-modern glassmorphism">
            <div class="search-box">
                <input type="text" id="instantSearch" placeholder="<?php echo __('search_placeholder'); ?>...">
                <span class="search-icon">🔍</span>
            </div>
            <div class="filter-group">
                <select id="filterCity" class="minimal-select">
                    <option value=""><?php echo __("all_cities"); ?></option>
                    <?php foreach($villes as $v): ?>
                        <option value="<?php echo $v['id']; ?>"><?php echo htmlspecialchars($v['nom']); ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="filterType" class="minimal-select">
                    <option value=""><?php echo __('all_types'); ?></option>
                    <option value="Public"><?php echo __('type_public'); ?></option>
                    <option value="Private"><?php echo __('type_private'); ?></option>
                </select>
            </div>
        </div>

        <div id="resultsGrid" class="premium-grid">
            <!-- Loaded via AJAX -->
            <div class="loading-state">
                <div class="loading-spinner"></div>
            </div>
        </div>

        <div id="emptyState" class="empty-state-modern" style="display:none;">
            <div class="empty-illustration">🏜️</div>
            <h3><?php echo __('no_school_found'); ?></h3>
            <p><?php echo __('try_modify_filters'); ?></p>
            <button onclick="resetFilters()" class="btn btn-outline"><?php echo __('reset'); ?></button>
        </div>
    </div>
</div>

<style>
.filiere-details-page { padding-bottom: 120px; background: #fdfdfd; }
.filiere-hero { 
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); 
    color: white; 
    padding: 140px 0 200px; 
    margin-bottom: -100px; 
    border-radius: 0 0 80px 80px; 
    position: relative;
    overflow: hidden;
}
.filiere-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.7); /* Dark overlay */
    z-index: 1;
}
.filiere-hero .container {
    position: relative;
    z-index: 2;
}
.filiere-hero::after {
    content: '';
    position: absolute;
    top: -50%; right: -20%;
    width: 80%; height: 200%;
    background: radial-gradient(circle, rgba(var(--primary-rgb), 0.1) 0%, transparent 70%);
    pointer-events: none;
    filter: blur(80px);
}
.hero-path { 
    opacity: 0.6; 
    font-size: 0.9rem; 
    letter-spacing: 2px; 
    margin-bottom: 35px; 
    font-weight: 700; 
    text-transform: uppercase;
}
.hero-path a { color: white; text-decoration: none; }
.filiere-title { 
    font-size: clamp(3rem, 6vw, 5rem); 
    font-weight: 950; 
    margin-bottom: 30px; 
    letter-spacing: -3px;
    line-height: 1;
    background: linear-gradient(to bottom, #ffffff 0%, #cbd5e1 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: slideUp 0.8s cubic-bezier(0.23, 1, 0.32, 1);
}
.filiere-description { font-size: 1.4rem; opacity: 0.8; max-width: 850px; line-height: 1.7; margin-bottom: 45px; font-weight: 400; }

.filter-bar-modern {
    background: rgba(255, 255, 255, 0.8);
    padding: 35px 50px;
    border-radius: 40px;
    box-shadow: 0 40px 100px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 40px;
    margin-bottom: 80px;
    position: relative;
    z-index: 10;
    border: 1px solid rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(30px) saturate(180%);
}
.search-box { flex: 1.5; position: relative; }
.search-box input { 
    width: 100%; 
    padding: 20px 60px 20px 30px; 
    border-radius: 20px; 
    border: 1.5px solid #e2e8f0; 
    font-weight: 700; 
    font-size: 1.1rem;
    background: #f8fafc;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.search-box input:focus { 
    border-color: var(--primary); 
    background: white;
    transform: scale(1.02);
    box-shadow: 0 15px 35px rgba(var(--primary-rgb), 0.1); 
}
.search-icon { position: absolute; right: 25px; top: 50%; transform: translateY(-50%); font-size: 1.3rem; opacity: 0.3; }

.filter-group { display: flex; gap: 20px; }
.minimal-select { 
    padding: 15px 30px; 
    border-radius: 18px; 
    border: 1.5px solid #e2e8f0; 
    font-weight: 700; 
    background: #f8fafc;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
}
.minimal-select:hover { border-color: #cbd5e1; background: white; transform: translateY(-3px); }

.premium-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 50px; }

/* Premium School Card */
.school-card-v2 {
    background: white;
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 1px solid rgba(0,0,0,0.03);
    position: relative;
}
.school-card-v2:hover { transform: translateY(-12px); box-shadow: 0 40px 70px rgba(0,0,0,0.12); }
.card-v2-img-wrapper { height: 220px; overflow: hidden; position: relative; }
.card-v2-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s; }
.school-card-v2:hover .card-v2-img { transform: scale(1.15); }

.card-v2-content { padding: 30px; }
.card-v2-tag { 
    font-size: 0.75rem; 
    font-weight: 800; 
    text-transform: uppercase; 
    color: var(--primary); 
    background: rgba(var(--primary-rgb), 0.1); 
    padding: 6px 14px; 
    border-radius: 8px; 
    display: inline-block; 
    margin-bottom: 15px; 
}
.card-v2-title { font-size: 1.4rem; font-weight: 850; margin-bottom: 12px; color: #1e293b; line-height: 1.3; }
.card-v2-loc { color: #64748b; font-size: 0.95rem; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
.card-v2-desc { font-size: 0.95rem; color: #475569; line-height: 1.6; margin-bottom: 25px; }

.card-v2-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 20px; border-top: 1px solid #f1f5f9; }
.btn-v2-details { 
    padding: 10px 24px; 
    background: #1e293b; 
    color: white; 
    border-radius: 12px; 
    text-decoration: none; 
    font-weight: 700; 
    font-size: 0.9rem; 
    transition: all 0.3s; 
}
.btn-v2-details:hover { background: var(--primary); transform: translateX(5px); }

@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

.loading-state { grid-column: 1/-1; text-align: center; padding: 100px 0; }
.stat-badge { 
    background: rgba(255,255,255,0.08); 
    padding: 14px 35px; 
    border-radius: 100px; 
    font-weight: 800; 
    font-size: 1.1rem; 
    border: 1px solid rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
}
.pulse { display: inline-block; width: 10px; height: 10px; background: #10b981; border-radius: 50%; margin-right: 12px; animation: pulse 1.5s infinite; }
@keyframes pulse { 0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); } 70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); } 100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); } }

@media (max-width: 992px) {
    .filter-bar-modern { flex-direction: column; padding: 40px; }
    .filter-group { width: 100%; }
    .minimal-select { flex: 1; }
}

[data-theme="dark"] .filiere-details-page { background: #0f172a; }
[data-theme="dark"] .filter-bar-modern { background: #1e293b; border-color: rgba(255,255,255,0.05); }
[data-theme="dark"] .search-box input { background: #0f172a; border-color: #334155; color: white; }
[data-theme="dark"] .minimal-select { background: #0f172a; border-color: #334155; color: white; }
[data-theme="dark"] .school-card-v2 { background: #1e293b; border-color: rgba(255,255,255,0.05); }
[data-theme="dark"] .card-v2-title { color: white; }
[data-theme="dark"] .card-v2-desc { color: #94a3b8; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filiereId = <?php echo $filiereId; ?>;
    const instantSearch = document.getElementById('instantSearch');
    const filterCity = document.getElementById('filterCity');
    const filterType = document.getElementById('filterType');
    const resultsGrid = document.getElementById('resultsGrid');
    const emptyState = document.getElementById('emptyState');
    const schoolCountBadge = document.getElementById('schoolCountBadge');
    
    const translations = {
        schools_found: <?php echo json_encode(__('schools_found')); ?>,
        details: <?php echo json_encode(__('view_details')); ?>,
        no_school: <?php echo json_encode(__('no_school_found')); ?>
    };

    function updateResults() {
        const params = new URLSearchParams();
        params.set('filiere_id', filiereId);
        if (instantSearch.value) params.set('search', instantSearch.value);
        if (filterCity.value) params.set('city_id', filterCity.value);
        if (filterType.value) params.set('type', filterType.value);

        resultsGrid.innerHTML = '<div class="loading-state"><div class="loading-spinner"></div></div>';
        
        fetch(`../search_ajax.php?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    resultsGrid.style.display = 'none';
                    emptyState.style.display = 'block';
                    schoolCountBadge.innerHTML = `<span class="pulse" style="background:#ef4444"></span> ${translations.no_school}`;
                } else {
                    resultsGrid.style.display = 'grid';
                    emptyState.style.display = 'none';
                    schoolCountBadge.innerHTML = `<span class="pulse"></span> ${data.length} ${translations.schools_found}`;
                    renderSchools(data);
                }
            });
    }

    function renderSchools(schools) {
        resultsGrid.innerHTML = schools.map(inst => `
            <div class="school-card-v2 animate-card">
                <div class="card-v2-img-wrapper">
                    <img src="${resolveImage(inst)}" class="card-v2-img" alt="${inst.name}">
                </div>
                <div class="card-v2-content">
                    <div class="card-v2-tag">${translateType(inst.type)}</div>
                    <h3 class="card-v2-title">${inst.name}</h3>
                    <div class="card-v2-loc">📍 ${inst.city || 'Maroc'}</div>
                    <p class="card-v2-desc">${truncate(inst.description, 100)}</p>
                    <div class="card-v2-footer">
                        <a href="institution_detail.php?id=${inst.id}" class="btn-v2-details">${translations.details} →</a>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function resolveImage(inst) {
        if (inst.image && inst.image !== 'default_school.jpg') {
            return `../assets/images/institutions/${inst.image}`;
        }
        return `../assets/images/default_school.jpg`; 
    }

    function translateType(type) {
        // Reusing standard mapping
        return type; 
    }

    function truncate(str, n) {
        if (!str) return "";
        return (str.length > n) ? str.substr(0, n-1) + '...' : str;
    }

    function resetFilters() {
        instantSearch.value = "";
        filterCity.value = "";
        filterType.value = "";
        updateResults();
    }
    window.resetFilters = resetFilters;

    instantSearch.addEventListener('input', () => {
        clearTimeout(window.searchTimer);
        window.searchTimer = setTimeout(updateResults, 300);
    });
    filterCity.addEventListener('change', updateResults);
    filterType.addEventListener('change', updateResults);

    // Initial load
    updateResults();
});
</script>

<?php require "../includes/footer.php"; ?>
