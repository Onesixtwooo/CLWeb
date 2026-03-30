# CLSU Web CMS System Tutorial

## 1. System Overview

This system is a Laravel-based CMS for managing:

- college public pages
- departments and institutes
- faculty and facilities
- alumni and testimonials
- scholarships, FAQs, downloads, videos, extensions, and trainings
- news and announcements
- Facebook post syncing
- Google Drive file and image storage

Main technologies:

- PHP 8.2+
- Laravel 12
- MySQL
- Vite
- Node.js / npm
- Google Drive API
- Facebook Graph API

## 2. Project Structure

Important directories:

- `app/` application logic, controllers, models, services
- `resources/views/` Blade templates
- `routes/` web and console routes
- `database/migrations/` Laravel database migrations
- `public/` public assets
- `storage/` logs and framework files
- `docs/` project documentation

Important files:

- `.env` environment configuration
- `composer.json` PHP dependencies and scripts
- `package.json` frontend dependencies
- `vite.config.js` Vite configuration
- `dev-server.bat` local Windows launcher

## 3. Requirements

Install these before setup:

- PHP 8.2 or newer
- Composer
- Node.js and npm
- MySQL or MariaDB
- Git

Recommended local environment:

- Windows
- XAMPP or another local MySQL server
- project path like `d:\htdocs\CLSU`

## 4. First-Time Setup

### 4.1 Clone the project

```bash
git clone https://github.com/Onesixtwooo/CLWeb.git
cd CLWeb
```

### 4.2 Install dependencies

```bash
composer install
npm install
```

### 4.3 Create the environment file

If `.env` does not exist:

```bash
copy .env.example .env
```

### 4.4 Configure `.env`

Minimum required local settings:

```env
APP_NAME="CLSU Web CMS"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8001

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clsu_cis
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

FILESYSTEM_DISK=google

VITE_HMR_HOST=localhost
VITE_HMR_PORT=5173
```

Notes:

- `APP_URL` should match the URL you actually open in the browser.
- This project currently expects local development at `http://localhost:8001`.
- If you are not using Google Drive yet, leave the Google values blank and configure them later.

### 4.5 Generate the app key

```bash
php artisan key:generate
```

### 4.6 Create the database

Create an empty database named `clsu_cis` in MySQL.

Example SQL:

```sql
CREATE DATABASE clsu_cis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4.7 Run migrations

This project uses standard Laravel migrations for fresh setup.

Run:

```bash
php artisan migrate
```

If you want to rebuild everything from scratch:

```bash
php artisan migrate:fresh --force
```

Important:

- `migrate:fresh` will erase existing database tables.
- Use it only for local/dev or when you are sure a reset is safe.

## 5. Default Admin Access

The initial migration creates a default superadmin if it does not already exist:

- Email: `adminCLSU@clsu.edu`

You can also reset or recreate the admin account using:

```bash
php artisan admin:create-user
```

Custom email and password:

```bash
php artisan admin:create-user your@email.com yourpassword
```

Admin login URL:

```text
http://localhost:8001/admin/login
```

## 6. Running the System Locally

### Option A: Use the batch launcher

```bash
dev-server.bat
```

This starts:

- Vite dev server
- Laravel app server on `localhost:8001`

### Option B: Start manually

Terminal 1:

```bash
npm run dev
```

Terminal 2:

```bash
php artisan serve --host=localhost --port=8001
```

Open:

```text
http://localhost:8001
```

## 7. Core Daily Workflow

### 7.1 Login and roles

Main roles in the system:

- `superadmin`
- `admin`
- `editor`

General rule:

- superadmin can manage the whole system
- college admins manage their assigned college
- editors are limited by their college or department assignment

### 7.2 Manage college sections

From the admin area, each college has section-based content such as:

- Overview
- Departments
- Institutes
- Facilities
- Faculty
- Alumni
- Admissions
- FAQs
- Extension
- Training
- Scholarships
- Downloads
- Accreditation
- Membership
- Student Organizations

Each section may support:

- title and body content
- visibility toggles
- structured item management

### 7.3 Manage departments

Departments are nested under a college and usually have their own:

- overview
- faculty
- objectives
- programs
- awards
- research
- linkages
- extension
- training
- facilities
- organizations
- membership
- alumni

### 7.4 Manage alumni

Alumni are stored through the department alumni system and can be surfaced at the college level.

Current behavior:

- college alumni views aggregate alumni records from departments within that college
- college alumni pages now paginate in groups of 5
- compact cards show the essential info only to save space

### 7.5 News and announcements

News and announcements can be created manually in the admin panel or imported from Facebook.

Typical fields:

- title
- body
- author
- publish date
- banner or images
- college slug
- optional department name

### 7.6 Media uploads

This project is configured to use Google Drive for many uploaded files and images.

Media examples:

- faculty photos
- article and announcement images
- downloads
- logos
- section images

## 8. Google Drive Setup

If the system should upload files to Google Drive, set the filesystem disk in `.env`:

```env
FILESYSTEM_DISK=google
```

Then configure the Google Drive credentials in the admin settings page:

- Admin > Settings > Google Drive API Configuration
- Folder ID
- Client ID
- Client Secret
- Refresh Token

Notes:

- This project reads Google Drive credentials from the `settings` table first.
- The `GOOGLE_DRIVE_CLIENT_ID`, `GOOGLE_DRIVE_CLIENT_SECRET`, `GOOGLE_DRIVE_REFRESH_TOKEN`, and `GOOGLE_DRIVE_FOLDER_ID` values in `.env` are fallback values only.
- In normal use, update Google Drive credentials from the admin settings UI instead of editing `.env`.

Then follow the full guide in:

- [GOOGLE_DRIVE_SETUP.md](/d:/htdocs/CLSU/GOOGLE_DRIVE_SETUP.md)

Useful commands:

```bash
php artisan google:check-token
php artisan google:check-token --update
```

## 9. Facebook Integration

Facebook integration can fetch posts and convert them into articles.

Supported scopes:

- global
- college
- department
- organization

Useful command:

```bash
php artisan facebook:fetch-posts --use-db
```

Configuration guide:

- [FACEBOOK_SETUP.md](/d:/htdocs/CLSU/FACEBOOK_SETUP.md)
- [FACEBOOK_WEBHOOK_TUTORIAL.md](/d:/htdocs/CLSU/FACEBOOK_WEBHOOK_TUTORIAL.md)

## 10. Queue, Scheduler, and Logs

### 10.1 Queue

This project uses:

```env
QUEUE_CONNECTION=database
```

Run the queue worker locally if needed:

```bash
php artisan queue:listen --tries=1
```

### 10.2 Scheduler

For automated jobs such as syncing tasks, run:

```bash
php artisan schedule:run
```

For local continuous execution:

```bash
php artisan schedule:work
```

Production cron example:

```text
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### 10.3 Logs

