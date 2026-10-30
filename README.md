# LGU Sibonga -- Birth Records Management System

## Native PHP Project Architecture and Security Blueprint

**Stack:** PHP 8.4+, Composer, MySQL 8, Bootstrap 5, JavaScript\
**Architecture:** Lightweight MVC-inspired structure (no Laravel)\
**Web root:** `public/` only

------------------------------------------------------------------------

## 1. Project goals

Build an internal system for authorized LGU personnel to manage
birth-record information, supporting:

-   Secure staff authentication and role-based access.
-   Birth record creation, viewing, updating, verification, and
    archival.
-   Parent and barangay information management.
-   Restricted document storage and retrieval.
-   Dashboard statistics and authorized reports.
-   Audit trails for sensitive actions.
-   Database backup and recovery procedures.

This application is an internal records-management tool. It must not be
treated as a replacement for official Local Civil Registrar, PSA, or
other legally required registration and certificate-issuance procedures.
Confirm the exact workflow, fields, permissions, retention, and release
rules with the Municipal Civil Registrar and the LGU Data Protection
Officer.

------------------------------------------------------------------------

## 2. Recommended folder structure

``` text
lgusibonga/
│
├── app/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginController.php
│   │   │   ├── LogoutController.php
│   │   │   └── PasswordController.php
│   │   ├── DashboardController.php
│   │   ├── BirthRecordController.php
│   │   ├── ParentController.php
│   │   ├── BarangayController.php
│   │   ├── DocumentController.php
│   │   ├── ReportController.php
│   │   ├── UserController.php
│   │   ├── AuditLogController.php
│   │   └── SettingsController.php
│   │
│   ├── Core/
│   │   ├── App.php
│   │   ├── Router.php
│   │   ├── Request.php
│   │   ├── Response.php
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   ├── View.php
│   │   ├── Auth.php
│   │   ├── Session.php
│   │   ├── Validator.php
│   │   ├── Csrf.php
│   │   ├── RateLimiter.php
│   │   └── Exceptions/
│   │       ├── HttpException.php
│   │       └── ValidationException.php
│   │
│   ├── Middleware/
│   │   ├── Authenticate.php
│   │   ├── GuestOnly.php
│   │   ├── RequireRole.php
│   │   ├── VerifyCsrfToken.php
│   │   ├── RateLimit.php
│   │   └── RequirePermission.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Permission.php
│   │   ├── BirthRecord.php
│   │   ├── Parent.php
│   │   ├── Barangay.php
│   │   ├── BirthDocument.php
│   │   └── AuditLog.php
│   │
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── BirthRecordService.php
│   │   ├── DocumentStorageService.php
│   │   ├── ReportService.php
│   │   ├── AuditLogService.php
│   │   └── BackupService.php
│   │
│   ├── Policies/
│   │   ├── BirthRecordPolicy.php
│   │   ├── DocumentPolicy.php
│   │   └── UserPolicy.php
│   │
│   └── Views/
│       ├── layouts/
│       │   ├── app.php
│       │   ├── auth.php
│       │   ├── header.php
│       │   ├── sidebar.php
│       │   └── footer.php
│       ├── auth/
│       ├── dashboard/
│       ├── birth-records/
│       ├── parents/
│       ├── barangays/
│       ├── documents/
│       ├── reports/
│       ├── users/
│       ├── audit-logs/
│       └── errors/
│
├── bootstrap/
│   └── app.php
│
├── config/
│   ├── app.php
│   ├── database.php
│   ├── session.php
│   ├── security.php
│   ├── filesystems.php
│   └── permissions.php
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── schema.sql
│
├── routes/
│   ├── web.php
│   └── api.php
│
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── assets/
│       ├── css/
│       ├── js/
│       ├── images/
│       └── vendor/
│
├── resources/
│   ├── css/
│   └── js/
│
├── storage/
│   ├── app/
│   │   ├── birth-documents/
│   │   └── temporary/
│   ├── logs/
│   ├── backups/
│   └── exports/
│
├── tests/
│   ├── Unit/
│   └── Feature/
│
├── vendor/
├── .env
├── .env.example
├── .gitignore
├── composer.json
├── composer.lock
└── README.md
```

