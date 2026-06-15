# Maslaki Project — File Structure

```
Maslaki-projet/
│
├── .env                              ← Runtime credentials (DB, OAuth) — NOT committed
├── .env.example                      ← Credential template — safe to commit
├── .gitignore                        ← Ignores .env, logs, desktop.ini
├── USAGE_REPORT.md                   ← Feature usage report
│
├── index.php                         ← Landing page: hero, stats, popular schools
├── login_process.php                 ← POST: authenticate student
├── register_process.php              ← POST: register new student
├── google_callback.php               ← Google OAuth 2.0 handler
├── ai_process.php                    ← AI orientation results page
├── submit_review.php                 ← POST: submit school review (CSRF-protected)
├── process_appointment.php           ← POST: create or delete appointment (CSRF-protected)
├── process_add_institution.php       ← POST: add new institution (admin/superadmin, CSRF-protected)
├── chatbot.php                       ← Chatbot API endpoint
├── test_gemini.php                   ← Gemini API test/debug page
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
│   ├── chatbot.php                   ← Chatbot rendering + logic
│   └── platform_admin.php            ← is_platform_admin(), require_platform_admin()
│
│
├── services/
│   └── GeminiService.php             ← Google Gemini AI API integration with
│                                         retry, multi-model fallback
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
│   │                                     bac requirements, image gallery
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
│   ├── admin_dashboard.php           ← Platform management overview
│   ├── admin_reviews.php             ← Approve/reject pending reviews (shows
│   │                                     star ratings); POST+CSRF only
│   ├── admin_send_notification.php   ← Broadcast notifications
│   ├── admin_add_institution.php     ← Add new institution form (admin/superadmin)
│   └── admin_users_manage.php        ← User role management (superadmin only)
│
│
├── assets/
│   ├── css/
│   │   ├── style.css                 ← Design system (Navy + Orange, dark mode,
│   │                                     responsive, card components)
│   │   └── chatbot.css               ← Chatbot widget styles
│   ├── js/
│   │   ├── script.js                 ← Client-side logic
│   │   └── chatbot.js                ← Chatbot frontend JS
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
│   └── main_database_schema.sql      ← Single source of truth: all 25 tables,
│                                         PKs, FKs, UNIQUE indexes, and seed data
│
│
├── Rapport de PFE/
│   └── (project report documents)
│
└── md/
    ├── structure.md                  ← This file
    ├── progress.md                   ← Feature tracker + changelog
    ├── how_it_works.md               ← Detailed code walkthrough
    ├── workflow.md                   ← Development workflow guide
    ├── cahier_des_charges.md         ← Project specification document
    ├── mcd.md                        ← Conceptual data model (MCD)
    ├── mld.md                        ← Logical data model (MLD)
    ├── mcd_mld.md                    ← Combined MCD/MLD reference
    ├── class_diagram.md              ← UML class diagram
    └── use_case_diagram.md           ← UML use case diagram
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
