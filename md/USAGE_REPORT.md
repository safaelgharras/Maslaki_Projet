**Usage Report**

This document lists where the project uses `require` / `require_once`, `$_GET`, `$_POST`, and session functions (`session_start()` / `session_destroy()`). Links point to the file and the line where the usage was found.

**Require / require_once**
- **`require`**: [index.php](index.php#L2), [index.php](index.php#L14), [index.php](index.php#L17), [index.php](index.php#L206), [ai_process.php](ai_process.php#L3), [ai_process.php](ai_process.php#L7), [check_new_notifications.php](check_new_notifications.php#L3), [delete_notification.php](delete_notification.php#L3), [get_domains.php](get_domains.php#L2), [get_notifications.php](get_notifications.php#L3)
- **`require_once`**: [ai_process.php](ai_process.php#L4), [chatbot.php](chatbot.php#L12), [chatbot.php](chatbot.php#L13), [chatbot.php](chatbot.php#L14), [get_notifications.php](get_notifications.php#L2), [get_notifications.php](get_notifications.php#L4), [get_domains.php](get_domains.php#L7), [google_callback.php](google_callback.php#L10), [check_new_notifications.php](check_new_notifications.php#L35)

**`$_GET` usages**
- [delete_notification.php](delete_notification.php#L11) — `$_GET["id"]`
- [get_domains.php](get_domains.php#L4) — `$_GET['cat_id']`
- [google_callback.php](google_callback.php#L32) — `$_GET['code']` / [google_callback.php](google_callback.php#L33)
- [mark_notification_read.php](mark_notification_read.php#L11) — `$_GET["id"]`
- [mark_notification_read.php](mark_notification_read.php#L12) — `$_GET["all"]`
- [search_ajax.php](search_ajax.php#L4) — `$_GET["search"]`
- [search_ajax.php](search_ajax.php#L5) — `$_GET["city_id"]`
- [search_ajax.php](search_ajax.php#L6) — `$_GET["cat_id"]`
- [search_ajax.php](search_ajax.php#L7) — `$_GET["domain_id"]`
- [search_ajax.php](search_ajax.php#L8) — `$_GET["filiere_id"]`
- [search_ajax.php](search_ajax.php#L9) — `$_GET["bac_id"]`

**`$_POST` usages**
- [ai_process.php](ai_process.php#L14) — `$_POST["bac_branch"]`
- [ai_process.php](ai_process.php#L15) — `$_POST["average"]`
- [ai_process.php](ai_process.php#L16) — `$_POST["city"]`
- [google_callback.php](google_callback.php#L16) — `$_POST['dev_mode']`, [google_callback.php](google_callback.php#L17) — `$_POST['name']`, [google_callback.php](google_callback.php#L18) — `$_POST['email']`, [google_callback.php](google_callback.php#L19) — `$_POST['avatar']`
- [login_process.php](login_process.php#L8) — `$_POST["email"]`, [login_process.php](login_process.php#L9) — `$_POST["password"]`
- [process_appointment.php](process_appointment.php#L12) — `$_POST['action']`, [process_appointment.php](process_appointment.php#L16) — `$_POST['csrf_token']`, [process_appointment.php](process_appointment.php#L21) — `$_POST['title']`, [process_appointment.php](process_appointment.php#L22) — `$_POST['date']`, [process_appointment.php](process_appointment.php#L23) — `$_POST['time']`, [process_appointment.php](process_appointment.php#L53) — `$_POST['id']`
- [register_process.php](register_process.php#L7) — `$_POST["name"]`, [register_process.php](register_process.php#L8) — `$_POST["email"]`, [register_process.php](register_process.php#L9) — `$_POST["password"]`

**Sessions (`session_start()` / `session_destroy()`)**
- **`session_start()`**: [ai_process.php](ai_process.php#L2), [chatbot.php](chatbot.php#L11), [check_new_notifications.php](check_new_notifications.php#L2), [delete_notification.php](delete_notification.php#L2), [google_callback.php](google_callback.php#L9), [mark_notification_read.php](mark_notification_read.php#L2), [login_process.php](login_process.php#L2), [includes/csrf.php](includes/csrf.php#L3), [includes/header.php](includes/header.php#L3), [includes/helpers.php](includes/helpers.php#L19), [includes/lang_helper.php](includes/lang_helper.php#L3), [includes/platform_admin.php](includes/platform_admin.php#L38), [includes/platform_admin.php](includes/platform_admin.php#L49), [includes/platform_admin.php](includes/platform_admin.php#L63), [includes/platform_admin.php](includes/platform_admin.php#L87), [views/domain_details.php](views/domain_details.php#L2), [views/filiere_details.php](views/filiere_details.php#L2), [views/notifications.php](views/notifications.php#L2), [views/orientation_explore.php](views/orientation_explore.php#L8)
- **`session_destroy()`**: [views/logout.php](views/logout.php#L3)

If you want, I can:
- extend this report to include `include` / `include_once` usages,
- add exact code excerpts for each match,
- or run a full repository grep and include any remaining matches.

**Functions defined in the project**
Below are the functions and methods defined across the codebase with brief signatures and links to their definitions.

- [includes/csrf.php](includes/csrf.php#L6) — `csrf_token(): string`
- [includes/csrf.php](includes/csrf.php#L15) — `csrf_input(): string`
- [includes/csrf.php](includes/csrf.php#L20) — `verify_csrf_token(?string $token): bool`
- [includes/helpers.php](includes/helpers.php#L16) — `ensure_session(): void`
- [includes/helpers.php](includes/helpers.php#L28) — `is_logged_in(): bool`
- [includes/helpers.php](includes/helpers.php#L39) — `require_auth(string $redirectTo = 'login.php'): void`
- [includes/helpers.php](includes/helpers.php#L52) — `current_user_id(): ?int`
- [includes/helpers.php](includes/helpers.php#L68) — `json_response($data, int $statusCode = 200): void`
- [includes/helpers.php](includes/helpers.php#L82) — `json_success(string $message = 'Success', array $data = []): void`
- [includes/helpers.php](includes/helpers.php#L94) — `json_error(string $message, int $statusCode = 400, array $data = []): void`
- [includes/helpers.php](includes/helpers.php#L119) — `localized_db_field(array $row, string $field): string`
- [includes/helpers.php](includes/helpers.php#L144) — `localize_row(array $row, array $fields): array`
- [includes/helpers.php](includes/helpers.php#L159) — `localize_rows(array $rows, array $fields): array`
- [includes/helpers.php](includes/helpers.php#L187) — `resolve_institution_image(string $institutionName, ?string $dbImage = null, string $baseDir = null): string`
- [includes/helpers.php](includes/helpers.php#L257) — `translate_type(string $type): string`
- [includes/helpers.php](includes/helpers.php#L294) — `is_ajax_request(): bool`
- [includes/helpers.php](includes/helpers.php#L307) — `require_method(string $method, bool $exitOnFail = true): bool`
- [includes/lang_helper.php](includes/lang_helper.php#L29) — `__($key)`
- [includes/lang_helper.php](includes/lang_helper.php#L34) — `getLang()`
- [includes/lang_helper.php](includes/lang_helper.php#L39) — `isRTL()`
- [includes/lang_helper.php](includes/lang_helper.php#L43) — `getLocalizedDbField($row, $field)`
- [includes/lang_helper.php](includes/lang_helper.php#L57) — `formatLocalizedDate($dateStr)`
- [includes/platform_admin.php](includes/platform_admin.php#L20) — `platform_admin_role(PDO $pdo, int $userId): ?string`
- [includes/platform_admin.php](includes/platform_admin.php#L36) — `is_platform_admin(PDO $pdo): bool`
- [includes/platform_admin.php](includes/platform_admin.php#L47) — `is_superadmin(PDO $pdo): bool`
- [includes/platform_admin.php](includes/platform_admin.php#L61) — `require_platform_admin(PDO $pdo): void`
- [includes/platform_admin.php](includes/platform_admin.php#L85) — `require_superadmin(PDO $pdo): void`
- [get_notifications.php](get_notifications.php#L12) — `time_ago($timestamp)`
- [google_callback.php](google_callback.php#L79) — `processGoogleUser(PDO $pdo, string $name, string $email, string $avatar): void`
- [google_callback.php](google_callback.php#L120) — `curlPost(string $url, array $fields): string`
- [google_callback.php](google_callback.php#L136) — `curlGet(string $url, string $accessToken): string`
- [chatbot.php](chatbot.php#L89) — `handleAsk(PDO $pdo, ?int $userId, string $question, string $context = 'profile'): void`
- [chatbot.php](chatbot.php#L136) — `handleHistory(PDO $pdo, ?int $userId): void`
- [chatbot.php](chatbot.php#L174) — `handleClear(PDO $pdo, ?int $userId): void`
- [chatbot.php](chatbot.php#L192) — `searchDatabase(PDO $pdo, string $question, ?int $userId): ?string`
- [chatbot.php](chatbot.php#L237) — `searchByCity(PDO $pdo, string $city): string`
- [chatbot.php](chatbot.php#L261) — `searchByAverage(PDO $pdo, float $average): string`
- [chatbot.php](chatbot.php#L285) — `searchDeadlines(PDO $pdo): string`
- [chatbot.php](chatbot.php#L311) — `searchSavedSchools(PDO $pdo, ?int $userId): string`
- [chatbot.php](chatbot.php#L339) — `searchInstitutionDetail(PDO $pdo, string $name): string`
- [chatbot.php](chatbot.php#L371) — `searchInstitutionByName(PDO $pdo, string $question): string`
- [chatbot.php](chatbot.php#L407) — `searchByType(PDO $pdo, string $question): string`
- [chatbot.php](chatbot.php#L468) — `buildSystemPrompt(PDO $pdo, ?int $userId, string $context = 'profile'): string`
- [chatbot.php](chatbot.php#L496) — `saveLog(PDO $pdo, ?int $userId, string $question, string $answer, string $source): void`
- [ai_process.php](ai_process.php#L60) — `resolveInstitutionImagePath($institutionName, $dbImage = null)`
- [ai_process.php](ai_process.php#L102) — `translateType($type)`
- [views/institutions.php](views/institutions.php#L66) — `resolveInstitutionImagePath($institutionName, $dbImage = null)`
- [views/institutions.php](views/institutions.php#L108) — `translateType($type)`
- [views/institution_detail.php](views/institution_detail.php#L142) — `resolveDetailImage($path, $name)`
- [views/domain_details.php](views/domain_details.php#L57) — `resolveDomainCardImage($name, $dbImage = null)`
- [views/notifications.php](views/notifications.php#L39) — `getNotifIcon($type)`

(The project also defines many small front-end JavaScript functions inside view files; see the linked view files for client-side behavior.)

