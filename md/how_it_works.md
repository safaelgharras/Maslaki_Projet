# Maslaki — How The Project Works (Full Code Walkthrough)

> A deep line-by-line explanation of every major file, flow, and system.
> No code was changed — this is a read-only analysis.

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [File / Folder Structure](#2-file--folder-structure)
3. [Boot Sequence — What Happens On Every Request](#3-boot-sequence--what-happens-on-every-request)
4. [Database Connection — `config/DataBase.php`](#4-database-connection--configdatabasephp)
5. [Language System — `includes/lang_helper.php`](#5-language-system--includeslang_helperphp)
6. [Header & Navigation — `includes/header.php`](#6-header--navigation--includesheaderphp)
7. [Footer & Chatbot Loading — `includes/footer.php`](#7-footer--chatbot-loading--includesfooterphp)
8. [Home Page — `index.php`](#8-home-page--indexphp)
9. [Registration Flow](#9-registration-flow)
10. [Login Flow](#10-login-flow)
11. [Dashboard (Student) — `views/dashboard.php`](#11-dashboard-student--viewsdashboardphp)
12. [Institutions Listing — `views/institutions.php`](#12-institutions-listing--viewsinstitutionsphp)
13. [AJAX Search Engine — `search_ajax.php`](#13-ajax-search-engine--search_ajaxphp)
14. [Institution Detail Page — `views/institution_detail.php`](#14-institution-detail-page--viewsinstitution_detailphp)
15. [AI Orientation — `views/ai_form.php` + `ai_process.php`](#15-ai-orientation--viewsai_formphp--ai_processphp)
16. [Gemini AI Service — `services/GeminiService.php`](#16-gemini-ai-service--servicesgeminiservicephp)
17. [Admin System](#17-admin-system)
18. [Notification System](#18-notification-system)
19. [CSRF Protection — `includes/csrf.php`](#19-csrf-protection--includescsrfphp)
20. [Save / Remove School](#20-save--remove-school)
21. [Database Schema Summary](#21-database-schema-summary)

---

## 1. Project Overview

**Maslaki** is a Moroccan higher-education orientation platform. Students can:
- Browse 92 institutions with filters (city, category, domain, bac type, institution type)
- Get AI-powered school recommendations based on their bac branch and grade average
- Save favourite schools to a personal list
- Read and write reviews for institutions
- Receive real-time notifications

Admins can:
- Moderate reviews
- Send platform-wide or targeted notifications
- Add new institutions
- Manage user roles (superadmin only)

**Tech stack:** PHP 8+ (no framework), MariaDB/MySQL, vanilla JavaScript, CSS custom properties for theming.

---

## 2. File / Folder Structure

```
Maslaki_Projet/
├── index.php                  ← Home page (entry point)
├── .env                       ← Secret credentials (DB, Gemini API key)
├── config/
│   └── DataBase.php           ← PDO connection, reads .env
├── includes/
│   ├── header.php             ← HTML <head>, navbar, JS dropdowns, theme, notifications
│   ├── footer.php             ← </main>, footer HTML, conditional chatbot loader
│   ├── lang_helper.php        ← Language selection, __() translation function
│   ├── csrf.php               ← CSRF token generation & verification
│   ├── platform_admin.php     ← Role guards (require_platform_admin, require_superadmin)
│   ├── helpers.php            ← Misc helpers
│   └── chatbot.php            ← Chatbot widget HTML + JS
├── lang/
│   ├── fr.php                 ← French translation array
│   ├── en.php                 ← English translation array
│   └── ar.php                 ← Arabic translation array
├── views/
│   ├── login.php              ← Login form UI
│   ├── register.php           ← Registration form UI
│   ├── dashboard.php          ← Student profile page
│   ├── institutions.php       ← Institution listing + AJAX filters
│   ├── institution_detail.php ← Single institution full details
│   ├── ai_form.php            ← AI orientation input form
│   ├── orientation_explore.php← Browse by domain/category
│   ├── saved_schools.php      ← Student's saved list
│   ├── notifications.php      ← Full notifications page
│   ├── appointments.php       ← Book appointment
│   ├── contests.php           ← View contests/exams
│   ├── admin_dashboard.php    ← Admin tools landing
│   ├── admin_reviews.php      ← Review moderation
│   ├── admin_send_notification.php
│   ├── admin_add_institution.php
│   └── admin_users_manage.php ← Superadmin only: role management
├── services/
│   └── GeminiService.php      ← Google Gemini API wrapper
├── search_ajax.php            ← AJAX endpoint for institution search
├── login_process.php          ← Handles POST login
├── register_process.php       ← Handles POST registration
├── ai_process.php             ← Handles AI recommendation POST + results
├── save_school.php            ← Save a school to favourites
├── remove_school.php          ← Remove a school from favourites
├── submit_review.php          ← Submit institution review
├── get_notifications.php      ← AJAX: fetch notification list
├── check_new_notifications.php← AJAX: poll for new notifications
├── mark_notification_read.php ← AJAX: mark notification read
├── delete_notification.php    ← AJAX: delete notification
├── get_domains.php            ← AJAX: get domains for a category
└── assets/
    ├── css/style.css          ← All styles + dark mode CSS vars
    ├── js/script.js           ← Shared JS (save toggle, etc.)
    └── images/institutions/   ← School logo images
```

---

## 3. Boot Sequence — What Happens On Every Request

Every PHP page follows this exact boot order:

```
1. require "includes/lang_helper.php"
   → starts session if not started
   → reads ?lang= GET param or $_SESSION['lang'] or cookie
   → loads the correct lang/fr.php / lang/en.php / lang/ar.php
   → defines __($key) translation function

2. $pageTitle = __('some_key')   ← set before header so <title> gets it

3. require "includes/header.php"
   → starts session (if not already)
   → re-requires DataBase.php (safe: PDO already set)
   → detects $base path ('' if root, '../' if inside /views/)
   → queries unread notification count for the logged-in user
   → queries the user's role/name/avatar
   → outputs full HTML: <head>, <link>, theme script, navbar
   → opens <main class="main-content">

4. Page content runs here (forms, queries, loops)

5. require "includes/footer.php"
   → closes </main>
   → outputs the <footer>
   → loads assets/js/script.js
   → conditionally loads chatbot only on dashboard + AI pages
   → closes </body></html>
```

---

## 4. Database Connection — `config/DataBase.php`

```
Line 11-31: Load .env file
  - Opens project-root/.env
  - Reads every line, skips # comments
  - Splits on first '=' to get key=value
  - Only sets env vars that aren't already set (so production env vars win)
  - Calls putenv(), $_ENV[], $_SERVER[] for maximum compatibility

Line 33-36: Read credentials from environment
  - DB_HOST  → defaults to 'localhost'
  - DB_NAME  → defaults to '' (triggers die() if empty)
  - DB_USER  → defaults to ''
  - DB_PASS  → defaults to ''

Line 38-40: Guard — if DB_NAME is empty, stop with a config error message

Line 42-54: Create PDO connection
  - DSN: "mysql:host=...;dbname=...;charset=utf8mb4"
  - ERRMODE_EXCEPTION: any DB error throws a PDOException (no silent failures)
  - EMULATE_PREPARES=false: use native prepared statements (safer against SQL injection)
  - On failure: logs the error internally, shows a safe generic message to the user
```

The result is a `$pdo` variable available globally in every file that `require`s this.

---

## 5. Language System — `includes/lang_helper.php`

**Language Priority Chain:**
```
?lang=fr GET param → writes to session + cookie
→ $_SESSION['lang']   (set by previous request)
→ $_COOKIE['lang']    (set 30 days ago)
→ fallback: 'fr'
```

**Key functions:**

```php
__($key)
  // Looks up $key in the loaded $translations array
  // If not found, returns the key itself (so missing keys are visible)
  // Example: __('institutions') → "Établissements" (in fr) or "Institutions" (in en)

getLang()
  // Returns current language code: 'fr', 'en', or 'ar'
  // Used by header.php to set html[lang] and html[dir=rtl]

isRTL()
  // Returns true if lang is 'ar' (for Right-To-Left layout)

getLocalizedDbField($row, $field)
  // Reads multilingual DB columns
  // If lang='ar' and $row['city_ar'] is not empty → return city_ar
  // If lang='en' and $row['city_en'] is not empty → return city_en
  // Otherwise return $row['city'] (French/default value)
  // This is used everywhere institution data is displayed

formatLocalizedDate($dateStr)
  // Converts '2026-07-15' → '15 Juillet 2026' (fr) or '15 July 2026' (en)
  // Uses __('month_7') type translation keys
```

---

## 6. Header & Navigation — `includes/header.php`

**PHP section (lines 1-48):**
```
Line 2-4:  Start session if not started

Line 10:   Detect $base path
  - If URL contains '/views/', set $base = '../'
  - Otherwise $base = ''
  - This lets header.php work both from root and from /views/

Line 14-48: If user is logged in ($_SESSION['user_id'] set):
  - Run a LEFT JOIN query to count unread notifications:
    SELECT COUNT(*) FROM notifications n
    LEFT JOIN user_notifications un ON n.id = un.notification_id AND un.user_id = ?
    WHERE (n.is_global = 1 OR n.target_user_id = ?)
    AND (un.is_read IS NULL OR un.is_read = 0)
    AND (un.is_deleted IS NULL OR un.is_deleted = 0)
  - Run SELECT role, name, avatar FROM students WHERE id = ? to get user info
  - Build $initials from user name (first 2 word initials, uppercase)
  - $isPlatformAdmin = true if role is 'admin' OR 'superadmin'
  - $isSuperAdminNav = true only if role is 'superadmin'
```

**HTML output (lines 51-221):**
```
- Sets html[lang] and html[dir] based on getLang()
- Injects a <script> that immediately reads localStorage('theme') and sets
  data-theme on <html> — this runs BEFORE the body renders, preventing a
  flash of wrong theme (light/dark flicker)
- Navbar links:
  * Always shown: Home, Institutions, Orientation
  * Logged in only: AI Orientation button (orange pill), Notification bell, Profile icon
  * Logged out only: Login link, Register button
- Admin/Superadmin dropdown item shown only if $isPlatformAdmin is true
- Notification badge shows unread count from PHP query above
- Profile icon shows: avatar image > name initials > fallback emoji 👤
```

**JavaScript section (lines 223-506):**
```
- checkNotifications() — fetches check_new_notifications.php every 30s
  * Updates badge count
  * Shows toast popup if a new notification arrived since last visit
  * Stores lastNotifId in localStorage to avoid re-showing same toast

- showToast(notif) — creates a styled toast notification div
  * Uses DOM creation (not innerHTML) to prevent XSS
  * Auto-removes after 6 seconds
  * Has a manual close button

- loadNotifications() — fetches get_notifications.php when bell is clicked
  * Builds notification items safely with DOM API (no innerHTML for content)
  * Shows title, truncated message (60 chars), time_ago

- markAllRead — calls mark_notification_read.php?all=1
  * Fades out and removes the badge with CSS transition

- Profile dropdown — toggle on click, close on outside click

- Theme switching — themeLightBtn / themeDarkBtn
  * Sets data-theme on <html>
  * Saves to localStorage
  * Initialises from localStorage on page load

- Language change — smooth fade-out animation then redirect with ?lang=xx

- Mobile menu — toggles .mobile-active class on .nav-links
```

---

## 7. Footer & Chatbot Loading — `includes/footer.php`

```
Line 1:     Closes </main> (opened in header.php line 509)

Lines 3-41: Renders the <footer> with:
  - Maslaki logo + description
  - Navigation links column
  - Resources column (static links to # for now)
  - Contact column (email + city)
  - Footer bottom with dynamic year: date('Y')

Line 126:   Loads assets/js/script.js (shared JS for save button, etc.)

Lines 128-143: Conditional chatbot loader
  - Gets current page filename from $_SERVER['PHP_SELF']
  - Chatbot is ONLY loaded on: dashboard.php, ai_form.php, ai_process.php
  - On dashboard.php: $chatbotContext = 'profile'
  - On AI pages: $chatbotContext = 'orientation'
  - require_once chatbot.php to inject the widget
  - This keeps the chatbot off all other pages (lighter page load)
```

---

## 8. Home Page — `index.php`

```
Line 2:  require config/DataBase.php → creates $pdo

Lines 5-11: Fetch stats for the counter cards
  - $schoolCount = COUNT(*) FROM institutions
  - $cityCount = COUNT(DISTINCT ville_id) FROM institutions
    → if 0, fallback to COUNT(DISTINCT city) (covers old data without ville_id)
  - $typeCount = COUNT(DISTINCT type) FROM institutions
  - All inside try/catch so the page never crashes if DB is down

Lines 14-17: Load language + set page title + load header

Lines 20-56: Hero Slider
  - Fetches Solicode image specifically (by name LIKE '%solicode%')
    because it's the partner school and should always appear
  - Then fetches up to 4 more is_popular=1 schools (excluding Solicode)
  - Fallback: if still under 5 images, fills with random schools
  - Outputs <div class="hero-slide"> for each image with inline background-image

Lines 117-129: Hero slider JavaScript
  - Runs setInterval every 5000ms
  - Removes 'active' from current slide, adds it to next (cycling)
  - The CSS transition: opacity 1.5s + transform scale(1.05→1) creates
    the ken-burns zoom effect

Lines 132-156: Stats Section
  - Outputs the 3 counter cards using the PHP variables from lines 5-11
  - schoolCount + "+" displayed as stat-number

Lines 158-202: Popular Schools Section
  - Checks if is_popular column exists (try/catch guards against old schema)
  - Fetches 3 popular schools
  - Displays each as a card with localized name/city via getLocalizedDbField()
```

---

## 9. Registration Flow

**`views/register.php`** — the HTML form. Sends POST to `../register_process.php`.

**`register_process.php`** line by line:
```
Line 5:  Only runs on POST requests
Line 7-12: Reads: name, email, password, bac_branch, average, city
Line 15-21: Duplicate email check
  - SELECT id FROM students WHERE email = ?
  - If found → redirect back to register.php?error=error_email_exists
Line 23: Hash the password with PHP's password_hash (bcrypt)
Line 25-29: INSERT INTO students with all 6 fields (no role — defaults to 'student')
Line 31: Redirect to login.php?success=success_registration
```

---

## 10. Login Flow

**`views/login.php`** — the HTML form. Sends POST to `../login_process.php`.

**`login_process.php`** line by line:
```
Line 2:  session_start() explicitly (this file doesn't use header.php)
Line 3-4: Load lang helper + database
Line 6:  Only runs on POST
Line 8-9: Read email (trimmed) and password (NOT trimmed — passwords can have spaces)
Line 11-13: SELECT * FROM students WHERE email = ?
  - Uses prepared statement — email is never concatenated into SQL
Line 15:  fetch() — gets the student row or false
Line 17: password_verify($inputPassword, $hashedPasswordFromDB)
  - bcrypt verification — works even if the hash was created with different cost
Line 19-20: On success: set $_SESSION['user_id'] and $_SESSION['user_name']
Line 22: Redirect to views/dashboard.php
Line 26: On failure: redirect to login.php?error=error_invalid_credentials
  - Same error message for wrong email AND wrong password (security: no enumeration)
```

**Note:** The login does NOT set `$_SESSION['user_avatar']`. The header.php falls back to reading avatar from DB on each page load. The session stores `user_avatar` only if set explicitly (e.g. after Google OAuth).

---

## 11. Dashboard (Student) — `views/dashboard.php`

```
Line 2-4:  Load lang + set title + load header
  - header.php will detect user_id in session and show nav avatar

Line 7-10: Auth guard — if NOT logged in, redirect to login.php
  - This is the student-facing guard (header.php already showed the nav,
    but this prevents the page content from rendering)

Line 12:   $userId = $_SESSION['user_id']

Lines 15-17: Saved schools count
  - SELECT COUNT(*) FROM saved_schools WHERE student_id = $userId

Lines 20: Total institutions count
  - SELECT COUNT(*) FROM institutions

Lines 22-36: Upcoming deadlines
  - JOIN: saved_schools → institutions → deadlines
  - WHERE s.student_id = ? AND d.deadline_date >= CURDATE()
  - ORDER BY deadline_date ASC LIMIT 5
  - Shows only deadlines for schools the user has saved
  - Wrapped in try/catch (deadlines table might be empty)

Lines 43-55: Banner section
  - Greeting: "Bonjour, [name]" using $_SESSION['user_name']
  - Shows $savedNum and hardcoded "92%" profile completion

Lines 64-99: Quick Link cards (4 cards: institutions, saved, AI, contests)

Lines 106-131: Featured contests
  - SELECT c.*, i.name FROM contests c JOIN institutions i
    ON c.institution_id = i.id WHERE c.is_featured = 1 LIMIT 3
  - Shows status badge (open/closed/soon) with translation
  - Shows exam_date and registration_deadline using formatLocalizedDate()

Lines 133-153: Upcoming deadlines (only if count > 0)
  - Each card shows day number + month abbreviation in a calendar-style box

Lines 163-188: Sidebar
  - Appointment booking promo card (link to appointments.php)
  - Last 5 notifications (global + targeted)
    SELECT * FROM notifications WHERE (target_user_id = ? OR is_global = 1)
    ORDER BY created_at DESC LIMIT 5
```

---

## 12. Institutions Listing — `views/institutions.php`

This page has **two data layers**: initial PHP render + AJAX dynamic updates.

**PHP layer (server-side, lines 1-117):**
```
Lines 9-26: Fetch filter metadata
  - $villes: SELECT * FROM villes ORDER BY nom ASC
    → each city name run through getLocalizedDbField() for translation
  - $categories: SELECT * FROM categories ORDER BY nom ASC
    → same localization

Lines 28-36: Fetch types and bac types for filter dropdowns

Lines 38: $isLoggedIn = isset($_SESSION['user_id'])

Lines 40-57: Initial institution data load
  - SELECT * FROM institutions ORDER BY (id=131) DESC, is_popular DESC, name ASC
  - id=131 is Solicode (pinned to top)
  - Each institution localized: name, description, city, diplome, duree_etudes

Lines 61-64: If logged in, fetch saved IDs
  - SELECT institution_id FROM saved_schools WHERE student_id = [id]
  - Used to pre-highlight saved ❤ buttons

Lines 66-116: resolveInstitutionImagePath($name, $dbImage)
  - Priority: DB image path → special name mappings → name.webp → name.png → name.jpg → default
  - Checks actual file_exists() on disk to find the correct format/case
  - URL-encodes spaces for browser safety

Lines 108-116: translateType($type)
  - Builds key like 'type_engineering' and calls __() on it
  - Falls back to original string if no translation found
```

**HTML/JavaScript layer (lines 119-579):**
```
Lines 119-182: Filter Sidebar
  - City <select> populated from $villes
  - Category <select> populated from $categories
  - Domain <select> hidden by default (shown when category selected via AJAX)
  - Bac type <select> from $bac_types
  - Type <select> from $types (DISTINCT type values from DB)
  - Reset button

Lines 186-231: Results grid
  - PHP renders all institutions server-side on first load
  - Shows count: count($institutions) schools found
  - The JavaScript IMMEDIATELY calls doSearch() and replaces this with AJAX data
  - So the PHP render is just a flash — AJAX takes over within milliseconds

Lines 309-348: JavaScript translation object
  - Passes PHP __() values to JS for dynamic rendering (no hardcoded French in JS)

Lines 349-371: doSearch() function
  - Reads all filter values
  - Builds URLSearchParams
  - Calls fetch('../search_ajax.php?' + params)
  - Updates resultsCount text
  - Calls renderResults(data)

Lines 374-391: translateType() in JS
  - Maps English type strings to translated labels using the langTranslations object

Lines 393-428: renderResults(data)
  - If empty: shows "no results" message
  - Otherwise: builds card HTML via template literal for each institution
  - Sets isSaved by checking savedIds array (set from PHP on page load)
  - Each card has save ❤ button if logged in

Lines 431-509: resolveCardImage(inst) in JS
  - Same logic as PHP version: checks hardcoded name→filename map
  - Falls back to inst.image from DB, then default_school.jpg

Lines 513-516: Search input debounce (300ms)
  - Prevents a fetch on every keystroke — waits until user stops typing

Lines 518-520: All filter dropdowns trigger doSearch() on change

Lines 522-538: Category change handler
  - Fetches domains from get_domains.php?cat_id=X
  - Shows the domain sub-filter dropdown

Lines 540-578: URL parameter initialization
  - If page loaded with ?cat_id= (from orientation_explore link), pre-selects the filter
  - If ?domain_id= also present, waits 500ms for the domain dropdown to populate
  - Always calls doSearch() at the end to sync the view
```

---

## 13. AJAX Search Engine — `search_ajax.php`

This is the JSON API endpoint called by institutions.php JavaScript.

```
Lines 4-10: Read all GET parameters (no session needed — this is a public API)
  - search, city_id, cat_id, domain_id, filiere_id, bac_id, type

Lines 12-23: Build the base SELECT with LEFT JOINs
  - i.* — all institution columns
  - v.nom as city_name — localized city from villes
  - GROUP_CONCAT(DISTINCT f.nom) as filieres_list — comma-joined filiere names
  - LEFT JOIN villes v ON i.ville_id = v.id
  - LEFT JOIN institution_filieres ifil ON i.id = ifil.institution_id
  - LEFT JOIN filieres f ON ifil.filiere_id = f.id
  - LEFT JOIN domains d ON f.domain_id = d.id
  - LEFT JOIN institution_bac_types ibt ON i.id = ibt.institution_id
  - LEFT JOIN institution_domain idom ON i.id = idom.institution_id
  - LEFT JOIN domains d2 ON idom.domain_id = d2.id
  Multiple JOINs = some institutions may match multiple rows. GROUP BY i.id
  (line 68) collapses them back to one row per institution.

Lines 26-32: Search filter
  - i.name LIKE %x% OR i.name_ar LIKE %x% OR i.description LIKE %x% OR f.nom LIKE %x%
  - 4 parameters for the same search string (binds all 4 ? placeholders)

Lines 34-37: City filter — AND i.ville_id = ?

Lines 39-43: Category filter
  - AND (d.categorie_id = ? OR d2.categorie_id = ?)
  - Checks both the domain-via-filiere and the direct institution-domain domain

Lines 45-50: Domain filter
  - AND (f.domain_id = ? OR idom.domain_id = ? OR ifil.filiere_id = ?)
  - Triple check: via filiere's domain, via direct domain link, or filiere itself

Lines 52-54: Filiere filter — AND ifil.filiere_id = ?

Lines 57-60: Bac type filter — AND ibt.bac_type_id = ?

Lines 62-66: Type / sector filter
  - AND (i.type = ? OR i.sector_type = ?)
  - Handles both the category type ('Engineering') and sector_type ('public'/'private')

Line 68: GROUP BY i.id — crucial! Prevents duplicate rows from multiple JOINs

Line 69: ORDER BY (i.id = 131) DESC, i.is_popular DESC, i.name ASC
  - id=131 (Solicode) always appears first
  - Then popular schools
  - Then alphabetical

Lines 71-73: Execute prepared statement, fetch all as associative array

Lines 75-95: Localize each institution
  - Override name, description, city, diplome, duree_etudes, filieres_list
  - getLocalizedDbField() picks the right language column

Lines 99-100: Output as JSON with Content-Type: application/json header
```

---

## 14. Institution Detail Page — `views/institution_detail.php`

```
Lines 8-11: Validate ?id= parameter
  - Must be numeric → cast to int
  - Missing or non-numeric → redirect to institutions.php

Lines 17-35: Main institution query
  - Checks if villes table exists first (guards old schema)
  - If villes exists: SELECT i.*, v.nom as ville_nom FROM institutions i LEFT JOIN villes v
  - Then localizes all text fields

Lines 47-55: Parent university
  - If inst.parent_id is set, fetch the parent institution
  - Displayed as "Member of [Parent Name]" in the hero section

Lines 58-70: Sub-schools / faculties
  - SELECT * FROM institutions WHERE parent_id = $id
  - Enables hierarchical display: a University can show its faculties
  - Each sub-school is shown as a card grid

Lines 72-86: Domain tags
  - SELECT FROM domains JOIN institution_domain WHERE institution_id = ?
  - Shown as pill badges on the hero image

Lines 89-91: Special flags
  - $isCPGE: name contains 'cpge' OR type = 'Preparatory'
    → shows gold "Excellence Track" ribbon
  - $isAlternativeTech: name contains '1337' OR 'youcode' OR sector_type='alternative'
    → shows green "Alternative School" badge

Lines 93-107: Filieres (programs/streams)
  - SELECT f.*, d.nom as domain_nom FROM filieres f
    JOIN institution_filieres ifil ON f.id = ifil.filiere_id
    LEFT JOIN domains d ON f.domain_id = d.id
    WHERE ifil.institution_id = ?
  - Each filiere shown with its domain tag

Lines 110-120: Bac requirements
  - SELECT bt.*, ibt.min_grade FROM bac_types bt
    JOIN institution_bac_types ibt ON bt.id = ibt.bac_type_id
    WHERE ibt.institution_id = ?

Lines 122-136: Gallery images from institution_images table

Lines 129-136: Reviews
  - SELECT reviews.*, students.name AS author_name
    FROM reviews JOIN students ON reviews.student_id = students.id
    WHERE reviews.institution_id = ? AND reviews.status = 'approved'
  - Only approved reviews shown to visitors

Lines 240: Determine main image
  - If gallery images exist, use first one
  - Otherwise use inst.image from DB
  - Both run through resolveDetailImage() which has the same name→filename map

Lines 362-406: Review submission form (login required)
  - Star rating: 5 radio inputs in row-reverse (CSS trick for hover fill)
  - Textarea for written review
  - POST to ../submit_review.php with CSRF token
  - Reviews go in as status='pending' and await admin approval

Lines 493-513: Sidebar — Admission Info
  - Shows: seuil (general threshold), per-bac thresholds, diplome, duree_etudes, requirements
  - Official website link (auto-prepends https:// if missing)
  - Save to favourites button (login required) — POST to save_school.php
```

---

## 15. AI Orientation — `views/ai_form.php` + `ai_process.php`

**`views/ai_form.php`:**
- Simple form: bac_branch (select), average (number), city (text input)
- POSTs to `../ai_process.php`
- No PHP logic — just the form HTML

**`ai_process.php`** line by line:
```
Lines 9-12: Redirect GET requests back to the form

Lines 14-16: Read POST: bac_branch, average (floatval), city

Lines 18-27: Branch-to-type mapping
  - Maps bac branches to arrays of allowed institution types
  - SVT → [Engineering, Science, Technical, University, Preparatory, Private, Education]
  - Eco → [Business, University, Private, Education]
  - Lettres → [University, Education, Private]
  - Unknown branch → [University, Private]

Lines 29-30: Build SQL IN() clause
  - array_fill(0, count($allowedTypes), '?') → ['?', '?', '?', ...]
  - implode(',', ...) → '?,?,?,...'
  - This prevents SQL injection while supporting a variable-length IN()

Lines 32-34: Main query
  - SELECT * FROM institutions WHERE min_average <= ? AND type IN (?,?,...)
  - Filters by student's grade average AND allowed types for their bac branch

Lines 39-46: City preference ordering
  - If city provided: ORDER BY CASE WHEN city = ? THEN 0 ELSE 1 END, min_average DESC
    → Schools in the student's city appear first, then by highest min_average
  - If no city: ORDER BY min_average DESC

Lines 52-58: Save recommendation to DB
  - If logged in: INSERT INTO ai_recommendations (student_id, result)
  - result = text like "5 écoles trouvées pour SVT avec 14.5 de moyenne"
  - This provides history but the Gemini AI (chatbot) uses this to provide context

Lines 60-114: Image resolution + type translation functions (same logic as other pages)

Lines 117-207: Results page HTML
  - Summary bar showing count, branch, average, city
  - If no results: shows empty state with "Try Again" button
  - Otherwise: cards grid similar to institutions.php
    - Each card has: image, type badge, city-match badge (if school is in student's city)
    - description, diplome, duree_etudes, requirements tags
    - Save button (login required), details link
```

**Note:** The AI here is **rule-based** (SQL filtering), NOT the Gemini AI. The Gemini AI is only used by the chatbot widget for conversational responses.

---

## 16. Gemini AI Service — `services/GeminiService.php`

Used only by the chatbot (`includes/chatbot.php`).

```
Class properties:
  - $apiKey: from GEMINI_API_KEY env var
  - $models: ['gemini-2.5-flash-lite', 'gemini-2.5-flash']  ← priority order
  - $maxRetries: 3
  - $retryDelay: 2 seconds

Constructor:
  - loadEnv() — reads .env file (same logic as DataBase.php)
  - detectCaBundle() — auto-finds SSL certificate for cURL
    Checks XAMPP, WAMP, Laragon, Linux, macOS standard locations

ask($prompt, $system='') — the main public method:
  1. Validates API key (not empty, not 'YOUR_KEY')
  2. Loops through $models array (tries lite first, then full)
  3. For each model: calls tryModel()
  4. tryModel() calls sendRequest() up to 3 times
  5. On success → returns immediately
  6. On 503 → logs the error, waits 2s, retries
  7. Non-503 errors → returns immediately (no retry: auth errors affect all models)
  8. If ALL models and ALL retries fail with 503 → returns friendly "AI busy" message

sendRequest($model, $prompt, $system):
  - Builds URL: baseUrl + model + ':generateContent?key=' + apiKey
  - Payload structure: { contents: [{ role: 'user', parts: [{text: system}, {text: prompt}] }] }
  - generationConfig: temperature=0.7, maxOutputTokens=1024
  - Sends via cURL POST with 30s timeout
  - SSL: if GEMINI_DEBUG_SSL=true → disables verification (dev only)
         if cert found → uses it for proper verification
  - Extracts text from: decoded.candidates[0].content.parts[0].text
  - Returns {success, reply, error, http_code, raw, model_used}
```

---

## 17. Admin System

### Role Hierarchy (stored in `students.role`):
```
'student'    → Normal user. No admin access.
'admin'      → Can: moderate reviews, send notifications, add institutions
'superadmin' → All admin powers + manage other users' roles
```

### Access Guards — `includes/platform_admin.php`:

```
platform_admin_role($pdo, $userId)
  → SELECT role FROM students WHERE id = ? LIMIT 1
  → Returns the role string or null

is_platform_admin($pdo)
  → Returns true if role is 'admin' OR 'superadmin'

is_superadmin($pdo)
  → Returns true ONLY if role is 'superadmin'

require_platform_admin($pdo)
  → Redirects to login if not logged in
  → Shows 403 error page if not at least 'admin'
  → Used at top of: admin_dashboard.php, admin_reviews.php, admin_send_notification.php

require_superadmin($pdo)
  → Same as above but stricter
  → If 'admin' (but not 'superadmin'): shows specific "role management is superadmin only" error
  → Used only in: admin_users_manage.php
```

### Admin Dashboard — `views/admin_dashboard.php`:
```
Line 5: require_platform_admin($pdo) — blocks non-admins
Line 8: $isSuperAdmin = is_superadmin($pdo) — determines which cards to show
- Review moderation card (all admins)
- Notification card (all admins)
- Add institution card (all admins)
- User management card (superadmin only — wrapped in if $isSuperAdmin)
```

### User Management — `views/admin_users_manage.php`:
```
Line 8: require_superadmin($pdo) — only superadmin can access

POST handling (lines 13-56):
  1. Double-check superadmin on every POST
  2. Verify CSRF token
  3. Read $targetUserId and $action
  4. Check target user exists via platform_admin_role() — returns null if not found
  5. Block self-modification ($targetUserId === $currentUserId)
  6. Block modifying another superadmin (targetRole === 'superadmin')
  7. 'promote': UPDATE students SET role='admin' WHERE id=?
  8. 'demote':  UPDATE students SET role='student' WHERE id=?
  9. Fallback: redirect with 'Action invalide' error

GET display (lines 61-84):
  - Search by name or email
  - Filter by role dropdown
  - SELECT id, name, email, role, created_at FROM students
  - ORDER BY FIELD(role, 'superadmin', 'admin', 'student') → superadmin first
  
Per-user card logic:
  - If $isSelf → shows "Connected account" (no buttons)
  - If target is superadmin → shows "Protected account" (no buttons)
  - If target is 'student' → shows green Promote button
  - If target is 'admin' → shows red Demote button
```

---

## 18. Notification System

### Database tables involved:
```
notifications      — the actual notification content (global or per-user)
user_notifications — pivot: tracks read/deleted status per user per notification
```

### AJAX endpoints:

**`check_new_notifications.php`** (called every 30s):
- Returns: `{ unread_count: N, latest: { id, title, message, type } }`
- Used by header.php JS to update bell badge and show toasts

**`get_notifications.php`** (called when bell is clicked):
- Returns array of notifications for the logged-in user
- Includes: id, title, message (truncated), type, icon emoji, time_ago, is_read
- `time_ago` calculated in PHP: "Il y a 5 minutes" etc.

**`mark_notification_read.php`**:
- `?id=X` → marks one notification read (INSERT or UPDATE in user_notifications)
- `?all=1` → marks all unread as read for the user

**`delete_notification.php`**:
- Sets `is_deleted = 1` in user_notifications for the specified id

### Admin sending notifications — `views/admin_send_notification.php`:
- Form with: title (FR/AR/EN), message (FR/AR/EN), type, target (global or specific user)
- INSERT INTO notifications with all fields
- If global: all users will see it on next check
- If targeted: only the specified user_id receives it

---

## 19. CSRF Protection — `includes/csrf.php`

```
csrf_token()
  - If $_SESSION['csrf_token'] is empty, generates: bin2hex(random_bytes(32))
  - Returns the token (always the same within a session)

csrf_input()
  - Returns: <input type="hidden" name="csrf_token" value="[token]">
  - Called inside every form that does a state-changing POST

verify_csrf_token($token)
  - Checks: session has csrf_token AND input token is a string
    AND hash_equals(session_token, input_token)
  - hash_equals() prevents timing attacks (constant-time comparison)
  - Returns true/false
```

Every POST-handling file calls `verify_csrf_token($_POST['csrf_token'])` early on, before any DB write. Failed verification → redirect with error.

---

## 20. Save / Remove School

**`save_school.php`** (POST):
```
- Requires login (checks $_SESSION['user_id'])
- Verifies CSRF token
- Reads institution_id from POST
- INSERT IGNORE INTO saved_schools (student_id, institution_id)
  → INSERT IGNORE means: if already saved, does nothing (no error)
- Redirects back with ?saved=1 success message
```

**`remove_school.php`** (POST or via JS in saved_schools.php):
```
- Requires login
- Verifies CSRF
- DELETE FROM saved_schools WHERE student_id = ? AND institution_id = ?
```

**In `institutions.php`**, the save button works via JavaScript (`toggleSave(id, button)`):
- `script.js` handles the AJAX call to save_school.php / remove_school.php
- Updates the button's `.active` class immediately without page reload
- Updates the local `savedIds` array

---

## 21. Database Schema Summary

| Table | Primary Key | Unique Constraint | Purpose |
|-------|-------------|-------------------|---------|
| `students` | `id` AUTO_INCREMENT | email (none — should add) | Users |
| `institutions` | `id` AUTO_INCREMENT | `(name, city)` | Schools |
| `filieres` | `id` AUTO_INCREMENT | `(nom, categorie_id)` | Study programs |
| `domains` | `id` AUTO_INCREMENT | `(nom, categorie_id)` | Domains of study |
| `categories` | `id` AUTO_INCREMENT | `nom` | Top-level categories |
| `villes` | `id` AUTO_INCREMENT | `nom` | Moroccan cities |
| `institution_filieres` | `(institution_id, filiere_id)` | — | School ↔ Program pivot |
| `institution_domain` | `(institution_id, domain_id)` | — | School ↔ Domain pivot |
| `institution_bac_types` | `(institution_id, bac_type_id)` | — | School ↔ Bac type pivot |
| `bac_types` | `id` AUTO_INCREMENT | — | Bac branches |
| `contests` | `id` AUTO_INCREMENT | — | Entrance exams |
| `deadlines` | `id` AUTO_INCREMENT | — | Application deadlines |
| `reviews` | `id` AUTO_INCREMENT | — | Student reviews (pending/approved) |
| `notifications` | `id` AUTO_INCREMENT | — | Platform notifications |
| `user_notifications` | `id` AUTO_INCREMENT | — | Read/delete tracking per user |
| `saved_schools` | `id` AUTO_INCREMENT | — | Student favourites |
| `appointments` | `id` AUTO_INCREMENT | — | Booking appointments |
| `ai_recommendations` | `id` AUTO_INCREMENT | — | History of AI searches |
| `admin_notifications` | `id` AUTO_INCREMENT | — | Internal admin alerts |
| `premium_plans` | `id` AUTO_INCREMENT | — | Subscription plans (not yet active) |

**All tables now have PRIMARY KEYs and AUTO_INCREMENT** (fixed during this session). Key foreign key relationships:
- `saved_schools.student_id` → `students.id`
- `saved_schools.institution_id` → `institutions.id`
- `reviews.student_id` → `students.id`
- `reviews.institution_id` → `institutions.id`
- `institution_filieres.institution_id` → `institutions.id`
- `institution_filieres.filiere_id` → `filieres.id`
- `contests.institution_id` → `institutions.id`
- `deadlines.institution_id` → `institutions.id`
- `user_notifications.notification_id` → `notifications.id`
- `user_notifications.user_id` → `students.id`

> Note: These foreign key relationships exist logically in the application code, but no `FOREIGN KEY CONSTRAINT` has been added to the database yet. The application code handles referential integrity.

---

*End of documentation — no code was modified during this analysis.*