### Directory responsibilities

  -----------------------------------------------------------------------
  Directory                           Responsibility
  ----------------------------------- -----------------------------------
  `app/Core/`                         Reusable framework components:
                                      router, request, response,
                                      database, session, validation,
                                      CSRF.

  `app/Controllers/`                  Accept requests, call services, and
                                      return views or responses.

  `app/Models/`                       Database access and entity-level
                                      queries.

  `app/Services/`                     Business rules, transactions,
                                      document storage, reports, and
                                      audit logging.

  `app/Middleware/`                   Authentication, authorization,
                                      CSRF, and rate-limit checks.

  `app/Policies/`                     Record-level authorization rules.

  `app/Views/`                        PHP templates; never place SQL or
                                      business rules here.

  `bootstrap/`                        Loads Composer, environment,
                                      configuration, and the application.

  `config/`                           Central application, database,
                                      session, file, and security
                                      settings.

  `routes/`                           Maps HTTP methods and URL paths to
                                      controller actions and middleware.

  `public/`                           Only web-accessible directory;
                                      front controller and public assets.

  `storage/`                          Private uploaded files, logs,
                                      exports, and backups.

  `database/`                         SQL schema, migrations, and seed
                                      data.

  `tests/`                            Automated unit and feature tests.
  -----------------------------------------------------------------------

------------------------------------------------------------------------

## 3. Request flow and how the folders connect

``` text
Browser
  |
  v
public/index.php
  |
  v
bootstrap/app.php
  |-- vendor/autoload.php
  |-- load .env
  |-- load config files
  |-- start secure session
  |-- create Database / Router / App
  |
  v
routes/web.php or routes/api.php
  |
  v
Middleware
  |-- guest/authentication
  |-- role or permission
  |-- CSRF (state-changing web requests)
  |-- rate limit (sensitive endpoints)
  |
  v
Controller
  |
  |-- validate request
  |-- call Policy for record-level access
  |-- call Service
  |      |-- Model / Database
  |      |-- AuditLogService
  |      |-- DocumentStorageService
  |
  v
Response / View
  |
  v
Browser
```

**Important:** Middleware is not a substitute for record-level
authorization. For example, a staff member who can open the birth-record
module must still be checked before viewing a particular record or
downloading its document.

------------------------------------------------------------------------

## 4. Route map

Use named routes and HTTP methods. These are suggested routes; adjust
them to the actual approved LGU workflow.

### Public / guest routes

  --------------------------------------------------------------------------------------------------
  Method            URL                         Controller action                  Protection
  ----------------- --------------------------- ---------------------------------- -----------------
  GET               `/login`                    `LoginController@show`             Guest only

  POST              `/login`                    `LoginController@login`            Guest only, CSRF,
                                                                                   rate limit

  GET               `/forgot-password`          `PasswordController@requestForm`   Guest only, rate
                                                                                   limit

  POST              `/forgot-password`          `PasswordController@sendReset`     Guest only, CSRF,
                                                                                   rate limit

  GET               `/reset-password/{token}`   `PasswordController@resetForm`     Valid, unexpired
                                                                                   token

  POST              `/reset-password`           `PasswordController@reset`         CSRF, valid
                                                                                   token, rate limit
  --------------------------------------------------------------------------------------------------

Do not create public birth-record search, public document-download, or
public record-detail routes unless a formally approved legal process
specifically requires them.

### Authenticated routes

  -------------------------------------------------------------------------------------------
  Method            URL                   Controller action                Access
  ----------------- --------------------- -------------------------------- ------------------
  POST              `/logout`             `LogoutController@logout`        Authenticated,
                                                                           CSRF

  GET               `/dashboard`          `DashboardController@index`      Authenticated,
                                                                           permission

  GET               `/profile`            `UserController@profile`         Authenticated

  PATCH             `/profile`            `UserController@updateProfile`   Authenticated,
                                                                           CSRF

  POST              `/profile/password`   `PasswordController@change`      Authenticated,
                                                                           CSRF,
                                                                           current-password
                                                                           check
  -------------------------------------------------------------------------------------------

