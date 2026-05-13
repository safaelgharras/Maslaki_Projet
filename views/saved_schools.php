<?php
$pageTitle = "Sauvegardés";
require "../includes/header.php";
require "../config/DataBase.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION["user_id"];

$sql = "SELECT institutions.*
        FROM saved_schools
        JOIN institutions 
        ON saved_schools.institution_id = institutions.id
        WHERE saved_schools.student_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$student_id]);
$schools = $stmt->fetchAll();
?>

<h1 class="page-title">Mes écoles sauvegardées</h1>

<?php if (isset($_GET['success'])): ?>
    <div class="msg msg-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="msg msg-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<?php if (count($schools) == 0): ?>
    <div class="empty-state">
        <div class="icon">📭</div>
        <p>Tu n'as pas encore sauvegardé d'école.</p>
        <a href="institutions.php" class="btn btn-orange btn-lg" style="margin-top:15px;">Explorer les universités</a>
    </div>
<?php else: ?>
    <div class="cards-grid">
        <?php foreach($schools as $s): ?>
            <div class="card">
                <img src="../assets/images/institutions/<?php echo $s['image'] ?? 'default_school.jpg'; ?>" class="card-img" alt="<?php echo htmlspecialchars($s['name']); ?>">
                <div class="card-body">
                    <div class="card-tag"><?php echo htmlspecialchars($s['type']); ?></div>
                    <h3><?php echo htmlspecialchars($s['name']); ?></h3>
                    <p class="school-location">📍 <?php echo htmlspecialchars($s['city']); ?></p>
                    
                    <div class="card-footer">
                        <span class="seuil">Seuil: <strong><?php echo $s['min_average'] ?? '--'; ?></strong></span>
                        <div class="card-actions">
                            <a href="institution_detail.php?id=<?php echo $s['id']; ?>" class="btn-link">Détails →</a>
                            <a href="../remove_school.php?id=<?php echo $s['id']; ?>" class="btn btn-danger" onclick="return confirm('Supprimer cette école ?');">Supprimer</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>

<?php require "../includes/footer.php"; ?>               if (document.querySelectorAll('.card').length === 0) {
                    location.reload();
                }
            }, 300);
        } else {
            showToast('Erreur lors de la suppression', 'error');
        }
    });
}
</script>

<?php require "../includes/footer.php"; ?>