# Maslaki Project — Progress Tracker

---

## 📊 Database Schema

| Table | Used in Code? | Status |
|-------|---------------|--------|
| `students` | ✅ | Registration, login, sessions, OAuth |
| `institutions` | ✅ | List, detail, search, AI, filters |
| `saved_schools` | ✅ | Save, remove, dashboard count |
| `ai_recommendations` | ✅ | Stores AI search results |
| `deadlines` | ✅ | Shown on cards + dashboard |
| `reviews` | ✅ | Submit (with rating), approve, display |
| `notifications` | ✅ | System, school, contest announcements |
| `user_notifications` | ✅ | Per-user read/deleted state |
| `contests` | ✅ | Contest list with deadlines |
| `villes` | ✅ | City filter, localization |
| `categories` | ✅ | Category/sector filter |
| `domains` | ✅ | Domain filter, orientation |
| `filieres` | ✅ | Program details, orientation |
| `bac_types` | ✅ | Bac type filter |
| `organizer_staff` | ✅ | Platform admin access control |

---

## ✅ Completed Features

### Security & Infrastructure
1. **Environment variables** — DB credentials & OAuth keys moved to `.env` ✔
2. **CSRF protection** — All POST endpoints protected with tokens ✔
3. **XSS protection** — Notification rendering sanitized (DOM-based, no innerHTML) ✔
4. **Shared helpers** — `includes/helpers.php`: auth guards, JSON responses, image resolver, localization ✔
5. **Request-time migrations removed** — Schema changes run via migration files only ✔
6. **`.gitignore`** — Prevents committing `.env`, logs, `desktop.ini` ✔
7. **GET→POST hardening** — Review approve/reject, appointment delete, saved-school remove all use POST+CSRF ✔

### Authentication
8. Student registration + duplicate email check ✔
9. Student login + hashed password verify ✔
10. **Google OAuth 2.0** — Login via Google account ✔
11. Logout (session destroy) ✔
12. Session protection on all private pages ✔
13. **Auth helpers** — `require_auth()`, `is_logged_in()`, `current_user_id()` ✔

### Multilingual Support
14. **French/Arabic/English localization** — `lang/fr.php`, `lang/ar.php`, `lang/en.php` ✔
15. **Database field localization** — `name`, `description`, etc. have `_ar` / `_en` variants ✔
16. **Translation system** — `includes/lang_helper.php` + `__()` function ✔
17. **Localized notifications** — Messages displayed in user's language ✔

### Pages
18. Landing page — hero + stats bar (dynamic counts from DB) ✔
19. Dashboard — welcome + nav cards + upcoming deadlines + contests ✔
20. Institutions list — card grid with filters (city, category, domain, bac, type) ✔
21. Institution detail page — full info + reviews + sub-schools + filieres ✔
22. Saved schools page — with remove button ✔
23. AI form — bac dropdown + average + city ✔
24. AI results — smart filtering + save button ✔
25. **Orientation explorer** — Category → Domain → Filiere navigation ✔
26. **Domain details** — Shows filieres + institutions offering them ✔
27. **Filiere details** — Program info + institutions offering it ✔
28. **Contests page** — List with registration deadlines + exam dates ✔
29. **Notifications page** — User notification center ✔
30. **Appointments page** — Book + list + delete appointments ✔
31. Admin reviews panel — approve/reject + star rating display ✔
32. **Admin notification sender** — Broadcast to all users or specific user ✔
33. **Admin dashboard** — Platform management portal ✔

### Features
34. Save school (duplicate prevention) ✔
35. Remove saved school (confirm dialog) ✔
36. "Already Saved" indicator on institution cards ✔
37. Save button hidden from guests ✔
38. **AJAX search** — `search_ajax.php` with advanced filters ✔
39. **Live search** — debounced input + instant filters ✔
40. **Domain-based filtering** — Select category → load domains → filter ✔
41. AI recommendations — bac→type mapping + average filter + city priority ✔
42. AI results saved to `ai_recommendations` table ✔
43. Deadlines on institution cards (color-coded urgency) ✔
44. Upcoming deadlines on dashboard (for saved schools) ✔
45. Reviews — submit with star rating (1–5), 1 per user per school, pending status ✔
46. Reviews — admin approve/reject with star display ✔
47. Reviews — display approved reviews with avatar initials, relative timestamps, star display ✔
48. **Real-time notifications** — Polling every 30s, toast popups ✔
49. **Mark all read / delete notifications** ✔
50. **Notification badges** — Unread count on bell icon ✔
51. **Institution image resolver** — Smart fallback system for missing images ✔
52. **Platform admin role** — Access control via `organizer_staff` table ✔

### Design (Navy + Orange)
53. Complete CSS redesign matching Maslaki brand ✔
54. **Dark mode support** — Theme toggle in header ✔
55. Shared header/footer with session-aware navbar ✔
56. Responsive design — Mobile, tablet, desktop ✔
57. **Reviews UI** — Compose box with star rating widget, avatar initials, relative timestamps, "Utile" button ✔

---

## 🔒 Security Fixes Applied

| Date | Fix |
|------|-----|
| 2026-06-02 | DB & OAuth credentials moved to `.env` |
| 2026-06-02 | XSS: notification rendering rewritten to use DOM APIs |
| 2026-06-02 | Request-time migrations removed from `google_callback.php`, `views/institutions.php` |
| 2026-06-02 | Shared helper library `includes/helpers.php` extracted |
| 2026-06-02 | `save_school.php`, `remove_school.php`, `get_notifications.php` refactored to use helpers |
| 2026-06-02 | GET→POST: admin review approve/reject dead GET code removed |
| 2026-06-02 | GET→POST: appointment delete converted from `?delete=id` link to POST form with CSRF |
| 2026-06-02 | `submit_review.php` — CSRF added, `require_method` replaced with redirect-safe check |
| 2026-06-02 | `reviews` table — `add_review_rating.sql` migration created for `rating` column |