### Birth record routes

  --------------------------------------------------------------------------------------------------------------
  Method            URL                             Controller action                 Access
  ----------------- ------------------------------- --------------------------------- --------------------------
  GET               `/birth-records`                `BirthRecordController@index`     `birth_records.view`

  GET               `/birth-records/create`         `BirthRecordController@create`    `birth_records.create`

  POST              `/birth-records`                `BirthRecordController@store`     `birth_records.create`,
                                                                                      CSRF

  GET               `/birth-records/{id}`           `BirthRecordController@show`      `birth_records.view`,
                                                                                      record policy

  GET               `/birth-records/{id}/edit`      `BirthRecordController@edit`      `birth_records.update`,
                                                                                      record policy

  PUT/PATCH         `/birth-records/{id}`           `BirthRecordController@update`    `birth_records.update`,
                                                                                      CSRF, record policy

  POST              `/birth-records/{id}/submit`    `BirthRecordController@submit`    `birth_records.submit`,
                                                                                      CSRF, record policy

  POST              `/birth-records/{id}/verify`    `BirthRecordController@verify`    `birth_records.verify`,
                                                                                      CSRF, record policy

  POST              `/birth-records/{id}/archive`   `BirthRecordController@archive`   `birth_records.archive`,
                                                                                      CSRF, record policy

  GET               `/birth-records/{id}/history`   `BirthRecordController@history`   `birth_records.history`,
                                                                                      record policy
  --------------------------------------------------------------------------------------------------------------

Avoid a general permanent-delete route. Use controlled archival and
correction procedures with history and approvals. If deletion is legally
required, define a separate restricted process and log it.

### Parent and barangay routes

  ------------------------------------------------------------------------------------------------------
  Method            URL                             Controller action              Access
  ----------------- ------------------------------- ------------------------------ ---------------------
  GET               `/birth-records/{id}/parents`   `ParentController@index`       Record view
                                                                                   permission and policy

  POST              `/birth-records/{id}/parents`   `ParentController@store`       Parent update
                                                                                   permission, CSRF,
                                                                                   policy

  PATCH             `/parents/{id}`                 `ParentController@update`      Parent update
                                                                                   permission, CSRF,
                                                                                   policy

  GET               `/barangays`                    `BarangayController@index`     `barangays.view`

  POST              `/barangays`                    `BarangayController@store`     `barangays.manage`,
                                                                                   CSRF

  PATCH             `/barangays/{id}`               `BarangayController@update`    `barangays.manage`,
                                                                                   CSRF

  POST              `/barangays/{id}/archive`       `BarangayController@archive`   `barangays.manage`,
                                                                                   CSRF
  ------------------------------------------------------------------------------------------------------

### Document routes

  -----------------------------------------------------------------------------------------------------------
  Method            URL                               Controller action               Access
  ----------------- --------------------------------- ------------------------------- -----------------------
  POST              `/birth-records/{id}/documents`   `DocumentController@upload`     `documents.upload`,
                                                                                      CSRF, record policy

  GET               `/birth-records/{id}/documents`   `DocumentController@index`      `documents.view`,
                                                                                      record policy

  GET               `/documents/{id}/download`        `DocumentController@download`   `documents.download`,
                                                                                      document policy

  POST              `/documents/{id}/archive`         `DocumentController@archive`    `documents.manage`,
                                                                                      CSRF, document policy
  -----------------------------------------------------------------------------------------------------------

Never expose a physical storage path or a direct public URL to a
confidential birth document. Stream the file through an authenticated
controller after authorization.

### Reports and analytics routes

  -----------------------------------------------------------------------------------------------------------------
  Method            URL                              Controller action                       Access
  ----------------- -------------------------------- --------------------------------------- ----------------------
  GET               `/reports`                       `ReportController@index`                `reports.view`

  GET               `/reports/registrations`         `ReportController@registrations`        `reports.view`

  GET               `/reports/barangay-statistics`   `ReportController@barangayStatistics`   `reports.statistics`

  GET               `/reports/export/excel`          `ReportController@exportExcel`          `reports.export`

  GET               `/reports/export/pdf`            `ReportController@exportPdf`            `reports.export`
  -----------------------------------------------------------------------------------------------------------------

Reports should default to aggregated statistics. Restrict exports
containing names, dates of birth, parent details, or other identifiable
information to specifically authorized users. Log each sensitive export.

