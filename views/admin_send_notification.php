<?php
require_once "../includes/lang_helper.php";
require "../config/DataBase.php";
require_once "../includes/platform_admin.php";
require_once "../includes/csrf.php";
require_platform_admin($pdo);

$successMsg = "";
$errorMsg = "";
$allowedTypes = ['system', 'school', 'filiere', 'announcement', 'maintenance', 'orientation', 'deadline'];
$notificationColumns = [];

try {
    $columnStmt = $pdo->query("SHOW COLUMNS FROM notifications");
    foreach ($columnStmt->fetchAll(PDO::FETCH_ASSOC) as $column) {
        $notificationColumns[$column['Field']] = true;
    }
} catch (Exception $e) {
    $errorMsg = "Impossible de lire la structure des notifications : " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!verify_csrf_token($_POST["csrf_token"] ?? null)) {
        $errorMsg = "Requete invalide. Rechargez la page et reessayez.";
    }

    $title = trim($_POST["title"] ?? "");
    $titleAr = trim($_POST["title_ar"] ?? "");
    $titleEn = trim($_POST["title_en"] ?? "");
    $message = trim($_POST["message"] ?? "");
    $messageAr = trim($_POST["message_ar"] ?? "");
    $messageEn = trim($_POST["message_en"] ?? "");
    $type = $_POST["type"] ?? "system";
    $link = trim($_POST["link"] ?? "");
    $target = $_POST["target"] ?? "all";

    if ($errorMsg === "" && ($title === "" || $message === "")) {
        $errorMsg = "Le titre et le message en francais sont obligatoires.";
    } elseif ($errorMsg === "" && !in_array($type, $allowedTypes, true)) {
        $errorMsg = "Type de notification invalide.";
    } elseif ($errorMsg === "" && $target !== "all" && filter_var($target, FILTER_VALIDATE_INT) === false) {
        $errorMsg = "Cible invalide.";
    } elseif ($errorMsg === "") {
        try {
            $columns = ['title', 'message', 'type', 'related_link', 'is_global'];
            $values = [$title, $message, $type, $link !== "" ? $link : null, $target === "all" ? 1 : 0];

            foreach ([
                'title_ar' => $titleAr,
                'title_en' => $titleEn,
                'message_ar' => $messageAr,
                'message_en' => $messageEn,
            ] as $column => $value) {
                if (isset($notificationColumns[$column])) {
                    $columns[] = $column;
                    $values[] = $value !== "" ? $value : null;
                }
            }

            if ($target !== "all") {
                $columns[] = 'target_user_id';
                $values[] = (int) $target;
            }

            $placeholders = implode(', ', array_fill(0, count($columns), '?'));
            $stmt = $pdo->prepare("INSERT INTO notifications (" . implode(', ', $columns) . ") VALUES ($placeholders)");
            $stmt->execute($values);
            $successMsg = "Notification envoyee avec succes !";
        } catch (Exception $e) {
            $errorMsg = "Erreur : " . $e->getMessage();
        }
    }
}

try {
    $students = $pdo->query("SELECT id, name, email FROM students ORDER BY name ASC")->fetchAll();
} catch (Exception $e) {
    $students = [];
    if ($errorMsg === "") {
        $errorMsg = "Impossible de charger les etudiants : " . $e->getMessage();
    }
}

$pageTitle = __("platform_admin_notifications_page_title");
require "../includes/header.php";
?>

<div class="container" style="max-width: 700px; margin-top: 40px;">
    <h1 class="page-title">Envoyer une Notification</h1>

    <?php if ($successMsg): ?>
        <div class="msg msg-success"><?php echo htmlspecialchars($successMsg); ?></div>
    <?php endif; ?>

    <?php if ($errorMsg): ?>
        <div class="msg msg-error"><?php echo htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>

    <div class="form-container" style="max-width: 100%; box-shadow: var(--shadow-md); border-radius: 20px;">
        <form method="POST">
            <?php echo csrf_input(); ?>

            <div class="form-group" style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                <div>
                    <label>Titre (Francais)</label>
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
                <label>Message (Francais)</label>
                <textarea name="message" rows="3" placeholder="Contenu en francais..." required></textarea>
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
                        <option value="system">Systeme</option>
                        <option value="school">Ecole</option>
                        <option value="filiere">Filiere</option>
                        <option value="announcement">Annonce</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="orientation">Orientation</option>
                        <option value="deadline">Date limite</option>
                    </select>
                </div>
                <div>
                    <label>Cible</label>
                    <select name="target">
                        <option value="all">Tous les utilisateurs</option>
                        <optgroup label="Etudiant specifique">
                            <?php foreach ($students as $student): ?>
                                <option value="<?php echo (int) $student['id']; ?>">
                                    <?php echo htmlspecialchars($student['name']); ?> (<?php echo htmlspecialchars($student['email']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Lien relatif (optionnel)</label>
                <input type="text" name="link" placeholder="Ex: views/institution_detail.php?id=61">
                <small style="color:var(--text-muted);">Laissez vide si aucun lien n'est necessaire.</small>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; padding:15px; font-size:1rem;">Diffuser la notification</button>
        </form>
    </div>
</div>

<?php require "../includes/footer.php"; ?>
