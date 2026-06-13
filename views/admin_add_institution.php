<?php
require_once "../includes/lang_helper.php";
require "../config/DataBase.php";
require_once "../includes/platform_admin.php";
require_once "../includes/csrf.php";
require_platform_admin($pdo);

$pageTitle = __('admin_add_institution_title');
require '../includes/header.php';

// Fetch villes for dropdown
$villes = [];
try {
    $villes = $pdo->query("SELECT id, nom, nom_ar, nom_en FROM villes ORDER BY nom ASC")->fetchAll();
} catch (Exception $e) {}

// Institution types
$types = ['Engineering', 'Business', 'Science', 'Technical', 'Preparatory', 'Private', 'Education', 'University', 'Digital', 'Art', 'Management', 'Medical'];
?>

<div class="admin-form-container">
    <header class="admin-form-header">
        <h1><?php echo __('admin_add_institution_title'); ?></h1>
        <p><?php echo __('admin_add_institution_desc'); ?></p>
    </header>

    <?php if (isset($_GET['error'])): ?>
        <div class="admin-msg admin-msg-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <div class="admin-msg admin-msg-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>

    <form method="POST" action="../process_add_institution.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

        <!-- Section: Basic Info -->
        <fieldset class="admin-fieldset">
            <legend><?php echo __('section_basic_info'); ?></legend>

            <div class="form-row">
                <div class="form-group">
                    <label><?php echo __('inst_name_fr'); ?> *</label>
                    <input type="text" name="name" required placeholder="Ex: ENSA Casablanca">
                </div>
            </div>

            <div class="form-row form-row-2col">
                <div class="form-group">
                    <label>🏙️ <?php echo __('city'); ?> *</label>
                    <select name="ville_id" required>
                        <option value=""><?php echo __('inst_city_select'); ?></option>
                        <?php foreach ($villes as $v): ?>
                            <option value="<?php echo $v['id']; ?>"><?php echo htmlspecialchars($v['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>🏢 <?php echo __('inst_type_label'); ?> *</label>
                    <select name="type" required>
                        <option value=""><?php echo __('type'); ?></option>
                        <?php foreach ($types as $t): ?>
                            <option value="<?php echo $t; ?>"><?php echo htmlspecialchars(__('type_' . strtolower($t))); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row form-row-2col">
                <div class="form-group">
                    <label>📊 <?php echo __('inst_seuil'); ?></label>
                    <input type="number" step="0.01" name="seuil" placeholder="12.00" min="0" max="20">
                </div>
                <div class="form-group">
                    <label>📉 <?php echo __('inst_min_average'); ?></label>
                    <input type="number" step="0.01" name="min_average" placeholder="10.00" min="0" max="20">
                </div>
            </div>
        </fieldset>

        <!-- Section: Translations -->
        <fieldset class="admin-fieldset">
            <legend><?php echo __('section_translations'); ?></legend>

            <div class="form-row form-row-2col">
                <div class="form-group">
                    <label><?php echo __('inst_name_ar'); ?></label>
                    <input type="text" name="name_ar" dir="rtl" placeholder="المدرسة الوطنية...">
                </div>
                <div class="form-group">
                    <label><?php echo __('inst_name_en'); ?></label>
                    <input type="text" name="name_en" placeholder="National School of...">
                </div>
            </div>

            <div class="form-group">
                <label><?php echo __('inst_description_fr'); ?></label>
                <textarea name="description" rows="3" placeholder="Description de l'établissement..."></textarea>
            </div>

            <div class="form-row form-row-2col">
                <div class="form-group">
                    <label><?php echo __('inst_description_ar'); ?></label>
                    <textarea name="description_ar" rows="3" dir="rtl"></textarea>
                </div>
                <div class="form-group">
                    <label><?php echo __('inst_description_en'); ?></label>
                    <textarea name="description_en" rows="3"></textarea>
                </div>
            </div>

            <div class="form-group">
                <label><?php echo __('inst_requirements_fr'); ?></label>
                <textarea name="requirements" rows="2" placeholder="Bac Sciences + concours"></textarea>
            </div>

            <div class="form-row form-row-2col">
                <div class="form-group">
                    <label><?php echo __('inst_requirements_ar'); ?></label>
                    <textarea name="requirements_ar" rows="2" dir="rtl"></textarea>
                </div>
                <div class="form-group">
                    <label><?php echo __('inst_requirements_en'); ?></label>
                    <textarea name="requirements_en" rows="2"></textarea>
                </div>
            </div>
        </fieldset>

        <!-- Section: Academic Details -->
        <fieldset class="admin-fieldset">
            <legend><?php echo __('section_details'); ?></legend>

            <div class="form-group">
                <label>🎓 <?php echo __('inst_diploma_fr'); ?></label>
                <input type="text" name="diplome" placeholder="Diplôme d'Ingénieur d'État">
            </div>

            <div class="form-row form-row-2col">
                <div class="form-group">
                    <label><?php echo __('inst_diploma_ar'); ?></label>
                    <input type="text" name="diplome_ar" dir="rtl" placeholder="دبلوم مهندس الدولة">
                </div>
                <div class="form-group">
                    <label><?php echo __('inst_diploma_en'); ?></label>
                    <input type="text" name="diplome_en" placeholder="State Engineering Degree">
                </div>
            </div>

            <div class="form-row form-row-2col">
                <div class="form-group">
                    <label>⏳ <?php echo __('inst_duration'); ?></label>
                    <input type="text" name="duree_etudes" placeholder="5 ans">
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_popular" value="1">
                        <?php echo __('inst_popular'); ?>
                    </label>
                </div>
            </div>
        </fieldset>

        <!-- Section: Media & Links -->
        <fieldset class="admin-fieldset">
            <legend><?php echo __('section_media'); ?></legend>

            <div class="form-row form-row-2col">
                <div class="form-group">
                    <label>🌐 <?php echo __('inst_website'); ?></label>
                    <input type="url" name="site_web" placeholder="https://www.example.ma">
                </div>
                <div class="form-group">
                    <label>🖼️ <?php echo __('inst_image'); ?></label>
                    <input type="text" name="image" placeholder="default_school.jpg">
                </div>
            </div>
        </fieldset>

        <div class="admin-form-actions">
            <button type="submit" class="btn btn-primary btn-full">
                <?php echo __('btn_add_institution'); ?>
            </button>
            <a href="admin_dashboard.php" class="btn btn-outline btn-full">
                ← <?php echo __('back_to_dashboard'); ?>
            </a>
        </div>
    </form>
</div>

<style>
.admin-form-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 20px 60px;
}

.admin-form-header {
    margin-bottom: 32px;
}

.admin-form-header h1 {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--primary, #1e3a8a);
    margin: 0 0 8px;
}

.admin-form-header p {
    color: var(--text-muted, #64748b);
    font-size: 0.95rem;
}

.admin-msg {
    padding: 14px 20px;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 20px;
}

.admin-msg-error {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.admin-msg-success {
    background: #f0fdf4;
    color: #16a34a;
    border: 1px solid #bbf7d0;
}

.admin-fieldset {
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    background: var(--white, #fff);
}

.admin-fieldset legend {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--primary, #1e3a8a);
    padding: 0 10px;
}

.form-row {
    margin-bottom: 16px;
}

.form-row-2col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.form-group {
    margin-bottom: 14px;
}

.form-group label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-muted, #64748b);
    margin-bottom: 6px;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group input[type="url"],
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid var(--border-color, #e2e8f0);
    border-radius: 10px;
    font-size: 0.9rem;
    background: var(--bg-light, #f8fafc);
    color: var(--text-dark, #0f172a);
    transition: border-color 0.2s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: var(--primary, #1e3a8a);
    outline: none;
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}

.form-group textarea {
    resize: vertical;
    font-family: inherit;
}

.form-group input[type="checkbox"] {
    margin-right: 8px;
    transform: scale(1.2);
    accent-color: var(--primary, #1e3a8a);
}

.admin-form-actions {
    display: flex;
    gap: 16px;
    margin-top: 16px;
}

.admin-form-actions .btn {
    flex: 1;
    padding: 14px 24px;
    font-size: 1rem;
    font-weight: 700;
    border-radius: 12px;
    text-align: center;
}

@media (max-width: 768px) {
    .form-row-2col { grid-template-columns: 1fr; }
    .admin-form-actions { flex-direction: column; }
}
</style>

<?php require '../includes/footer.php'; ?>