### User, role, audit, and settings routes

  ------------------------------------------------------------------------------------------------
  Method            URL                        Controller action             Access
  ----------------- -------------------------- ----------------------------- ---------------------
  GET               `/users`                   `UserController@index`        `users.view`

  POST              `/users`                   `UserController@store`        `users.create`, CSRF

  GET               `/users/{id}`              `UserController@show`         `users.view`

  PATCH             `/users/{id}`              `UserController@update`       `users.update`, CSRF

  POST              `/users/{id}/deactivate`   `UserController@deactivate`   `users.deactivate`,
                                                                             CSRF

  GET               `/audit-logs`              `AuditLogController@index`    `audit_logs.view`

  GET               `/audit-logs/{id}`         `AuditLogController@show`     `audit_logs.view`

  GET               `/settings`                `SettingsController@index`    `settings.view`

  PATCH             `/settings`                `SettingsController@update`   `settings.manage`,
                                                                             CSRF
  ------------------------------------------------------------------------------------------------

Do not allow ordinary users to edit or delete audit logs. Limit
audit-log access and protect the logs from tampering.

------------------------------------------------------------------------

## 5. Role and permission design

Use permissions rather than relying only on hard-coded role names.

Suggested roles:

  -----------------------------------------------------------------------
  Role                                Suggested responsibility
  ----------------------------------- -----------------------------------
  `admin`                             Manage system users, roles,
                                      settings, and approved system
                                      administration tasks.

  `registrar`                         Manage and verify birth records
                                      according to the Municipal Civil
                                      Registrar's authority.

  `staff`                             Encode and update assigned records
                                      within granted permissions.

  `records_officer`                   Manage authorized document
                                      retrieval, archival, and records
                                      workflows.

  `viewer`                            Read-only access to explicitly
                                      permitted records and aggregate
                                      reports.
  -----------------------------------------------------------------------

Suggested permissions:

``` text
dashboard.view

birth_records.view
birth_records.create
birth_records.update
birth_records.submit
birth_records.verify
birth_records.archive
birth_records.history

parents.view
parents.manage

barangays.view
barangays.manage

documents.view
documents.upload
documents.download
documents.manage

reports.view
reports.statistics
reports.export

users.view
users.create
users.update
users.deactivate
roles.manage

audit_logs.view
settings.view
settings.manage
```

Assign permissions only after the LGU approves the staff
responsibilities. Do not assume every administrator should automatically
have access to every confidential record. Separate technical
administration from civil-registry authorization where practical.

------------------------------------------------------------------------

## 6. Security rules for every route

Apply these rules consistently.

### Authentication

-   All internal system routes require an authenticated session.
-   Redirect unauthenticated users to `/login`.
-   Regenerate the session ID after login and privilege changes.
-   Destroy the session and rotate the session token on logout.
-   Use secure, HTTP-only, SameSite cookies; enable `Secure` when HTTPS
    is active.
-   Set idle and absolute session timeouts appropriate to the LGU
    environment.
-   Require re-authentication for sensitive operations.

### Authorization

-   Check permissions on the server for every request.
-   Check record ownership, assignment, office scope, or other approved
    access boundary on every record-specific request.
-   Never trust a hidden form field, URL ID, JavaScript check, or
    disabled button as authorization.
-   Deny access by default.
-   Return 403 for authenticated users without permission and 404 where
    appropriate to avoid disclosing record existence.

### Request and database security

-   Use PDO prepared statements for all user-supplied values.
-   Validate input on the server; browser validation is only a
    convenience.
-   Use allowlists for sort columns, filters, and report parameters.
-   Escape output according to its context, especially HTML.
-   Use CSRF tokens for every state-changing browser request.
-   Use POST/PATCH/PUT for changes; do not change data through GET.
-   Use database transactions for multi-table record changes.
-   Avoid putting personal information in URLs, logs, error messages, or
    analytics events.

### File upload and download security

-   Store files outside `public/`.
-   Allow only required file types and enforce size limits.
-   Check MIME type using server-side file inspection; do not trust the
    filename or browser-provided content type.
-   Generate random storage names; retain the original filename only as
    protected metadata if needed.
-   Reject executable files and double extensions.
-   Consider malware scanning before accepting documents.
-   Download only through an authenticated, authorized controller.
-   Set safe `Content-Disposition` and `X-Content-Type-Options: nosniff`
    headers.
-   Keep file metadata and access events in the database/audit trail.

### Audit and privacy

