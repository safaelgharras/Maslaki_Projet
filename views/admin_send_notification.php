<?php
require_once "../includes/lang_helper.php";
$pageTitle = __("platform_admin_notifications_page_title");
require "../includes/header.php";
require "../config/DataBase.php";
require_once "../includes/platform_admin.php";
require_platform_admin($pdo);

$successMsg = "";
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $title_ar = trim($_POST["title_ar"]);
    $title_en = trim($_POST["title_en"]);
    $message = trim($_POST["message"]);
    $message_ar = trim($_POST["message_ar"]);
    $message_en = trim($_POST["message_en"]);
    $type = $_POST["type"];
    $link = trim($_POST["link"]);
    $target = $_POST["target"]; // 'all' or specific student_id

    if (empty($title) || empty($message)) {
        $errorMsg = "Le titre et le message (Français) sont obligatoires.";
    } else {
        try {
            if ($target === 'all') {
                $stmt = $pdo->prepare("INSERT INTO notifications (title, title_ar, title_en, message, message_ar, message_en, type, related_link, is_global) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([$title, $title_ar, $title_en, $message, $message_ar, $message_en, $type, $link]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO notifications (title, title_ar, title_en, message, message_ar, message_en, type, related_link, is_global, target_user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?)");
                $stmt->execute([$title, $title_ar, $title_en, $message, $message_ar, $message_en, $type, $link, $target]);
            }
            $successMsg = "Notification envoyée avec succès !";
        } catch (Exception $e) {
            $errorMsg = "Erreur : " . $e->getMessage();
        }
    }
}

// Get all students for targeting
$students = $pdo->query("SELECT id, name, email FROM students ORDER BY name ASC")->fetchAll();
?>

<div class="container" style="max-width: 700px; margin-top: 40px;">
    <h1 class="page-title">Envoyer une Notification</h1>
    
    <?php if ($successMsg): ?>
        <div class="msg msg-success"><?php echo $successMsg; ?></div>
    <?php endif; ?>
    
    <?php if ($errorMsg): ?>
        <div class="msg msg-error"><?php echo $errorMsg; ?></div>
    <?php endif; ?>

    <div class="form-container" style="max-width: 100%; box-shadow: var(--shadow-md); border-radius: 20px;">
        <form method="POST">
            <div class="form-group" style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                <div>
                    <label>Titre (Français)</label>
                    <input type="text" name="title" placeholder="Ex: Nouveau seuil" required>
                </div>
                <div>
                    <label>Titre (English)</label>
                    <input type="text" name="title_en" placeholder="Ex: New threshold">
                </div>
            </div>

            <div class="form-group">
                <label>Titre (Arabic)</label>
                <input type="text" name="title_ar" placeholder="Ex: عتبة جديدة" style="text-align: right;" dir="rtl">
            </div>

            <div class="form-group">
                <label>Message (Français)</label>
                <textarea name="message" rows="3" placeholder="Contenu en français..." required></textarea>
            </div>

            <div class="form-group">
                <label>Message (English)</label>
                <textarea name="message_en" rows="3" placeholder="English content..."></textarea>
            </div>

            <div class="form-group">
                <label>Message (Arabic)</label>
                <textarea name="message_ar" rows="3" placeholder="المحتوى باللغة العربية..." style="text-align: right;" dir="rtl"></textarea>
            </div>

            <div class="form-group" style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                <div>
                    <label>Type</label>
                    <select name="type">
                        <option value="system">Système</option>
                        <option value="school">École</option>
                        <option value="filiere">Filière</option>
                        <option value="announcement">Annonce</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="deadline">Date Limite</option>
                    </select>
                </div>
                <div>
                    <label>Cible</label>
                    <select name="target">
                        <option value="all">Tous les utilisateurs</option>
                        <optgroup label="Étudiant spécifique">
                            <?php foreach($students as $s): ?>
                                <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?> (<?php echo $s['email']; ?>)</option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Lien relatif (optionnel)</label>
                <input type="text" name="link" placeholder="Ex: views/institution_detail.php?id=61">
                <small style="color:var(--text-muted);">Laissez vide si aucun lien n'est nécessaire.</small>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; padding:15px; font-size:1rem;">🚀 Diffuser la notification</button>
        </form>
    </div>
</div>

<?php require "../includes/footer.php"; ?>
