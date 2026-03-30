# CLSU Web CMS

CLSU Web CMS is a Laravel 12 content management system for Central Luzon State University college pages, departments, institutes, news, announcements, alumni, facilities, scholarships, downloads, Facebook syncing, and Google Drive media storage.

## Main Guide

For the full system manual, read:

[docs/SYSTEM_TUTORIAL.md](/d:/htdocs/CLSU/docs/SYSTEM_TUTORIAL.md)

It includes:
- full local setup
- database setup using Laravel migrations
- admin access
- daily content management workflow
- Google Drive and Facebook integration
- deployment and maintenance
- backup and recovery
- troubleshooting

## Quick Start

1. Install dependencies:

```bash
composer install
npm install
```

2. Create `.env` and update database settings.

3. Generate the app key:

```bash
php artisan key:generate
```

4. Create or reset the database schema:

```bash
php artisan migrate
```

5. Start development:

```bash
dev-server.bat
```

Or run manually:

```bash
npm run dev
php artisan serve --host=localhost --port=8001
```

6. Open:

```text
http://localhost:8001
http://localhost:8001/admin/login
```

## Default Superadmin

The initial migration creates a default superadmin account if it does not already exist:

- Email: `adminCLSU@clsu.edu`

You can also reset/create it with:

```bash
php artisan admin:create-user
```

## Additional Docs

- [docs/SYSTEM_TUTORIAL.md](/d:/htdocs/CLSU/docs/SYSTEM_TUTORIAL.md)
- [ADMIN.md](/d:/htdocs/CLSU/ADMIN.md)
- [GOOGLE_DRIVE_SETUP.md](/d:/htdocs/CLSU/GOOGLE_DRIVE_SETUP.md)
- [FACEBOOK_SETUP.md](/d:/htdocs/CLSU/FACEBOOK_SETUP.md)
- [FACEBOOK_WEBHOOK_TUTORIAL.md](/d:/htdocs/CLSU/FACEBOOK_WEBHOOK_TUTORIAL.md)