Log, at minimum, the user, action, target record identifier, timestamp,
result, and relevant request correlation ID for sensitive actions. Avoid
copying full birth-record contents or passwords into logs.

Record access, document downloads, exports, verification, edits,
archival, permission changes, and failed authentication attempts should
be auditable. Define a retention and review policy with the LGU Data
Protection Officer.

### Production configuration

-   Set `APP_DEBUG=false`.
-   Do not expose `.env`, `vendor/`, `storage/`, SQL files, logs, or
    backups through Apache.
-   Use HTTPS.
-   Use a least-privilege database account.
-   Keep PHP and Composer dependencies patched.
-   Back up the database and document storage, encrypt backups, restrict
    access, and test restoration.
-   Keep secrets out of Git; commit `.env.example`, not `.env`.

------------------------------------------------------------------------

## 7. Core and configuration responsibilities

  -----------------------------------------------------------------------
  File                                Responsibility
  ----------------------------------- -----------------------------------
  `app/Core/App.php`                  Application startup and dependency
                                      coordination.

  `app/Core/Router.php`               Register routes, match method/path,
                                      execute middleware and controller.

  `app/Core/Request.php`              Read method, path, query, body,
                                      headers, and uploaded files safely.

  `app/Core/Response.php`             HTML, JSON, redirect, status code,
                                      and file responses.

  `app/Core/Database.php`             Create and provide the PDO
                                      connection.

  `app/Core/Auth.php`                 Current authenticated user and
                                      login state.

  `app/Core/Session.php`              Secure session lifecycle and flash
                                      messages.

  `app/Core/Csrf.php`                 Generate and validate CSRF tokens.

  `app/Core/Validator.php`            Server-side validation rules.

  `app/Core/View.php`                 Render templates with escaped
                                      output conventions.

  `config/app.php`                    Application name, environment, base
                                      URL, timezone.

  `config/database.php`               PDO connection settings from
                                      environment variables.

  `config/session.php`                Cookie flags and session timeouts.

  `config/security.php`               Security headers, upload limits,
                                      and security options.

  `config/filesystems.php`            Private storage paths and allowed
                                      file types.

  `config/permissions.php`            Permission identifiers and role
                                      mappings, if not stored in DB.

  `bootstrap/app.php`                 Load Composer, environment, config,
                                      session, services, and routes.

  `public/index.php`                  Single web entry point.
  -----------------------------------------------------------------------

Keep secrets in `.env`; keep safe defaults and configuration logic in
`config/`. Do not put database passwords directly in PHP source files.

------------------------------------------------------------------------

## 8. Example route registration

The following is illustrative. The Router must support middleware groups
and route parameters before using this syntax.

``` php
<?php
// routes/web.php

use App\Controllers\Auth\LoginController;
use App\Controllers\Auth\LogoutController;
use App\Controllers\DashboardController;
use App\Controllers\BirthRecordController;
use App\Middleware\Authenticate;
use App\Middleware\GuestOnly;
use App\Middleware\RequirePermission;

$router->get('/login', [LoginController::class, 'show'], [
    GuestOnly::class
]);

$router->post('/login', [LoginController::class, 'login'], [
    GuestOnly::class,
    VerifyCsrfToken::class,
    RateLimit::class
]);

$router->post('/logout', [LogoutController::class, 'logout'], [
    Authenticate::class,
    VerifyCsrfToken::class
]);

$router->get('/dashboard', [DashboardController::class, 'index'], [
    Authenticate::class,
    [RequirePermission::class, 'dashboard.view']
]);

$router->get('/birth-records', [BirthRecordController::class, 'index'], [
    Authenticate::class,
    [RequirePermission::class, 'birth_records.view']
]);

$router->post('/birth-records', [BirthRecordController::class, 'store'], [
    Authenticate::class,
    [RequirePermission::class, 'birth_records.create'],
    VerifyCsrfToken::class
]);
```

Adapt the syntax to the actual Router implementation. Do not copy this
example into a Router that does not yet support these middleware
arguments. Ensure CSRF is applied to all state-changing browser routes,
not just the examples shown.

------------------------------------------------------------------------

## 9. Front controller and bootstrap

### `public/index.php`

``` php
<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/app.php';

$app->run();
```

### `bootstrap/app.php` -- startup responsibilities

