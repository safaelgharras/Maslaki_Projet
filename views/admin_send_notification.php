<?php
session_start();
require "../config/DataBase.php";

$pageTitle = "Administration - Notifications";
$base = "../";
require "../includes/header.php";

// Simple Admin Check (Role added in migration)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role FROM students WHERE id = ?");
$stmt->execute([$userId]);
$userRole = $stmt->fetchColumn();

// For this demo, we assume the first registered user or users with 'admin' role can access
if ($userRole !== 'admin' && $userId != 1) {
    echo "<div class='container'><div class='msg msg-error'>Accès refusé. Vous devez être administrateur.</div></div>";
    require "../includes/footer.php";
    exit();
}

$successMsg = "";
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $message = trim($_POST["message"]);
    $type = $_POST["type"];
    $link = trim($_POST["link"]);
    $target = $_POST["target"]; // 'all' or specific student_id

    if (empty($title) || empty($message)) {
        $errorMsg = "Le titre et le message sont obligatoires.";
    } else {
        try {
            if ($target === 'all') {
                $stmt = $pdo->prepare("INSERT INTO notifications (title, message, type, related_link, is_global) VALUES (?, ?, ?, ?, 1)");
                $stmt->execute([$title, $message, $type, $link]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO notifications (title, message, type, related_link, is_global, target_user_id) VALUES (?, ?, ?, ?, 0, ?)");
                $stmt->execute([$title, $message, $type, $link, $target]);
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
            <div class="form-group">
                <label>Titre de la notification</label>
                <input type="text" name="title" placeholder="Ex: Nouveau seuil ENSA Tanger" required>
            </div>

            <div class="form-group">
                <label>Message</label>
                <textarea name="message" rows="4" placeholder="Entrez le contenu du message ici..." required></textarea>
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
