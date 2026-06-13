# Maslaki Project — File Structure

```
Maslaki-projet/
│
├── .env                              ← Runtime credentials (DB, OAuth) — NOT committed
├── .env.example                      ← Credential template — safe to commit
├── .gitignore                        ← Ignores .env, logs, desktop.ini
│
├── index.php                         ← Landing page: hero, stats, popular schools
├── login_process.php                 ← POST: authenticate student
├── register_process.php              ← POST: register new student
├── google_callback.php               ← Google OAuth 2.0 handler
├── ai_process.php                    ← AI orientation results page
├── submit_review.php                 ← POST: submit school review (CSRF-protected)
├── process_appointment.php           ← POST: create or delete appointment (CSRF-protected)
├── process_add_institution.php       ← POST: add new institution (admin/superadmin, CSRF-protected)
├── migrate.php                       ← Manual migration runner (dev only)
│
│   ── Schools & Saved ─────────────────────────────────────────────────────────
├── save_school.php                   ← POST/AJAX: toggle saved school
├── remove_school.php                 ← POST/AJAX: remove saved school
├── search_ajax.php                   ← GET/JSON: institution search with filters
│
│   ── Notifications ─────────────────────────────────────────────────────────
├── get_notifications.php             ← GET/JSON: fetch user notifications
├── check_new_notifications.php       ← GET/JSON: unread count + latest
├── mark_notification_read.php        ← GET/JSON: mark one or all as read
├── delete_notification.php           ← GET/JSON: soft-delete a notification
│
│   ── Orientation ───────────────────────────────────────────────────────────
├── get_domains.php                   ← GET/JSON: domains for a category
│
│
├── config/
│   ├── DataBase.php                  ← PDO connection; reads DB_* from .env
│   └── google_config.php             ← Google OAuth constants; reads from .env
│
│
├── includes/
│   ├── helpers.php                   ← Shared utilities:
│   │                                     auth guards (require_auth, is_logged_in,
│   │                                       current_user_id)
│   │                                     JSON helpers (json_response, json_success,
│   │                                       json_error)
│   │                                     image resolver (resolve_institution_image)
│   │                                     localization (localized_db_field,
│   │                                       localize_row, localize_rows)
│   │                                     request helpers (is_ajax_request,
│   │                                       require_method)
│   │                                     type translation (translate_type)
│   ├── header.php                    ← Navbar, session check, unread badge,
│   │                                     XSS-safe notification toast/dropdown
│   ├── footer.php                    ← Page footer
│   ├── csrf.php                      ← CSRF token generation + verify_csrf_token()
│   ├── lang_helper.php               ← __(), getLocalizedDbField(), lang switch
│   ├── translations.php              ← Translation strings (fr/ar/en)
│   └── platform_admin.php            ← is_platform_admin(), require_platform_admin()
│
│
├── views/
│   │
│   │   ── Auth ─────────────────────────────────────────────────────────────
│   ├── login.php                     ← Login form
│   ├── register.php                  ← Registration form
│   ├── logout.php                    ← Destroys session, redirects
│   │
│   │   ── Student ───────────────────────────────────────────────────────────
│   ├── dashboard.php                 ← User dashboard: upcoming contests,
│   │                                     saved schools, notifications preview
│   ├── saved_schools.php             ← Saved school cards + remove (POST+CSRF)
│   ├── notifications.php             ← Full notification list
│   ├── appointments.php              ← Appointment booking + list; delete via
│   │                                     POST form (CSRF-protected)
│   │
│   │   ── Institutions ─────────────────────────────────────────────────────
│   ├── institutions.php              ← Filterable school card grid (AJAX)
│   ├── institution_detail.php        ← Full school page: info, filieres,
│   │                                     reviews (star rating UI), sub-schools,
│   │                                     bac requirements
│   │
│   │   ── Orientation ─────────────────────────────────────────────────────
│   ├── ai_form.php                   ← AI orientation input form
│   ├── orientation_explore.php       ← Category → Domain explorer
│   ├── domain_details.php            ← Domain detail: filieres + schools
│   ├── filiere_details.php           ← Program detail + offering schools
│   │
│   │   ── Contests ─────────────────────────────────────────────────────────
│   ├── contests.php                  ← Contest list with deadlines
│   │
│   │   ── Admin ───────────────────────────────────────────────────────────
│   ├── admin_reviews.php             ← Approve/reject pending reviews (shows
│   │                                     star ratings); POST+CSRF only
│   ├── admin_send_notification.php   ← Broadcast notifications
│   ├── admin_add_institution.php     ← Add new institution form (admin/superadmin)
│   └── admin_dashboard.php           ← Platform management overview
│
│
├── assets/
│   ├── css/
│   │   └── style.css                 ← Design system (Navy + Orange, dark mode,
│   │                                     responsive, card components)
│   ├── js/
│   │   └── script.js                 ← Client-side logic
│   └── images/
│       ├── Institutions/             ← School logo images (~100 files,
│       │                                 mixed case: .png .jpg .webp .WEBP .PNG)
│       ├── Maquette/                 ← Design mockup assets
│       ├── logo.png
│       └── students_illustration.png
│
│
├── lang/
│   ├── fr.php                        ← French translations (default)
│   ├── ar.php                        ← Arabic translations
│   └── en.php                        ← English translations
│
│
├── database/
│   │   ── Core ──────────────────────────────────────────────────────────────
│   ├── maslaki.sql                   ← Base schema
│   ├── maslaki_full_database.sql     ← Full DB dump (schema + seed data)
│   │
│   │   ── Schema Migrations ─────────────────────────────────────────────────
│   ├── schema_update.sql             ← Core schema additions
│   ├── features_update.sql           ← Feature column additions
│   ├── fix_missing_tables.sql        ← Adds tables that were absent
│   ├── fix_missing_images.sql        ← Image path corrections
│   ├── fix_seuil.sql                 ← Admission threshold fixes
│   ├── add_review_rating.sql         ← Adds rating column to reviews table
│   ├── add_superadmin_role.sql       ← Adds superadmin role to students table
│   ├── ensure_translations_columns.sql ← Guarantees _ar/_en columns exist
│   ├── map_villes.sql                ← Maps institutions to villes table
│   ├── reorganize_domains.sql        ← Domain structure migration
│   ├── setup_logic_relationships.sql ← FK and pivot table setup
│   ├── cleanup_duplicates.sql        ← Deduplication script
│   │
│   │   ── Seed Data ────────────────────────────────────────────────────────
│   ├── seed_deadlines.sql            ← Deadline data
│   ├── seed_real_contests.sql        ← Contest data
│   ├── fill_info.sql                 ← Fills missing institution fields
│   ├── populate_orientation_data.sql ← Category/domain/filiere data
│   ├── notifications_setup.sql       ← Notification tables + initial data
│   │
│   │   ── Translations ─────────────────────────────────────────────────────
│   ├── bac_localization.sql          ← Bac type translations
│   ├── english_localization.sql      ← English translation entries
│   ├── localization_update.sql       ← General localization patches
│   ├── translate_descriptions.sql    ← Institution description translations
│   ├── translate_filieres.sql        ← Filiere name translations
│   ├── translate_institution_details.sql ← Detail field translations
│   ├── translate_notifications.sql   ← Notification message translations
│   ├── update_translations_notif_contests.sql ← Contest/notif translation updates
│   │
│   └── update_institutions_info.php  ← Programmatic data updater script
│
│
├── admin/
│   └── admin_migration.sql           ← Admin role table + initial staff setup
│
├── models/
│   └── (empty — reserved for future model classes)
│
├── Rapport de PFE/
│   └── (project report documents)
│
└── md/
    ├── structure.md                  ← This file
    ├── progress.md                   ← Feature tracker + changelog
    └── workflow.md                   ← Development workflow guide
```

