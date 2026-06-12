<?php
require "config/DataBase.php";

// Get real stats with safety
$schoolCount = 0; $cityCount = 0; $typeCount = 0;
try {
    $schoolCount = $pdo->query("SELECT COUNT(*) FROM institutions")->fetchColumn();
    $cityCount = $pdo->query("SELECT COUNT(DISTINCT ville_id) FROM institutions")->fetchColumn();
    if ($cityCount == 0) $cityCount = $pdo->query("SELECT COUNT(DISTINCT city) FROM institutions")->fetchColumn();
    $typeCount = $pdo->query("SELECT COUNT(DISTINCT type) FROM institutions")->fetchColumn();
} catch (Exception $e) {}


require "includes/lang_helper.php";

$pageTitle = __("home");
require "includes/header.php";
?>

<div class="hero hero-slider" id="heroSlider">
    <?php
    // Fetch 5 popular institutions for the slider, ensuring Solicode is included
    $sliderImages = [];
    try {
        // Fetch Solicode specifically
        $solicodeStmt = $pdo->query("SELECT image FROM institutions WHERE name LIKE '%solicode%' AND image IS NOT NULL AND image != '' LIMIT 1");
        if ($solicodeStmt) {
            $solicodeImg = $solicodeStmt->fetchColumn();
            if ($solicodeImg) $sliderImages[] = $solicodeImg;
        }
        
        // Fetch remaining popular institutions
        $limit = 5 - count($sliderImages);
        $popularStmt = $pdo->query("SELECT image FROM institutions WHERE is_popular = 1 AND name NOT LIKE '%solicode%' AND image IS NOT NULL AND image != '' LIMIT $limit");
        if ($popularStmt) {
            $popularImages = $popularStmt->fetchAll(PDO::FETCH_COLUMN);
            $sliderImages = array_merge($sliderImages, $popularImages);
        }
        
        // Fallback to random if we don't have 5
        if (count($sliderImages) < 5) {
            $needed = 5 - count($sliderImages);
            $moreStmt = $pdo->query("SELECT image FROM institutions WHERE name NOT LIKE '%solicode%' AND image IS NOT NULL AND image != '' ORDER BY RAND() LIMIT $needed");
            if ($moreStmt) {
                $moreImages = $moreStmt->fetchAll(PDO::FETCH_COLUMN);
                $sliderImages = array_merge($sliderImages, $moreImages);
            }
        }
    } catch (Exception $e) {}
    
    // Add slide elements
    foreach ($sliderImages as $index => $img) {
        $activeClass = $index === 0 ? 'active' : '';
        echo "<div class='hero-slide $activeClass' style='background-image: url(\"{$base}assets/images/institutions/" . htmlspecialchars($img) . "\");'></div>";
    }
    ?>
    <div class="hero-overlay"></div>
    
    <div class="hero-content">
        <div class="hero-badge"><?php echo __('hero_badge'); ?></div>
        <h1 class="text-gradient"><?php echo __('hero_title'); ?></h1>
        <p><?php echo __('hero_subtitle'); ?></p>
        <div class="hero-buttons">
            <a href="views/institutions.php" class="btn btn-hero btn-hero-primary"><?php echo __('cta_schools'); ?></a>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="views/register.php" class="btn btn-hero btn-hero-secondary"><?php echo __('cta_register'); ?></a>
            <?php else: ?>
                <a href="views/dashboard.php" class="btn btn-hero btn-hero-secondary"><?php echo __('cta_dashboard'); ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.hero-slider {
    position: relative;
    overflow: hidden;
    background: #0f172a; /* Fallback dark background */
}
.hero-slide {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transition: opacity 1.5s ease-in-out, transform 6s linear;
    z-index: 1;
    transform: scale(1.05);
}
.hero-slide.active {
    opacity: 1;
    transform: scale(1);
}
.hero-overlay {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    /* Create a dark gradient overlay matching the UI */
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.65) 0%, rgba(30, 58, 138, 0.45) 100%);
    z-index: 2;
}
.hero-content {
    position: relative;
    z-index: 3;
}
.hero-content h1.text-gradient {
    background: linear-gradient(135deg, #ffffff 30%, var(--accent) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.85));
}
.hero-content p {
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.9);
    font-weight: 500;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.hero-slide');
    if (slides.length <= 1) return;
    
    let currentSlide = 0;
    setInterval(() => {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add('active');
    }, 5000); // Switch every 5 seconds
});
</script>


<div class="stats-section">
    <div class="stats-container">
        <div class="stat-card stagger-1">
            <div class="stat-icon">🏫</div>
            <div class="stat-info">
                <div class="stat-number"><?php echo $schoolCount; ?>+</div>
                <div class="stat-label"><?php echo __('institutions'); ?></div>
            </div>
        </div>
        <div class="stat-card stagger-2">
            <div class="stat-icon">📍</div>
            <div class="stat-info">
                <div class="stat-number"><?php echo $cityCount; ?></div>
                <div class="stat-label"><?php echo __('cities_covered'); ?></div>
            </div>
        </div>
        <div class="stat-card stagger-3">
            <div class="stat-icon">📚</div>
            <div class="stat-info">
                <div class="stat-number"><?php echo $typeCount; ?></div>
                <div class="stat-label"><?php echo __('formation_types'); ?></div>
            </div>
        </div>
    </div>
</div>

<section class="popular-section">
    <h2 class="section-title"><?php echo __('popular_schools'); ?></h2>
    <div class="cards-grid">
        <?php
        // Check if is_popular column exists to avoid fatal error
        $columnExists = false;
        try {
            $pdo->query("SELECT is_popular FROM institutions LIMIT 1");
            $columnExists = true;
        } catch (Exception $e) {
            $columnExists = false;
        }

        if ($columnExists) {
            $popular = $pdo->query("SELECT * FROM institutions WHERE is_popular = 1 LIMIT 3")->fetchAll();
        } else {
            // Fallback if migration hasn't run
            $popular = $pdo->query("SELECT * FROM institutions LIMIT 3")->fetchAll();
        }

        if (empty($popular)): ?>
            <p style="text-align:center; color:var(--text-muted); width:100%;"><?php echo __('no_schools'); ?></p>
        <?php else:
            foreach ($popular as $school):
                $schoolName = getLocalizedDbField($school, 'name');
                $schoolCity = getLocalizedDbField($school, 'city');
                $schoolTypeKey = 'type_' . strtolower($school['type']);
                $schoolType = __($schoolTypeKey) !== $schoolTypeKey ? __($schoolTypeKey) : $school['type'];
        ?>
        <div class="card hover-lift">
            <img src="assets/images/institutions/<?php echo $school['image'] ?? 'default_school.jpg'; ?>" class="card-img" alt="<?php echo htmlspecialchars($schoolName); ?>">
            <div class="card-body">
                <div class="card-tag"><?php echo htmlspecialchars($schoolType); ?></div>
                <h3><?php echo htmlspecialchars($schoolName); ?></h3>
                <p class="school-location">📍 <?php echo htmlspecialchars($schoolCity ?: __('morocco')); ?></p>
                <div class="card-footer">
                    <span class="seuil"><?php echo __('seuil'); ?>: <strong><?php echo $school['seuil'] ?? '--'; ?></strong></span>
                    <a href="views/institution_detail.php?id=<?php echo $school['id']; ?>" class="btn-link"><?php echo __('details_arrow'); ?></a>
                </div>
            </div>
        </div>
        <?php endforeach; 
        endif; ?>
    </div>
</section>



<?php require "includes/footer.php"; ?>
