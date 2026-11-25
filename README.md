# Internal System v2

An internal operations platform for employee services, finance requests, and approval workflows. Built with Laravel 12 and Filament 5, it combines an administration panel with a TailAdmin-inspired employee portal backed by the same application and database.

## Capabilities

- **Employee services:** profiles, departments, attendance, leave balances, holidays, overtime, and late-arrival permits.
- **Finance workflows:** loan notes, payment applications, travel and expense reimbursements, and meal claims.
- **Legal operations:** stamp applications and incoming/outgoing letter registration.
- **Asset management:** asset inventory, assignments, purchase details, and lifecycle records.
- **Approvals and accountability:** role-based authorization, record-level checks, approval history, activity logs, and scheduled reminders.
- **Reporting:** PDF documents, Excel exports, and finance summaries.
- **Mobile integration:** Sanctum-protected attendance and workflow APIs.
- **Multilingual interface:** English, Bahasa Indonesia, and Simplified Chinese, with a persistent language preference.

## Application entry points

| Path | Purpose |
| --- | --- |
| `/portal` | Employee portal dashboard |
| `/portal/login` | Portal sign-in |
| `/portal/register` | Registration, when enabled |
| `/portal/workspace` | Portal resource pages and reports |
| `/admin` | Original Filament administration panel |

Both interfaces share authentication, models, authorization rules, and workflow components. The portal is inspired by TailAdmin; it does not bundle a TailAdmin Pro license. Translations apply to interface labels, not stored business data or user documents.

## Technology

| Layer | Technology |
| --- | --- |
| Runtime | PHP 8.3+ recommended; Composer 2 |
| Backend | Laravel 12, Filament 5, Livewire |
| Database | MySQL 8+ / compatible MariaDB |
| Frontend | Tailwind CSS 4, Vite 7, Node.js 22.12+ |
| Authentication | Laravel session guard and Sanctum API tokens |
| Documents | Dompdf, Laravel Excel, PhpSpreadsheet |

## Local setup

```bash
git clone https://github.com/robyfirmansya11/internal-system_v2.git
cd internal-system_v2
composer install
```

Create a local configuration file:

```bash
# Linux / macOS
cp .env.example .env

# Windows PowerShell
Copy-Item .env.example .env
```

Create an empty local MySQL database and set `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`. Never use production credentials for development.

```bash
php artisan key:generate
php artisan migrate
npm ci
npm run build
php artisan app:create-administrator
php artisan serve
```

The administrator command prompts for credentials interactively, uses a hidden password prompt, and does not overwrite an existing account. Complete department assignments and employee profiles through the administration panel.

For concurrent development services, run `composer run dev`. Laravel Herd can also serve the checkout directly.

### Optional demo data

Demo seeders are restricted to `local` and `testing`. `InitialAccessSeeder` contains fictional `example.test` identities and generates temporary passwords. Run it only in an isolated disposable database; never capture its credential output in shared logs. The application does not require demo data to operate.

## Configuration

| Setting | Guidance |
| --- | --- |
| `APP_ENV` | `local` for development; `production` for deployment |
| `APP_DEBUG` | Must be `false` in production |
| `APP_URL` | Use the canonical HTTPS URL in production |
| `APP_LOCALE` | `en`, `id`, or `zh_CN` |
| `PORTAL_REGISTRATION_ENABLED` | `false` for production; enable only with an explicit onboarding policy |
| `SESSION_SECURE_COOKIE` | `true` when serving over HTTPS |
| `SESSION_HTTP_ONLY` | Keep `true` |
| `SESSION_SAME_SITE` | Keep `lax` unless a reviewed integration requires otherwise |
| `RECEIPT_OCR_ENABLED` | Optional; requires a separately installed Tesseract executable |

The example environment is for local development. Production values must be set explicitly. Private uploads belong on the configured private disk and must be served through authorized application endpoints.

## Tests and dependency checks

The PHPUnit configuration uses an isolated MySQL database named `insys_db_testing`. Create that database and grant the local test account access before running tests. Some tests reset its schema: **never point the test suite at an operational database**. `Tests/TestCase.php` enforces this database name for MySQL.

```bash
php artisan test
composer validate --no-check-publish
composer audit --locked
npm audit
npm run build
```

The suite covers authentication, locale switching, panel parity, authorization, resource forms, document access, and approval workflows. Automated rendering tests do not replace browser-based acceptance testing for uploads, signatures, exports, and production integrations.

## Deployment

1. Back up the database and private uploads; verify a restore procedure.
2. Configure production secrets outside source control, disable debug mode and open registration, and enable HTTPS-only session cookies.
3. Install locked dependencies with `composer install --no-dev --optimize-autoloader` and `npm ci`.
4. Build frontend assets using `npm run build`; deploy `public/build` with the application.
5. Run `php artisan migrate --force`, followed by `php artisan optimize`.
6. Set the web-server document root to `public`; grant write access only where Laravel requires it.
7. Configure the queue worker and run `php artisan schedule:run` every minute.
8. Verify login, authorization, private uploads, approval actions, PDF/Excel export, and all three interface languages.

Do not run `migrate:fresh`, demo seeders, or the test suite against production. Do not expose `.env`, storage directories, logs, or database backups through the web server.

## Security and privacy

This repository is a source-only release. It excludes runtime configuration, user uploads, database exports, private keys, installed dependencies, and runtime/cache artifacts. A clean initial history is used for v2 so excluded legacy files are not published through older commits.

Security controls include CSRF protection, rate-limited authentication, hashed passwords, role/record authorization, private document delivery, and production registration controls. These controls require correct server configuration and regular dependency updates; they are not a security certification.

See [Security Policy](SECURITY.md). Do not include credentials, employee information, attendance photos, or financial documents in public issues or pull requests.

## Documentation

- [Portal architecture and languages](docs/PORTAL.md)
- [Asset management](docs/ASSET_MANAGEMENT.md)
- [Mobile attendance integrity](docs/MOBILE_ATTENDANCE_INTEGRITY.md)
- [Production operations](docs/PRODUCTION_OPERATIONS.md)

## Licensing

The application is intended for authorized internal use. No additional redistribution rights are granted by this README. Review the project's licensing terms and the licenses of its third-party dependencies before redistribution.
