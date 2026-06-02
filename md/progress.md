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
| `reviews` | ✅ | Submit, approve, display |
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

### Security & Infrastructure (NEW)
1. **Environment variables** — DB credentials & OAuth keys moved to `.env` ✔
2. **CSRF protection** — All POST endpoints protected with tokens ✔
3. **XSS protection** — Notification rendering sanitized (DOM-based) ✔
4. **Shared helpers** — Auth guards, JSON responses, image resolver, localization ✔
5. **Request-time migrations removed** — Schema changes must run via migration files ✔
6. **`.gitignore`** — Prevents committing secrets (.env, logs) ✔

### Authentication
7. Student registration + duplicate email check ✔
8. Student login + hashed password verify ✔
9. **Google OAuth 2.0** — Login via Google account ✔
10. Logout (session destroy) ✔
11. Session protection on all private pages ✔
12. **Auth helpers** — `require_auth()`, `is_logged_in()`, `current_user_id()` ✔

### Multilingual Support
13. **French/Arabic localization** — All pages support lang switching ✔
14. **Database field localization** — `name`, `description`, etc. have `_ar` variants ✔
15. **Translation system** — `includes/translations.php` + `lang_helper.php` ✔
16. **Localized notifications** — Messages displayed in user's language ✔

### Pages
17. Landing page — hero + stats bar (dynamic counts from DB) ✔
18. Dashboard — welcome + nav cards + upcoming deadlines + contests ✔
19. Institutions list — card grid with filters (city, category, domain, bac, type) ✔
20. Institution detail page — full info + reviews + sub-schools + filieres ✔
21. Saved schools page — with remove button ✔
22. AI form — bac dropdown + average + city ✔
23. AI results — smart filtering + save button ✔
24. **Orientation explorer** — Category → Domain → Filiere navigation ✔
25. **Domain details** — Shows filieres + institutions offering them ✔
26. **Filiere details** — Program info + institutions offering it ✔
27. **Contests page** — List with registration deadlines + exam dates ✔
28. **Notifications page** — User notification center ✔
29. Admin reviews panel — approve/reject ✔
30. **Admin notification sender** — Broadcast to all users or specific user ✔
31. **Admin dashboard** — Platform management portal ✔

### Features
32. Save school (duplicate prevention) ✔
33. Remove saved school (confirm dialog) ✔
34. "Already Saved" indicator on institution cards ✔
35. Save button hidden from guests ✔
36. **AJAX search** — `search_ajax.php` with advanced filters ✔
37. **Live search** — debounced input + instant filters ✔
38. **Domain-based filtering** — Select category → load domains → filter ✔
39. AI recommendations — bac→type mapping + average filter + city priority ✔
40. AI results saved to `ai_recommendations` table ✔
41. Deadlines on institution cards (color-coded urgency) ✔
42. Upcoming deadlines on dashboard (for saved schools) ✔
43. Reviews — submit (1 per user per school, pending status) ✔
44. Reviews — admin approve/reject panel ✔
45. Reviews — display approved reviews on detail page ✔
46. **Real-time notifications** — Polling every 30s, toast popups ✔
47. **Mark all read / delete notifications** ✔
48. **Notification badges** — Unread count on bell icon ✔
49. **Institution image resolver** — Smart fallback system for missing images ✔
50. **Platform admin role** — Access control via `organizer_staff` table ✔

### Design (Navy + Orange)
51. Complete CSS redesign matching Maslaki brand ✔
52. **Dark mode support** — Theme toggle in header ✔
53. Shared header/footer with session-aware navbar ✔
54. Responsive design — Mobile, tablet, desktop ✔
55. **Card-based layouts** — Consistent UI patterns ✔

---

## 🔒 Security Improvements (2026-06-02)

### Recently Completed
- ✅ **Environment configuration** — Database & OAuth credentials in `.env`
- ✅ **XSS mitigation** — Sanitized notification rendering (lines 260, 326 in header.php)
- ✅ **Request-time migrations removed** — From `google_callback.php`, `views/institutions.php`
- ✅ **Shared helper library** — `includes/helpers.php` with auth, JSON, localization utilities
- ✅ **Refactored endpoints** — `save_school.php`, `remove_school.php`, `get_notifications.php`

---

## ❌ Recommended Future Enhancements

1. **Input validation** — Sanitize user inputs in forms before DB insertion
2. **Rate limiting** — Prevent brute force on login, notification spam
3. **Password reset** — Email-based password recovery flow
4. **Email verification** — Confirm email on registration
5. **SQL injection audit** — Review all raw queries, ensure prepared statements everywhere
6. **File upload security** — If adding avatar uploads, validate types/sizes
7. **Session fixation protection** — Regenerate session ID on login
8. **Logging** — Audit trail for admin actions, login attempts
9. **Pagination** — Institutions list (currently loads all)
10. **Admin role system** — More granular permissions beyond platform_admin boolean

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
9. ~~404 on XAMPP~~ → Junction link created ✔
10. ~~Hardcoded DB credentials~~ → Moved to `.env` ✔
11. ~~Request-time migrations~~ → Removed, use migration files only ✔
12. ~~Duplicate image resolver functions~~ → Extracted to `helpers.php` ✔

---

## 📁 Final File Structure

```
Maslaki-projet/
├── .env                         ← Environment variables (DO NOT COMMIT)
├── .env.example                 ← Template for .env
├── .gitignore                   ← Prevents committing secrets
├── index.php                    ← Landing page (hero + stats)
├── login_process.php            ← Login backend
├── register_process.php         ← Register backend
├── google_callback.php          ← Google OAuth handler
├── save_school.php              ← Save school backend (refactored)
├── remove_school.php            ← Remove saved school (refactored)
├── search_ajax.php              ← AJAX search endpoint
├── ai_process.php               ← AI results page
├── submit_review.php            ← Review submission backend
├── get_notifications.php        ← Notifications API (refactored)
├── mark_notification_read.php   ← Mark read API
├── delete_notification.php      ← Delete notification API
├── check_new_notifications.php  ← Check unread count API
├── get_domains.php              ← Domain filter API
├── process_appointment.php      ← Appointment handler
├── migrate.php                  ← Manual migration runner
│
├── config/
│   ├── DataBase.php             ← PDO connection (reads .env)
│   └── google_config.php        ← Google OAuth config (reads .env)
│
├── views/
│   ├── login.php                ← Login form
│   ├── register.php             ← Register form
│   ├── dashboard.php            ← User dashboard
│   ├── institutions.php         ← School list + search (refactored)
│   ├── institution_detail.php   ← School detail + reviews
│   ├── saved_schools.php        ← Saved schools list
│   ├── ai_form.php              ← AI orientation form
│   ├── orientation_explore.php  ← Category/domain explorer
│   ├── domain_details.php       ← Domain detail page
│   ├── filiere_details.php      ← Filiere detail page
│   ├── contests.php             ← Contest list
│   ├── notifications.php        ← Notification center
│   ├── appointments.php         ← Appointment management
│   ├── admin_reviews.php        ← Admin review panel
│   ├── admin_send_notification.php ← Admin notification sender
│   ├── admin_dashboard.php      ← Platform admin panel
│   └── logout.php               ← Logout
│
├── assets/
│   ├── css/style.css            ← Full design system
│   ├── js/script.js             ← Client-side logic
│   └── images/
│       ├── institutions/        ← School logos (107 files)
│       ├── logo.png
│       └── students_illustration.png
│
├── includes/
│   ├── header.php               ← Shared navbar (XSS-safe notifications)
│   ├── footer.php               ← Shared footer
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
