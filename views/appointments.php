<?php
require_once "../includes/lang_helper.php";
$pageTitle = __('my_appointments');
require "../includes/header.php";
require "../config/DataBase.php";
require_once "../includes/csrf.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["user_id"];

// Get user appointments
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE student_id = ? ORDER BY appointment_date ASC, appointment_time ASC");
$stmt->execute([$userId]);
$appointments = $stmt->fetchAll();
?>

<div class="appointments-container">
    <div class="page-header">
        <h1>🗓️ <?php echo __('my_appointments'); ?></h1>
        <p><?php echo __('manage_orientation_sessions'); ?></p>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="msg msg-success">✅ <?php echo __('appointment_added_success'); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="msg msg-success">🗑️ <?php echo __('appointment_deleted'); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="msg msg-error">❌ <?php echo __('an_error_occurred'); ?></div>
    <?php endif; ?>

    <div class="appointments-grid">
        <section class="add-appointment-section">
            <div class="form-card">
                <h3><?php echo __('book_new_appointment'); ?></h3>
                <form action="../process_appointment.php" method="POST">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="action" value="create">
                    <div class="form-group">
                        <label><?php echo __('appointment_subject'); ?></label>
                        <input type="text" name="title" placeholder="<?php echo __('appointment_subject_placeholder'); ?>" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo __('appointment_date'); ?></label>
                            <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label><?php echo __('appointment_time_label'); ?></label>
                            <input type="time" name="time" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full"><?php echo __('confirm_appointment'); ?></button>
                </form>
            </div>
        </section>

        <section class="appointments-list-section">
            <div class="section-header">
                <h3><?php echo __('upcoming_appointments'); ?></h3>
            </div>
            
            <?php if (count($appointments) > 0): ?>
                <div class="appointments-list">
                    <?php foreach($appointments as $app): 
                        $statusClass = 'status-' . $app['status'];
                    ?>
                        <div class="appointment-card">
                            <div class="app-date">
                                <span class="d-day"><?php echo date('d', strtotime($app['appointment_date'])); ?></span>
                                <span class="d-month"><?php echo date('M', strtotime($app['appointment_date'])); ?></span>
                            </div>
                            <div class="app-info">
                                <h4><?php echo htmlspecialchars($app['title']); ?></h4>
                                <p>🕒 <?php echo date('H:i', strtotime($app['appointment_time'])); ?></p>
                                <span class="badge <?php echo $statusClass; ?>"><?php echo __('status_' . $app['status']); ?></span>
                            </div>
                            <div class="app-actions">
                                <form method="POST" action="../process_appointment.php" style="display:inline;">
                                    <?php echo csrf_input(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                                    <button type="submit" class="btn-delete" onclick="return confirm('<?php echo addslashes(__('delete_appointment_confirm')); ?>')">×</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="icon">📅</div>
                    <p><?php echo __('no_appointments_yet'); ?></p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<style>
.appointments-container { max-width: 1150px; margin: 0 auto; padding: 50px 20px; animation: fadeIn 0.5s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

.page-header { margin-bottom: 60px; text-align: center; position: relative; }
.page-header h1 { font-size: clamp(2.5rem, 5vw, 3.2rem); color: var(--primary); font-weight: 900; margin-bottom: 15px; letter-spacing: -1px; }
.page-header p { color: var(--text-muted); font-size: 1.15rem; max-width: 600px; margin: 0 auto; direction: ltr; font-weight: 500; display: inline-block; }
html[dir="rtl"] .page-header p { direction: rtl; }

/* Alert Messages styling */
.msg { padding: 16px 20px; border-radius: 16px; margin-bottom: 30px; font-weight: 700; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
.msg-success { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
.msg-error { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

/* Grid Layout */
.appointments-grid { display: flex; flex-wrap: wrap; gap: 50px; align-items: start; }
.add-appointment-section { flex: 0 0 400px; order: 1; }
.appointments-list-section { flex: 1; min-width: 300px; order: 2; }
html[dir="rtl"] .add-appointment-section { order: 2; }
html[dir="rtl"] .appointments-list-section { order: 1; }

/* Form Card Styling */
.form-card { background: var(--white); padding: 40px; border-radius: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.04), 0 1px 3px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05); position: sticky; top: 100px; }
.form-card h3 { margin-bottom: 30px; color: var(--primary-dark); font-weight: 850; font-size: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 15px; }

.form-group { margin-bottom: 20px; text-align: start; }
.form-group label { display: block; margin-bottom: 10px; font-weight: 700; color: var(--primary); font-size: 0.95rem; }
.form-group input { width: 100%; padding: 15px 18px; border: 2px solid var(--border-color); border-radius: 16px; font-size: 1rem; background: var(--bg-card); transition: all 0.3s ease; font-family: inherit; color: var(--text-color); }
.form-group input:focus { border-color: var(--accent); background: #fff; box-shadow: 0 0 0 4px rgba(var(--accent-rgb), 0.1); outline: none; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }

.btn-full { width: 100%; padding: 16px; font-weight: 800; border-radius: 16px; font-size: 1.05rem; margin-top: 15px; transition: all 0.3s ease; background: var(--accent); color: #fff; border: none; cursor: pointer; }
.btn-full:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(var(--accent-rgb), 0.3); background: #ea580c; }

/* List Section */
.section-header h3 { font-size: 1.8rem; color: var(--primary); font-weight: 850; margin-bottom: 30px; }

.appointments-list { display: grid; gap: 20px; }
.appointment-card { 
    background: var(--white); padding: 25px; border-radius: 24px; display: flex; align-items: center; gap: 25px; 
    box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.04); transition: all 0.3s ease; 
}
.appointment-card:hover { transform: translateY(-5px); border-color: rgba(var(--accent-rgb), 0.3); box-shadow: 0 15px 35px rgba(0,0,0,0.06); }

.app-date { background: linear-gradient(135deg, rgba(var(--primary-rgb),0.03), rgba(var(--primary-rgb),0.08)); padding: 15px; border-radius: 20px; text-align: center; min-width: 90px; border: 1px solid rgba(var(--primary-rgb),0.05); }
.app-date .d-day { display: block; font-size: 2rem; font-weight: 900; color: var(--primary); line-height: 1; margin-bottom: 5px; }
.app-date .d-month { font-size: 0.9rem; text-transform: uppercase; color: var(--accent); font-weight: 800; letter-spacing: 1px; }

.app-info { flex: 1; text-align: start; }
.app-info h4 { font-size: 1.3rem; margin-bottom: 10px; color: var(--primary-dark); font-weight: 800; }
.app-info p { font-size: 1rem; color: var(--text-muted); margin-bottom: 12px; font-weight: 600; display: flex; align-items: center; gap: 5px; }

.badge { padding: 6px 14px; border-radius: 12px; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; }
.status-pending { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
.status-confirmed { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
.status-cancelled { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

.app-actions { margin-left: auto; }
html[dir="rtl"] .app-actions { margin-left: 0; margin-right: auto; }
.app-actions form { display: inline; margin: 0; padding: 0; }
.btn-delete { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 14px; background: #fef2f2; color: #ef4444; font-size: 1.5rem; text-decoration: none; transition: all 0.2s ease; border: 1px solid #fee2e2; cursor: pointer; }
.btn-delete:hover { background: #fee2e2; color: #b91c1c; transform: scale(1.08); border-color: #fca5a5; }

/* Empty state */
.empty-state { text-align: center; padding: 80px 30px; background: rgba(var(--primary-rgb), 0.01); border-radius: 28px; border: 2px dashed rgba(var(--primary-rgb), 0.15); }
.empty-state .icon { font-size: 4.5rem; margin-bottom: 25px; opacity: 0.9; }
.empty-state p { font-size: 1.25rem; color: var(--text-muted); font-weight: 600; }

@media (max-width: 992px) {
    .add-appointment-section { flex: 1 1 100%; order: 1; }
    .appointments-list-section { order: 2; }
    .appointments-grid { gap: 30px; }
    .appointment-card { flex-direction: column; align-items: flex-start; text-align: left; }
    html[dir="rtl"] .appointment-card { text-align: right; }
    .app-actions, html[dir="rtl"] .app-actions { margin: 0; align-self: flex-end; }
}
</style>

<?php require "../includes/footer.php"; ?>
