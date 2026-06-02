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
├── submit_review.php                 ← POST: submit school review
├── process_appointment.php           ← POST: book an appointment
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
│   ├── saved_schools.php             ← Saved school cards + remove
│   ├── notifications.php             ← Full notification list
│   ├── appointments.php              ← Appointment history/booking
│   │
│   │   ── Institutions ─────────────────────────────────────────────────────
│   ├── institutions.php              ← Filterable school card grid (AJAX)
│   ├── institution_detail.php        ← Full school page: info, filieres,
│   │                                     reviews, sub-schools, bac requirements
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
│   ├── admin_reviews.php             ← Approve/reject pending reviews
│   ├── admin_send_notification.php   ← Broadcast notifications
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
│       ├── institutions/             ← School logo images (107 files:
│       │                                 .png, .jpg, .webp, .WEBP, .PNG)
│       ├── logo.png
│       └── students_illustration.png
│
│
├── lang/
│   └── [Locale files used by lang_helper.php]
│
│
├── database/
│   ├── maslaki.sql                   ← Base schema
│   ├── maslaki_full_database.sql     ← Full DB dump (schema + seed data)
│   ├── schema_update.sql             ← Core schema migrations
│   ├── features_update.sql           ← Feature additions
│   ├── notifications_setup.sql       ← Notification tables
│   ├── seed_deadlines.sql            ← Deadline data
│   ├── seed_real_contests.sql        ← Contest data
│   ├── populate_orientation_data.sql ← Category/domain/filiere data
│   ├── bac_localization.sql          ← Bac type translations
│   ├── english_localization.sql      ← English translation entries
│   ├── translate_*.sql               ← Per-table translation patches
│   ├── fix_*.sql                     ← Hotfix migrations
│   ├── reorganize_domains.sql        ← Domain structure migration
│   └── update_institutions_info.php  ← Programmatic data updater
│
│
├── admin/
│   └── admin_migration.sql           ← Admin role table + initial staff setup
│
├── models/
│   └── [Future: ORM / model classes]
│
├── scratch/
│   └── [Development & debugging scripts — not for production]
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
// Searches assets/images/ and assets/images/institutions/
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