---

## ❌ Recommended Future Enhancements

1. **Input validation** — Sanitize user inputs in forms before DB insertion
2. **Rate limiting** — Prevent brute force on login, notification spam
3. **Password reset** — Email-based password recovery flow
4. **Email verification** — Confirm email on registration
5. **Session fixation protection** — Regenerate session ID on login
6. **Logging** — Audit trail for admin actions, login attempts
7. **Pagination** — Institutions list (currently loads all)
8. **Admin role system** — More granular permissions beyond platform_admin boolean
9. **"Utile" votes** — Wire the helpful button on reviews to a DB votes table

---

## 🐞 All Bugs Fixed

1. ~~`save_school.php` dead-end~~ → Redirects ✔
2. ~~`register_process.php` dead-end~~ → Redirects to login ✔
3. ~~`login_process.php` dead-end~~ → Redirects with error ✔
4. ~~No XSS protection~~ → `htmlspecialchars()` + DOM sanitization ✔
5. ~~`save_school.php` no validation~~ → Validates + prevents duplicates ✔
6. ~~`index.php` test page~~ → Real landing page ✔
7. ~~No navigation~~ → Shared navbar ✔
8. ~~`ai_process.php` ignores input~~ → Smart filtering ✔
9. ~~Hardcoded DB credentials~~ → Moved to `.env` ✔
10. ~~Request-time migrations~~ → Removed, use migration files only ✔
11. ~~Duplicate image resolver functions~~ → Extracted to `helpers.php` ✔
12. ~~GET-based state changes~~ → Appointment delete, review actions all POST+CSRF ✔
13. ~~`submit_review.php` used `require_method()` which sent JSON on non-POST~~ → Replaced with redirect ✔
14. ~~`reviews` table missing `rating` column~~ → `add_review_rating.sql` migration added ✔

---

## 📁 Root-Level Files

| File | Purpose |
|------|---------|
| `.env` | Runtime credentials — never committed |
| `.env.example` | Credential template |
| `.gitignore` | Excludes `.env`, logs, `desktop.ini` |
| `index.php` | Landing page |
| `login_process.php` | Login backend |
| `register_process.php` | Register backend |
| `google_callback.php` | Google OAuth handler |
| `ai_process.php` | AI orientation results |
| `submit_review.php` | Review submission (POST+CSRF) |
| `process_appointment.php` | Appointment create + delete (POST+CSRF, `action` field) |
| `migrate.php` | Manual migration runner |
| `save_school.php` | Toggle saved school (POST/AJAX) |
| `remove_school.php` | Remove saved school (POST/AJAX) |
| `search_ajax.php` | AJAX institution search |
| `get_notifications.php` | Notifications JSON API |
| `check_new_notifications.php` | Unread count JSON API |
| `mark_notification_read.php` | Mark read JSON API |
| `delete_notification.php` | Soft-delete notification API |
| `get_domains.php` | Domains filter JSON API |

---

## 🚀 Setup Instructions

1. **Copy project** to web server directory (XAMPP `htdocs/` etc.)
2. **Copy `.env.example` → `.env`** and fill in:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
   - `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`
3. **Import database** — run `database/maslaki_full_database.sql` in phpMyAdmin
4. **Run the rating migration** — run `database/add_review_rating.sql`
5. **Run other migrations as needed** — files in `database/` named `add_*`, `fix_*`, `ensure_*`
6. **Configure OAuth** — set the redirect URI in Google Cloud Console

---

## 📦 Dependencies

- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- PDO + PDO_MySQL extension
- cURL extension (for Google OAuth)
- Apache with `mod_rewrite` or Nginx equivalent

---

*Last updated: 2026-06-09*
│   ├── helpers.php              ← NEW: Shared utilities
│   ├── csrf.php                 ← CSRF token generation/validation
│   ├── lang_helper.php          ← Localization helpers
│   ├── translations.php         ← Translation strings
│   └── platform_admin.php       ← Admin role checks
│
├── lang/
│   └── [Translation files]
│
├── database/
│   ├── maslaki.sql              ← Base schema
│   ├── maslaki_full_database.sql ← Complete DB dump
│   ├── seed_deadlines.sql       ← Deadline seed data
│   ├── seed_real_contests.sql   ← Contest seed data
│   ├── notifications_setup.sql  ← Notification tables
│   ├── [30+ migration files]
│   └── update_institutions_info.php
│
├── admin/
│   └── admin_migration.sql      ← Admin table setup
│
├── models/
│   └── [Future model classes]
│
├── scratch/
│   └── [Development/testing files]
│
└── md/
    ├── structure.md             ← This file
    ├── progress.md              ← Feature tracker
    └── workflow.md              ← Development workflow
```

---

## 🚀 Setup Instructions

1. **Clone/copy project** to web server directory
2. **Copy `.env.example` to `.env`** and fill in:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
   - `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`
3. **Import database** — `database/maslaki_full_database.sql` in phpMyAdmin
4. **Run migrations** — Visit `migrate.php` if needed
5. **Set permissions** — Ensure `.env` is not web-accessible (ideally outside document root)
6. **Configure OAuth** — Set redirect URI in Google Cloud Console

---

## 📦 Dependencies

- PHP 7.4+ (8.0+ recommended)
- MySQL 5.7+ / MariaDB 10.3+
- PDO extension
- cURL extension (for Google OAuth)
- Apache/Nginx with mod_rewrite

---

*Last updated: 2026-06-02 — Security hardening complete*
