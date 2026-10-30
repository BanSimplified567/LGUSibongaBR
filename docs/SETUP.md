# Local setup

## Requirements

- PHP 8.4 or newer with PDO MySQL and the PHP extensions required by Composer.
- Composer 2.
- MySQL 8.

## Start the application

1. Copy `.env.example` to `.env` and set the local database credentials. Do not commit `.env`.
2. Create the database and a least-privilege application account in MySQL.
3. Apply `database/schema.sql` to that database. This creates authentication, permission, and audit tables; it does not add example citizens, users, roles, or permission grants.
4. Create an authorized user and grant only the LGU-approved permissions through a trusted administrative process. A user without `dashboard.view` cannot access the dashboard.
5. From the project root, run:

   ```powershell
   php -S 127.0.0.1:8000 -t public
   ```

6. Open `http://127.0.0.1:8000/login`.

The login page and routing can be exercised before the database is configured, but authentication requires the MySQL schema and an approved account. Birth-record and document routes are intentionally not enabled until the LGU confirms the workflow and authorization policy.

For Apache, set the virtual host `DocumentRoot` to the project's `public` directory. Do not expose the project root.

## Scope and production

This is the foundation and authentication stage, not a complete civil-registry records system. It intentionally does not seed privileged users or make assumptions about role assignments. Use HTTPS in production, set `APP_DEBUG=false`, restrict database privileges, and keep storage outside the web root.
