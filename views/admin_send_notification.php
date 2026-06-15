<?php
/**
 * admin_send_notification.php — Admin page to broadcast or target notifications.
 *
 * Allows platform admins to create a new notification with:
 *   - Title and message in 3 languages (FR, AR, EN)
 *   - Type (system, school, filiere, announcement, maintenance, orientation, deadline)
 *   - Target: all students (global) or a specific student
 *   - Optional related link
 *
 * The notification is inserted into the `notifications` table.
 * Protected by CSRF token and platform_admin role check.
 */

require_once "../includes/lang_helper.php";
require "../config/DataBase.php";
require_once "../includes/platform_admin.php";
require_once "../includes/csrf.php";

// Block access for non-admin users
require_platform_admin($pdo);

$successMsg = "";
$errorMsg   = "";

// Allowed notification types (must match the ENUM in the DB)
$allowedTypes = ['system', 'school', 'filiere', 'announcement', 'maintenance', 'orientation', 'deadline'];
$notificationColumns = [];

// Discover actual columns in the notifications table (for optional _ar/_en fields)
try {
    $columnStmt = $pdo->query("SHOW COLUMNS FROM notifications");
    foreach ($columnStmt->fetchAll(PDO::FETCH_ASSOC) as $column) {
        $notificationColumns[$column['Field']] = true;
    }
} catch (Exception $e) {
    $errorMsg = __('admin_notif_error_db_structure') . ' : ' . $e->getMessage();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verify CSRF token
    if (!verify_csrf_token($_POST["csrf_token"] ?? null)) {
        $errorMsg = __('admin_notif_error_csrf');
    }

    // Collect form fields (trilingual title/message + type/link/target)
    $title    = trim($_POST["title"] ?? "");
    $titleAr  = trim($_POST["title_ar"] ?? "");
    $titleEn  = trim($_POST["title_en"] ?? "");
    $message  = trim($_POST["message"] ?? "");
    $messageAr = trim($_POST["message_ar"] ?? "");
    $messageEn = trim($_POST["message_en"] ?? "");
    $type   = $_POST["type"] ?? "system";
    $link   = trim($_POST["link"] ?? "");
    $target = $_POST["target"] ?? "all";

    // Validation: required fields, valid type, valid target
    if ($errorMsg === "" && ($title === "" || $message === "")) {
        $errorMsg = __('admin_notif_error_required');
    } elseif ($errorMsg === "" && !in_array($type, $allowedTypes, true)) {
        $errorMsg = __('admin_notif_error_type');
    } elseif ($errorMsg === "" && $target !== "all" && filter_var($target, FILTER_VALIDATE_INT) === false) {
        $errorMsg = __('admin_notif_error_target');
    } elseif ($errorMsg === "") {
        try {
            // Build dynamic INSERT with only columns that exist in the table
            $columns = ['title', 'message', 'type', 'related_link', 'is_global'];
            $values  = [$title, $message, $type, $link !== "" ? $link : null, $target === "all" ? 1 : 0];

            // Add localized columns if they exist in the schema
            foreach ([
                'title_ar' => $titleAr,
                'title_en' => $titleEn,
                'message_ar' => $messageAr,
                'message_en' => $messageEn,
            ] as $column => $value) {
                if (isset($notificationColumns[$column])) {
                    $columns[] = $column;
                    $values[]  = $value !== "" ? $value : null;
                }
            }

            // If targeting a specific student, add target_user_id
            if ($target !== "all") {
                $columns[] = 'target_user_id';
                $values[]  = (int) $target;
            }

            $placeholders = implode(', ', array_fill(0, count($columns), '?'));
            $stmt = $pdo->prepare("INSERT INTO notifications (" . implode(', ', $columns) . ") VALUES ($placeholders)");
            $stmt->execute($values);
            $successMsg = __('admin_notif_success');
        } catch (Exception $e) {
            $errorMsg = __('admin_notif_error_prefix') . ' : ' . $e->getMessage();
        }
    }
}

// Fetch all students for the target dropdown
try {
    $students = $pdo->query("SELECT id, name, email FROM students ORDER BY name ASC")->fetchAll();
} catch (Exception $e) {
    $students = [];
    if ($errorMsg === "") {
        $errorMsg = __('admin_notif_error_students') . ' : ' . $e->getMessage();
    }
}

$pageTitle = __("platform_admin_notifications_page_title");
require "../includes/header.php";
?>

<div class="container" style="max-width: 700px; margin-top: 40px;">
    <h1 class="page-title"><?php echo __('admin_notif_title'); ?></h1>

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
                    <label><?php echo __('admin_notif_label_title_fr'); ?></label>
                    <input type="text" name="title" placeholder="<?php echo __('admin_notif_ph_title_fr'); ?>" required>
                </div>
                <div>
                    <label><?php echo __('admin_notif_label_title_en'); ?></label>
                    <input type="text" name="title_en" placeholder="<?php echo __('admin_notif_ph_title_en'); ?>">
                </div>
            </div>

            <div class="form-group">
                <label><?php echo __('admin_notif_label_title_ar'); ?></label>
                <input type="text" name="title_ar" placeholder="<?php echo __('admin_notif_ph_title_ar'); ?>" style="text-align: right;" dir="rtl">
            </div>

            <div class="form-group">
                <label><?php echo __('admin_notif_label_msg_fr'); ?></label>
                <textarea name="message" rows="3" placeholder="<?php echo __('admin_notif_ph_msg_fr'); ?>" required></textarea>
            </div>

            <div class="form-group">
                <label><?php echo __('admin_notif_label_msg_en'); ?></label>
                <textarea name="message_en" rows="3" placeholder="<?php echo __('admin_notif_ph_msg_en'); ?>"></textarea>
            </div>

            <div class="form-group">
                <label><?php echo __('admin_notif_label_msg_ar'); ?></label>
                <textarea name="message_ar" rows="3" placeholder="<?php echo __('admin_notif_ph_msg_ar'); ?>" style="text-align: right;" dir="rtl"></textarea>
            </div>

            <div class="form-group" style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                <div>
                    <label><?php echo __('admin_notif_label_type'); ?></label>
                    <select name="type">
                        <option value="system"><?php echo __('admin_notif_type_system'); ?></option>
                        <option value="school"><?php echo __('admin_notif_type_school'); ?></option>
                        <option value="filiere"><?php echo __('admin_notif_type_filiere'); ?></option>
                        <option value="announcement"><?php echo __('admin_notif_type_announcement'); ?></option>
                        <option value="maintenance"><?php echo __('admin_notif_type_maintenance'); ?></option>
                        <option value="orientation"><?php echo __('admin_notif_type_orientation'); ?></option>
                        <option value="deadline"><?php echo __('admin_notif_type_deadline'); ?></option>
                    </select>
                </div>
                <div>
                    <label><?php echo __('admin_notif_label_target'); ?></label>
                    <select name="target">
                        <option value="all"><?php echo __('admin_notif_target_all'); ?></option>
                        <optgroup label="<?php echo __('admin_notif_target_specific'); ?>">
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
                <label><?php echo __('admin_notif_label_link'); ?></label>
                <input type="text" name="link" placeholder="<?php echo __('admin_notif_ph_link'); ?>">
                <small style="color:var(--text-muted);"><?php echo __('admin_notif_link_hint'); ?></small>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; padding:15px; font-size:1rem;"><?php echo __('admin_notif_submit'); ?></button>
        </form>
    </div>
</div>

<?php require "../includes/footer.php"; ?>