---

## Key Conventions

**Database access**
All pages that need a DB connection: `require "config/DataBase.php";`
The connection is always in `$pdo`.

**Auth flow**
```php
require_once "includes/helpers.php";
require_auth();          // Redirects to login.php if not logged in
$userId = current_user_id();
```

**JSON endpoints**
```php
require_once "includes/helpers.php";
json_response($data);               // 200 + Content-Type set
json_success('Done', ['key' => 1]); // {"status":"success","message":"Done","key":1}
json_error('Not allowed', 403);     // {"status":"error","message":"Not allowed"}
```

**Localization**
```php
// Translate a UI string
echo __('page_title');

// Get the correct language variant of a DB field
$name = localized_db_field($row, 'name');     // → name_ar if lang=ar, else name

// Localize multiple fields at once
$row = localize_row($row, ['name', 'city', 'description']);
$rows = localize_rows($rows, ['nom']);
```

**Image resolution**
```php
$src = resolve_institution_image($inst['name'], $inst['image'] ?? null);
// Searches assets/images/ and assets/images/Institutions/
// Falls back to default_school.jpg
```

**Path convention**
- Root-level PHP files include config as: `require "config/DataBase.php";`
- Files in `views/` include config as: `require "../config/DataBase.php";`

**Environment variables**
Credentials never hardcoded. Set in `.env` (dev) or server environment (prod):
```
DB_HOST, DB_NAME, DB_USER, DB_PASS
GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REDIRECT_URI
```
