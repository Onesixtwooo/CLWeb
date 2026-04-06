# CLSU Web CMS

CLSU Web CMS is a Laravel 12 content management system for Central Luzon State University public pages, colleges, departments, institutes, news, announcements, alumni, facilities, scholarships, Facebook syncing, and Google Drive media storage.

## Main Guide

Use the full system guide for setup, operations, deployment, troubleshooting, and maintenance:

[docs/SYSTEM_TUTORIAL.md](/d:/htdocs/CLSU/docs/SYSTEM_TUTORIAL.md)

## Quick Start

1. Install backend and frontend dependencies:

```bash
composer install
npm install
```

Or run the helper:

```bash
composer setup
```

2. Create `.env` if needed, then update it for MySQL and local development.

Recommended local values:

```env
APP_URL=http://localhost:8001
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clsu
DB_USERNAME=root
DB_PASSWORD=
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=google
VITE_HMR_HOST=localhost
VITE_HMR_PORT=5173
```

3. Generate the app key if needed:

```bash
php artisan key:generate
```

4. Run the schema setup:

```bash
php artisan migrate
```

Fresh installs use the baseline schema loader in:

- [database/migrations/2026_03_30_000000_load_initial_schema.php](/d:/htdocs/CLSU/database/migrations/2026_03_30_000000_load_initial_schema.php)
- [database/migrations/schema/initial-schema.sql](/d:/htdocs/CLSU/database/migrations/schema/initial-schema.sql)

5. Start development:

```bash
dev-server.bat
```

Or run manually:

```bash
npm run dev
php artisan serve --host=localhost --port=8001
```

You can also use:

```bash
composer dev
```

6. Open:

- `http://localhost:8001`
- `http://localhost:8001/admin/login`

## Default Superadmin

Create or reset the admin account with:

```bash
php artisan admin:create-user
```

Current defaults:

- Email: `adminCLSU@clsu.edu`
- Password: `!CLSUCi$@_2026`

Custom credentials:

```bash
php artisan admin:create-user your@email.com yourpassword
```

## Common Commands

```bash
php artisan test
php artisan google:check-token
php artisan facebook:fetch-posts --use-db
php artisan queue:listen --tries=1
php artisan schedule:work
npm run build
```

## Additional Docs

- [docs/SYSTEM_TUTORIAL.md](/d:/htdocs/CLSU/docs/SYSTEM_TUTORIAL.md)
- [ADMIN.md](/d:/htdocs/CLSU/ADMIN.md)
- [GOOGLE_DRIVE_SETUP.md](/d:/htdocs/CLSU/GOOGLE_DRIVE_SETUP.md)
- [FACEBOOK_SETUP.md](/d:/htdocs/CLSU/FACEBOOK_SETUP.md)
- [FACEBOOK_WEBHOOK_TUTORIAL.md](/d:/htdocs/CLSU/FACEBOOK_WEBHOOK_TUTORIAL.md)
