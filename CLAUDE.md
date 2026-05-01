# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Install dependencies
composer install
npm install

# Compile SCSS to CSS
npm run compile-sass

# Generate PHPDoc for Eloquent models
php demo/phpdocs.php            # Regenerate all models
php demo/phpdocs.php Student    # Single model

# Run local dev server (no built-in script)
php -S localhost:8000
```

No formal test suite exists — testing is done manually.

## Architecture

SchoolSoft is a **multi-tenant PHP 8.4+ school management SaaS**. Each school runs as its own subdirectory (e.g., `/demo`, `/omar`) with an isolated database connection, session, and configuration.

### Bootstrap Flow

Every PHP entry point must start with `require_once '../app.php'`. Skipping it breaks tenant context and database connections.

1. `demo/app.php` — Defines school constants (`__SCHOOL_ACRONYM`, `__ROOT_SCHOOL`, `__SCHOOL_URL`, `__LANG`), loads root `bootstrap.php`
2. `bootstrap.php` — Loads Composer autoload, `.env`, sets up Eloquent with morph map
3. `core/Database.php` — Configures two Eloquent connections: `tenant` (default, per-school) and `central` (shared schools table)
4. `demo/constants.php` — Dynamically loads school config rows from the `central` DB using `__SCHOOL_ACRONYM`

### Dual ORM

Legacy `Classes\DataBase\DB` (raw query builder) coexists with modern Eloquent models in `app/Models/`. Prefer Eloquent for new code.

**Eloquent quirks:**
- Many models use non-standard table names (e.g., `Student` → `year` table, `Family` → `madre`)
- Most models carry global scopes — `Student` auto-filters by current academic year and excludes unenrolled records
- Polymorphic relationships use a morph map (`'student'`, `'admin'`)

### Page Pattern

```php
require_once '../app.php';
Session::is_logged();  // Validates acronym, location, 4-hour timeout
Route::includeFile('/admin/includes/layouts/header.php');
// … page content …
Route::includeFile('/includes/layouts/scripts.php', true);  // true = root, not school folder
```

### Key Helpers

```php
// Config
config('app.locale')             // root /config/app.php
school_config('app.acronym')     // /demo/config/app.php
school_is('demo', 'omar')        // check current tenant

// Assets
asset('images/globe.gif')        // /images/globe.gif
school_asset('css/main.css')     // /demo/css/main.css
school_logo()                    // current school's logo URL

// Routing
Route::includeFile('/path.php')          // include relative to school folder
Route::includeFile('/path.php', true)    // include relative to server root
Route::redirect('/login.php')            // tenant-aware redirect

// Files
upload_attachment($_FILES['doc'], 'messages')  // stores to attachments/messages/
attachments_url('letters/doc.pdf')             // returns full URL

// Debug
ds($data)   // LaraD umps (dev only)
dd($data)   // legacy dump-and-die
```

### Translation

Prefer the modern `__()` helper for all new code. `Classes\Lang` is still widespread in legacy pages but should not be used in new code.

```php
__('Welcome back')               // uses lang/en.json or lang/es/
trans_choice('student.count', 5) // pluralization

// Legacy (avoid in new code)
$lang = new Lang([['Inicio', 'Home']]);
echo $lang->translation('Inicio');
```

`__LANG` (set in `demo/app.php`) drives both systems. Admin users have a per-user language override (`$_user->idioma`).

### Authentication

Location-based sessions (locations: `admin`, `regiweb`, `foro`, `parents`, `cafeteria`):

```php
$_SESSION['logged'] = [
    'acronym'  => __SCHOOL_ACRONYM,  // cross-school access prevention
    'location' => 'admin',
    'user'     => ['id' => $userId],
];
```

Always call `Session::is_logged()` at the top of every protected page.

### PDF Generation

`Classes\PDF` extends FPDF with automatic school header/footer (logo, name, address from `Admin::primaryAdmin()`):

```php
$pdf = new PDF();
$pdf->AddPage();
$pdf->Cell(0, 5, 'Title', 0, 1, 'C');
$pdf->Output();

// Optional flags
$pdf->header = false;  // disable header
$pdf->logo   = false;  // disable logo
```

### Common Patterns

- **Grades**: Stored as zero-padded strings (`'01'`, `'11'`). Use `School->allGrades()` for dropdowns.
- **Year handling**: Admins use `year2`; other portals use `year` from school config.
- **School-specific logic**: Use `__SCHOOL_ACRONYM` or `school_is()` for conditional behavior per tenant (e.g., `'cbtm'` uses different terminology).

## Key Files

| File | Purpose |
|---|---|
| `demo/app.php` | School bootstrap template — copy when adding a new tenant |
| `bootstrap.php` | Root Composer/Eloquent bootstrap |
| `core/Database.php` | Dual-connection Eloquent setup |
| `app/helpers.php` | All global helper functions |
| `Classes/Route.php` | Routing, asset, and include helpers |
| `Classes/Session.php` | Auth validation logic |
| `config/database.php` | Central DB config |
| `demo/config/database.php` | Tenant DB config template |
