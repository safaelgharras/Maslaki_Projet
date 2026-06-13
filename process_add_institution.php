<?php
/**
 * Process: Add a new institution (admin/superadmin only).
 */

require_once "config/DataBase.php";
require_once "includes/lang_helper.php";
require_once "includes/platform_admin.php";
require_once "includes/csrf.php";
require_once "includes/helpers.php";

// ── Auth guard ──────────────────────────────────────────────────
require_platform_admin($pdo);

// ── Method check ────────────────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: views/admin_add_institution.php?error=" . urlencode(__('error_invalid_request')));
    exit();
}

// ── CSRF check ──────────────────────────────────────────────────
if (!verify_csrf_token($_POST["csrf_token"] ?? null)) {
    header("Location: views/admin_add_institution.php?error=" . urlencode(__('error_csrf_retry')));
    exit();
}

// ── Gather & sanitise fields ────────────────────────────────────
$name        = trim($_POST['name']        ?? '');
$villeId     = !empty($_POST['ville_id']) ? (int) $_POST['ville_id'] : null;
$type        = trim($_POST['type']        ?? '');

// Required fields check
if ($name === '' || $villeId === null || $type === '') {
    header("Location: views/admin_add_institution.php?error=" . urlencode(__('error_all_fields_required')));
    exit();
}

$nameAr         = trim($_POST['name_ar']          ?? '') ?: null;
$nameEn         = trim($_POST['name_en']          ?? '') ?: null;
$seuil          = isset($_POST['seuil'])       && $_POST['seuil']       !== '' ? (float) $_POST['seuil']       : null;
$minAverage     = isset($_POST['min_average'])  && $_POST['min_average'] !== '' ? (float) $_POST['min_average'] : null;
$description    = trim($_POST['description']     ?? '') ?: null;
$descriptionAr  = trim($_POST['description_ar']  ?? '') ?: null;
$descriptionEn  = trim($_POST['description_en']  ?? '') ?: null;
$requirements   = trim($_POST['requirements']    ?? '') ?: null;
$requirementsAr = trim($_POST['requirements_ar'] ?? '') ?: null;
$requirementsEn = trim($_POST['requirements_en'] ?? '') ?: null;
$diplome        = trim($_POST['diplome']         ?? '') ?: null;
$diplomeAr      = trim($_POST['diplome_ar']      ?? '') ?: null;
$diplomeEn      = trim($_POST['diplome_en']      ?? '') ?: null;
$dureeEtudes    = trim($_POST['duree_etudes']    ?? '') ?: null;
$siteWeb        = trim($_POST['site_web']        ?? '') ?: null;
$image          = trim($_POST['image']           ?? '') ?: 'default_school.jpg';
$isPopular      = isset($_POST['is_popular']) ? 1 : 0;

// Resolve city name from villes table
$cityName    = null;
$cityNameAr  = null;
$cityNameEn  = null;
try {
    $cityStmt = $pdo->prepare("SELECT nom, nom_ar, nom_en FROM villes WHERE id = ?");
    $cityStmt->execute([$villeId]);
    $cityRow = $cityStmt->fetch();
    if ($cityRow) {
        $cityName   = $cityRow['nom'];
        $cityNameAr = $cityRow['nom_ar'];
        $cityNameEn = $cityRow['nom_en'];
    }
} catch (Exception $e) {}

// ── Insert ──────────────────────────────────────────────────────
try {
    $stmt = $pdo->prepare("
        INSERT INTO institutions (
            name, name_ar, name_en,
            city, city_ar, city_en, ville_id,
            type, seuil, min_average,
            description, description_ar, description_en,
            requirements, requirements_ar, requirements_en,
            diplome, diplome_ar, diplome_en,
            duree_etudes, site_web, image, is_popular
        ) VALUES (
            :name, :name_ar, :name_en,
            :city, :city_ar, :city_en, :ville_id,
            :type, :seuil, :min_average,
            :description, :description_ar, :description_en,
            :requirements, :requirements_ar, :requirements_en,
            :diplome, :diplome_ar, :diplome_en,
            :duree_etudes, :site_web, :image, :is_popular
        )
    ");

    $stmt->execute([
        ':name'            => $name,
        ':name_ar'         => $nameAr,
        ':name_en'         => $nameEn,
        ':city'            => $cityName,
        ':city_ar'         => $cityNameAr,
        ':city_en'         => $cityNameEn,
        ':ville_id'        => $villeId,
        ':type'            => $type,
        ':seuil'           => $seuil,
        ':min_average'     => $minAverage,
        ':description'     => $description,
        ':description_ar'  => $descriptionAr,
        ':description_en'  => $descriptionEn,
        ':requirements'    => $requirements,
        ':requirements_ar' => $requirementsAr,
        ':requirements_en' => $requirementsEn,
        ':diplome'         => $diplome,
        ':diplome_ar'      => $diplomeAr,
        ':diplome_en'      => $diplomeEn,
        ':duree_etudes'    => $dureeEtudes,
        ':site_web'        => $siteWeb,
        ':image'           => $image,
        ':is_popular'      => $isPopular,
    ]);

    header("Location: views/admin_add_institution.php?success=" . urlencode(__('success_institution_added')));
    exit();

} catch (Exception $e) {
    error_log('[Maslaki] Add institution error: ' . $e->getMessage());
    header("Location: views/admin_add_institution.php?error=" . urlencode(__('error_institution_add') . ' ' . $e->getMessage()));
    exit();
}
