# AGENTS.md — RequerimientosApp

## Project
CodeIgniter 4.7 app (PHP 8.2+) for managing "requerimientos" (service requests). Uses `codeigniter4/framework` as a Composer dependency.

## Commands
- `composer install` — install dependencies
- `php spark serve` — dev server (default port 8080; `.env` overrides to 8081)
- `php spark <command>` — all CLI commands (migrations, seeds, etc.)
- `vendor/bin/phpunit` or `composer test` — run tests
- `php spark migrate` — run pending migrations
- `php spark db:seed AdminSeeder` — seed initial admin user

## Architecture
- **Entry point**: `public/index.php` (web), `spark` (CLI)
- **Config**: `app/Config/` — `Routes.php`, `Filters.php`, `Database.php` are the most frequently edited
- **Controllers**: `app/Controllers/` — namespaced subdirs for role-based areas (`Admin/`, `Secretaria/`, `Director/`, `Lider/`)
- **Models**: `app/Models/` — flat namespace (ActivityReportModel, AssignmentModel, ClientsModel, DocumentModel, RolesModel, SettingsModel, UsersModel)
- **Filters**: `app/Filters/AuthFilter.php` (checks `session()->get('is_logged_in')`), `app/Filters/RoleFilter.php` (checks `session()->get('role_slug')`)
- **Services**: `app/Services/EmailService.php` — handles email notifications via Gmail SMTP
- **Migrations**: `app/Database/Migrations/` — timestamp-prefixed, run with `php spark migrate`
- **Seeds**: `app/Database/Seeds/AdminSeeder.php`
- **Writable dirs**: `writable/` — cache, logs, sessions, uploads (gitignored contents)

## Auth & Roles
Session-based auth. Four role slugs: `admin`, `secretaria`, `director`, `lider_area`. Routes are protected by chained filters: `['auth', 'role:<slug>']`. New role-gated routes must register the filter alias in `app/Config/Filters.php`.

## Database
MySQL via MySQLi. Database name: `requerimiento_app`. Credentials in `.env` (dev: admin/admin on 127.0.0.1:3306). Migrations are excluded from classmap autoload.

## Testing
- PHPUnit 10.5, config in `phpunit.xml.dist`
- Test files in `tests/` (subdirs: `database/`, `session/`, `unit/`, `_support/`)
- Bootstrap: `vendor/codeigniter4/framework/system/Test/bootstrap.php`
- Test DB credentials are commented out in `.env` and `phpunit.xml.dist` — must be configured manually before running DB tests
- XDebug required for code coverage

## Conventions
- CSRF filter is **disabled** globally (commented out in `Filters.php` globals)
- All POST routes for CRUD operations use POST (not RESTful PUT/DELETE)
- Views live in `app/Views/`, public assets in `public/assets/` and `public/dist/`
- `.env` is gitignored; `env copy` exists as a backup reference
