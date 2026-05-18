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

<div class="container" style="margin-top: 40px; padding-bottom: 80px;">
    <h1 class="page-title" style="text-align:center; font-weight: 850; font-size: 2.8rem; margin-bottom: 10px; color: var(--primary);"><?php echo __('discover_future_path'); ?></h1>
    <div style="width: 80px; height: 4px; background: var(--orange); margin: 0 auto 20px auto; border-radius: 2px;"></div>
    <p class="section-subtitle" style="text-align:center; margin-bottom: 60px; font-size: 1.2rem; color: var(--text-muted); font-weight: 500;"><?php echo __('explore_domains_by_category'); ?></p>

    <div class="orientation-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; margin-bottom: 60px;">
        <?php foreach($categories as $index => $cat): ?>
            <div class="category-card stagger-<?php echo ($index % 5) + 1; ?>" onclick="location.href='domain_details.php?id=<?php echo $cat['id']; ?>'">
                <div class="card-content">
                    <div class="category-icon"><?php echo getCategoryEmoji($cat['id']); ?></div>
                    <h3><?php echo htmlspecialchars($cat['nom']); ?></h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.5; margin: 15px 0 20px 0;"><?php echo __('discover_streams_schools_domain'); ?></p>
                    <span class="explore-btn"><?php echo __('explore'); ?> <?php echo isRTL() ? '←' : '→'; ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.category-card {
    background: var(--white);
    border: 1px solid var(--border-color);
    border-radius: 24px;
    padding: 40px 30px;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    text-align: center;
    box-shadow: var(--shadow-sm);
}

.category-card:hover {
    transform: translateY(-8px);
    border-color: var(--primary);
    box-shadow: 0 20px 40px rgba(30, 58, 138, 0.08);
}

.category-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--orange), var(--primary));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.category-card:hover::before {
    opacity: 1;
}

.category-icon {
    font-size: 3.5rem;
    margin-bottom: 15px;
    transition: transform 0.3s ease;
}

.category-card:hover .category-icon {
    transform: scale(1.1) rotate(5deg);
}

.category-card h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary);
    margin: 10px 0 0 0;
    line-height: 1.3;
}

.explore-btn {
    display: inline-block;
    padding: 8px 20px;
    background: rgba(var(--primary-rgb), 0.05);
    color: var(--primary);
    font-weight: 700;
    font-size: 0.85rem;
    border-radius: 30px;
    transition: all 0.3s ease;
}

.category-card:hover .explore-btn {
    background: var(--primary);
    color: #fff;
    transform: translateY(-2px);
}

[data-theme="dark"] .category-card { 
    background: #161e31; 
    border-color: #242f49;
}
[data-theme="dark"] .explore-btn {
    background: rgba(255,255,255,0.05);
    color: #fff;
}
[data-theme="dark"] .category-card:hover .explore-btn {
    background: var(--accent);
    color: #fff;
}
</style>

<?php
function getCategoryEmoji($id) {
    $emojis = [
        1 => "🔬", // Sciences Exactes & Technologies
        2 => "⚙️", // Ingénierie & Industrie
        3 => "🩺", // Santé & Sciences de la Vie
        4 => "🌱", // Agriculture & Environnement
        5 => "📈", // Business, Gestion & Finance
        6 => "⚖️", // Droit, Politique & Société
        7 => "🎨", // Arts, Design & Médias
        8 => "✈️", // Services, Tourisme & Transport
        9 => "📚", // Éducation & Sciences Humaines
        10 => "🛠️", // Formation Professionnelle & Métiers
    ];
    return $emojis[$id] ?? "🎓";
}

require "../includes/footer.php";
?>
