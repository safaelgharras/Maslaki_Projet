<?php
require_once "../includes/lang_helper.php";
$pageTitle = __("explore_orientation");
require "../includes/header.php";
require "../config/DataBase.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();
foreach ($categories as &$cat) {
    $cat['nom'] = getLocalizedDbField($cat, 'nom');
}
unset($cat);
?>

<div class="container" style="margin-top: 40px;">
    <h1 class="page-title"><?php echo __('discover_future_path'); ?></h1>
    <p class="section-subtitle" style="text-align:center; margin-bottom: 50px; color: var(--text-muted);"><?php echo __('explore_domains_by_category'); ?></p>

    <div class="orientation-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; margin-bottom: 60px;">
        <?php foreach($categories as $index => $cat): ?>
            <div class="category-card stagger-<?php echo ($index % 5) + 1; ?>" onclick="showDomains(<?php echo $cat['id']; ?>, '<?php echo addslashes($cat['nom']); ?>')">
                <div class="card-content">
                    <div class="category-icon"><?php echo getCategoryEmoji($cat['id']); ?></div>
                    <h3><?php echo htmlspecialchars($cat['nom']); ?></h3>
                    <p><?php echo __('discover_streams_schools_domain'); ?></p>
                    <span class="explore-btn"><?php echo __('explore'); ?> <?php echo isRTL() ? '←' : '→'; ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Domains Modal/Overlay -->
<div id="domainsModal" class="modal-overlay" style="display:none;">
    <div class="modal-content glassmorphism">
        <div class="modal-header">
            <h2 id="modalTitle"><?php echo __('domains'); ?></h2>
            <button class="close-modal" onclick="closeModal()">×</button>
        </div>
        <div id="domainsList" class="domains-list">
            <!-- Loaded via JS -->
        </div>
    </div>
</div>

<style>
.category-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 30px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
}

.category-card:hover {
    transform: translateY(-10px);
    border-color: var(--primary);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
}

.category-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--accent));
    opacity: 0;
    transition: opacity 0.3s;
}

.category-card:hover::before {
    opacity: 1;
}

.category-icon {
    font-size: 3rem;
    margin-bottom: 20px;
}

.explore-btn {
    margin-top: 20px;
    color: var(--primary);
    font-weight: 600;
    font-size: 0.9rem;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(5px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: var(--bg-card);
    width: 90%;
    max-width: 700px;
    border-radius: 24px;
    padding: 40px;
    position: relative;
    max-height: 80vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.modal-header h2 {
    color: white;
    font-weight: 800;
}

.close-modal {
    background: none;
    border: none;
    font-size: 2rem;
    color: var(--text-muted);
    cursor: pointer;
}

.domains-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
    color: white;
}

.domain-item {
    padding: 10px 0;
    color: white;
    text-decoration: none;
    transition: all 0.2s ease;
    font-weight: 500;
    display: block;
}

.domain-item:hover {
    color: #1e3a8a; /* Navy Blue */
    transform: translateX(5px);
}

[data-theme="dark"] .category-card { background: #161e31; }
[data-theme="dark"] .modal-content { background: #1a233a; color: white; }
[data-theme="dark"] .modal-header h2 { color: white; }
[data-theme="dark"] .domain-item { color: white; }
[data-theme="dark"] .domain-item:hover { color: #3b82f6; } /* Lighter blue for dark mode hover */
</style>

<script>
function showDomains(catId, catName) {
    document.getElementById('modalTitle').textContent = catName;
    const list = document.getElementById('domainsList');
    list.innerHTML = '<div style="text-align:center; width:100%;">Chargement...</div>';
    document.getElementById('domainsModal').style.display = 'flex';

    fetch(`../get_domains.php?cat_id=${catId}`)
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                list.innerHTML = `<p>${<?php echo json_encode(__('no_domain_found')); ?>}</p>`;
                return;
            }
            list.innerHTML = data.map(d => `
                <a href="domain_details.php?id=${d.id}" class="domain-item">
                    ${d.nom}
                </a>
            `).join('');
        });
}

function closeModal() {
    document.getElementById('domainsModal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('domainsModal')) {
        closeModal();
    }
}
</script>

<?php
function getCategoryEmoji($id) {
    $emojis = [
        1 => "🔬", // Sciences Exactes
        2 => "⚙️", // Ingénierie
        3 => "🩺", // Santé
        4 => "🌿", // Agriculture
        5 => "📊", // Business
        6 => "⚖️", // Droit
        7 => "🎨", // Arts
        8 => "✈️", // Services
        9 => "📖", // Éducation
    ];
    return $emojis[$id] ?? "🎓";
}

require "../includes/footer.php";
?>