``` php
<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Asia/Manila');

$config = require BASE_PATH . '/config/app.php';

// Load database, session, security configuration.
// Start the secure session.
// Create the router and register route files.
// Build the App instance.

$app = new App\Core\App($config);
```

This is a startup outline, not a complete implementation: create the App
constructor and route-loading methods to match your actual classes. Load
session configuration before starting the session.

------------------------------------------------------------------------

## 10. Environment and Git protection

### `.env.example`

``` env
APP_NAME="LGU Sibonga Birth Records"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=Asia/Manila

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lgusibonga
DB_USERNAME=lgusibonga_app
DB_PASSWORD=change_this_local_password

SESSION_LIFETIME=30
```

Use a unique, strong password in the real `.env`. Production must use
HTTPS, `APP_DEBUG=false`, and a dedicated least-privilege database
account.

### `.gitignore`

``` gitignore
/vendor/
/node_modules/
.env
/storage/*
!/storage/.gitkeep
/public/uploads/
/.phpunit.cache/
*.log
.DS_Store
```

Never commit real citizen records, uploaded birth documents, backups,
credentials, or production database dumps.

------------------------------------------------------------------------

## 11. Apache and public-directory configuration

Configure the Apache virtual host's `DocumentRoot` to point to:

``` text
C:/laragon/www/lgusibonga/public
```

Do not point the web server to the project root. The project root
contains private configuration, Composer packages, and storage.

Example `public/.htaccess` for Apache with `mod_rewrite` enabled:

``` apache
Options -Indexes
RewriteEngine On

# Do not serve hidden files.
RewriteRule (^|/)\. - [F,L]

# Serve existing public assets directly.
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

# Route all other requests through the front controller.
RewriteRule ^ index.php [QSA,L]

<FilesMatch "\.(env|ini|log|sql|bak|dist)$">
    Require all denied
</FilesMatch>

<IfModule mod_headers.c>
    Header always set X-Content-Type-Options "nosniff"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set X-Frame-Options "DENY"
</IfModule>
```

The preferred protection is the Apache virtual host with `DocumentRoot`
set to `public/`. `.htaccess` is an additional safeguard, not a
substitute for correct server configuration. Test the configuration
before deployment.

------------------------------------------------------------------------

## 12. Suggested development phases

1.  **Foundation:** Composer, autoloading, environment configuration,
    front controller, Router, Request, Response, PDO.
2.  **Security:** Sessions, login/logout, password hashing, CSRF, rate
    limiting, roles, permissions, audit logging.
3.  **Core records:** Database schema, birth record CRUD, parent
    details, barangays, validation, record policies.
4.  **Documents:** Private uploads, metadata, authorized downloads, file
    validation, audit trail.
5.  **Dashboard:** Aggregate queries, Chart.js, filters, and
    privacy-safe statistics.
6.  **Reports:** PDF/Excel exports, access controls, export audit
    events.
7.  **Administration:** User management, permission management,
    settings, backup/restore.
8.  **Testing and deployment:** Authorization tests, validation tests,
    upload tests, restore drills, HTTPS, production configuration.

------------------------------------------------------------------------

## 13. Minimum release checklist

-   [ ] Only `public/` is exposed by Apache.
-   [ ] `.env` and private storage cannot be accessed over HTTP.
-   [ ] All internal routes require authentication.
-   [ ] Each route has a server-side permission check.
-   [ ] Each record-specific route checks record-level authorization.
-   [ ] Every state-changing form validates CSRF.
-   [ ] Database operations use PDO prepared statements.
-   [ ] Passwords are hashed and login attempts are rate-limited.
-   [ ] Uploaded files are validated and stored privately.
-   [ ] Downloads and exports are authorized and audited.
-   [ ] Errors do not reveal SQL, credentials, or personal information.
-   [ ] Production debug mode is disabled and HTTPS is enabled.
-   [ ] Backups are encrypted, access-restricted, and restore-tested.
-   [ ] LGU Civil Registrar and Data Protection Officer approve the
    workflow and access policy.

## 14. Important implementation note

This document defines the target architecture and route security policy.
It does not mean the folders, Router, middleware, controllers, database
tables, or security controls have already been implemented. Build and
test each layer before relying on it. Start with the front controller,
bootstrap, Router, PDO connection, authentication, and permission
middleware; then add the birth-record module.