Read logs at:

- `storage/logs/laravel.log`

Live log viewer:

```bash
php artisan pail
```

## 11. Testing and Verification

Run tests:

```bash
php artisan test
```

Lint important PHP files:

```bash
php -l app/Http/Controllers/Admin/CollegeController.php
php -l app/Http/Controllers/CollegePageController.php
```

Build frontend assets for production verification:

```bash
npm run build
```

## 12. Deployment Checklist

Before production deployment:

1. Pull the latest code.
2. Install PHP dependencies:

```bash
composer install --no-dev --optimize-autoloader
```

3. Install frontend dependencies and build:

```bash
npm install
npm run build
```

4. Configure production `.env`.
5. Ensure database credentials are correct.
6. Ensure Google Drive and Facebook credentials are correct if used.
7. Run database setup:

```bash
php artisan migrate --force
```

8. Clear and rebuild caches:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

9. Start queue worker and scheduler.
10. Confirm storage permissions and web server configuration.

## 13. Backup and Recovery

### 13.1 What to back up

At minimum, back up:

- MySQL database
- `.env`
- `database/migrations/`
- any local files not stored in Google Drive

### 13.2 Database backup

Example:

```bash
mysqldump -u root -p clsu_cis > clsu_cis-backup.sql
```

### 13.3 Restore database

Example:

```bash
mysql -u root -p clsu_cis < clsu_cis-backup.sql
```

### 13.4 Recovery strategy

If rebuilding from zero:

1. restore the repository
2. restore `.env`
3. recreate the database
4. run `php artisan migrate`
5. restore database backup if needed
6. verify admin login
7. verify media integrations

## 14. Maintenance Guide

### 14.1 Regular maintenance tasks

Recommended routine:

- monitor `storage/logs/laravel.log`
- verify Google Drive token still works
- verify Facebook tokens still work
- check failed jobs
- take database backups
- test admin login and core public pages

### 14.2 Clear stale caches

```bash
php artisan optimize:clear
```

### 14.3 Rebuild frontend assets

After frontend changes:

```bash
npm run build
```

### 14.4 Update dependencies carefully

PHP dependencies:

```bash
composer update
```

Frontend dependencies:

```bash
npm update
```

After updates:

```bash
php artisan test
npm run build
```

### 14.5 Schema maintenance

This project uses Laravel migration files for schema maintenance.

When the database structure changes:

- add a new migration
- keep migrations committed to version control
- test `php artisan migrate` on a clean database before deployment

## 15. Troubleshooting

### Vite assets not loading

Check:

- `APP_URL=http://localhost:8001`
- `VITE_HMR_HOST=localhost`
- Vite is running on port `5173`
- Laravel is running on port `8001`

Restart both:

```bash
npm run dev
php artisan serve --host=localhost --port=8001
```

### Database setup fails

Check:

- MySQL is running
- database exists
- `.env` credentials are correct

Then retry:

```bash
php artisan migrate
```

### Login fails

Reset admin:

```bash
php artisan admin:create-user
```

### Images fail to upload

Check:

- Google Drive credentials
- refresh token validity
- configured Drive folder ID

Then run:

```bash
php artisan google:check-token --update
```

### Facebook sync fails

Check:

- active Facebook configuration
- page access token
- page ID
- Graph API permissions

Then test:

```bash
php artisan facebook:fetch-posts --use-db
```

## 16. Operational Tips

- Keep `.env` out of version control.
- Back up the database before major structural changes.
- Test Vite, login, uploads, and public pages after deployment.
- Document each schema change and keep its migration in the repository.
- Prefer one stable local URL during development to avoid HMR mismatch issues.

## 17. Recommended Onboarding Flow for New Maintainers

1. Read this file fully.
2. Install dependencies.
3. Configure `.env`.
4. Run `php artisan migrate`.
5. Start `dev-server.bat`.
6. Log in to `/admin/login`.
7. Explore one college page from admin and public view.
8. Test one safe content update.
9. Review Google Drive and Facebook setup if the project uses them.
10. Learn the backup and recovery steps before touching production.
