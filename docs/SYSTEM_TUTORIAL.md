# CLSU Web CMS System Tutorial

## 1. Purpose

This document is the main onboarding and maintenance guide for the CLSU Web CMS.
It is intended to be the single tutorial file for setup, integrations, daily operations, troubleshooting, and maintenance.

It is written for:

- developers setting up the project locally
- maintainers managing the CMS day to day
- admins preparing deployments
- future team members inheriting the system

This guide reflects the current repository structure and command set as of March 30, 2026.

## 2. System Overview

CLSU Web CMS is a Laravel 12 content management system for managing:

- college public pages
- department and institute pages
- faculty and facilities
- alumni and testimonials
- scholarships, FAQs, downloads, memberships, trainings, and extensions
- news and announcements
- Facebook post syncing
- Google Drive-backed media storage
- admin appearance and settings

Main technologies:

- PHP 8.2+
- Laravel 12
- MySQL or MariaDB
- Vite
- Node.js / npm
- Google Drive API
- Facebook Graph API

## 3. Project Structure

Important directories:

- `app/` application logic, controllers, models, middleware, services, commands
- `bootstrap/` Laravel bootstrap files
- `config/` Laravel configuration
- `database/migrations/` migrations, including the baseline schema loader
- `database/migrations/schema/` SQL baseline schema file for fresh installs
- `docs/` project documentation
- `public/` public web root and static assets
- `resources/views/` Blade templates
- `resources/css/` frontend styles
- `resources/js/` frontend scripts
- `routes/` web and console routes
- `storage/` logs, cache, compiled views, framework files
- `tests/` automated tests

Important files:

- `.env` environment configuration
- `.env.example` default environment template
- `composer.json` PHP dependencies and helper scripts
- `package.json` frontend dependencies
- `vite.config.js` Vite configuration
- `dev-server.bat` Windows launcher for Vite and Laravel
- `dev-server.ps1` PowerShell launcher
- `README.md` quick-start overview
- `ADMIN.md` older admin note file
- `GOOGLE_DRIVE_SETUP.md` Google Drive-specific setup guide
- `FACEBOOK_SETUP.md` Facebook integration guide
- `FACEBOOK_WEBHOOK_TUTORIAL.md` Facebook webhook guide

## 4. Requirements

Install these before local setup:

- PHP 8.2 or newer
- Composer
- Node.js and npm
- MySQL or MariaDB
- Git

Recommended local environment:

- Windows
- XAMPP or another local MySQL server
- project path similar to `d:\htdocs\CLSU`

## 5. First-Time Setup

### 5.1 Clone the project

```bash
git clone https://github.com/Onesixtwooo/CLWeb.git
cd CLWeb
```

### 5.2 Install dependencies

Manual setup:

```bash
composer install
npm install
```

Or use the Composer helper:

```bash
composer setup
```

`composer setup` will:

- install Composer dependencies
- create `.env` if missing
- generate the app key
- run migrations
- install npm dependencies
- build frontend assets

### 5.3 Create the environment file

If `.env` does not exist:

```bash
copy .env.example .env
```

Important:

- `.env.example` currently defaults to SQLite
- this project normally runs on MySQL locally
- update the database settings before running the app

### 5.4 Configure `.env`

Recommended local minimum:

```env
APP_NAME="CLSU Web CMS"
APP_ENV=local
APP_DEBUG=true
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

Notes:

- `APP_URL` should match the exact browser URL you open during development
- the current Windows launcher uses `http://localhost:8001`
- Vite should normally run on `5173`
- Google Drive credentials in `.env` are fallback values; the app can also load them from the database
- Facebook credentials in `.env` may also be overridden by saved settings or Facebook configuration records

### 5.5 Generate the app key

If you did not use `composer setup`, run:

```bash
php artisan key:generate
```

### 5.6 Create the database

Create an empty database.

Example:

```sql
CREATE DATABASE clsu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5.7 Load the schema

Fresh setup uses the baseline schema loader migration:

- `database/migrations/2026_03_30_000000_load_initial_schema.php`
- `database/migrations/schema/initial-schema.sql`

Run:

```bash
php artisan migrate
```

For a full rebuild:

```bash
php artisan migrate:fresh --force
```

Important:

- `migrate:fresh` destroys existing tables
- use it only when a reset is safe
- the baseline loader is intended for fresh installs and simplified onboarding

### 5.8 Optional verification commands

Useful checks after setup:

```bash
php artisan migrate:status
php artisan route:list
php artisan about
```

## 6. Default Admin Access

The system includes a helper command that creates or resets the default superadmin account.

Default command:

```bash
php artisan admin:create-user
```

Current default account:

- Email: `adminCLSU@clsu.edu`
- Password: `!CLSUCi$@_2026`

Custom account:

```bash
php artisan admin:create-user your@email.com yourpassword
```

Admin URLs:

- `http://localhost:8001/admin/login`
- `http://localhost:8001/admin`

## 7. Running the System Locally

### Option A: Use the Windows launcher

```bash
dev-server.bat
```

This starts:

- Vite dev server
- Laravel app server on `localhost:8001`

### Option B: Use Composer dev mode

```bash
composer dev
```

This starts:

- Laravel app server
- queue listener
- `php artisan pail`
- Vite dev server

Note:

- `composer dev` uses the Composer script defaults
- if your team wants a fixed local URL of `http://localhost:8001`, prefer `dev-server.bat` or start Laravel manually on port `8001`

### Option C: Start manually

Terminal 1:

```bash
npm run dev
```

Terminal 2:

```bash
php artisan serve --host=localhost --port=8001
```

Open:

- `http://localhost:8001`
- `http://localhost:8001/admin/login`

## 8. Public Routes and Important URLs

Common routes:

- `/` public homepage
- `/about`
- `/news`
- `/privacy-policy`
- `/facebook/webhook`
- `/admin/login`
- `/admin`

Examples of public college routes:

- `/college/{college}`
- `/college/{college}/faculty`
- `/college/{college}/facilities`
- `/college/{college}/scholarships`
- `/college/{college}/downloads`
- `/college/{college}/news-announcement-board`

## 9. Roles and Access Scope

Main role names:

- `superadmin`
- `admin`
- `editor`

General behavior:

- `superadmin` can access the full system
- `admin` is usually restricted to an assigned college
- `editor` is a limited content role

Scope rules can also narrow a user by:

- college
- department
- organization

This means two users with the same role may still have different visibility depending on their assigned scope.

## 10. Core Daily Workflow

### 10.1 Log in to the admin panel

Use:

- `/admin/login`

After login, the dashboard is:

- `/admin`

### 10.2 Manage college content

College pages may include:

- overview
- departments
- institutes
- facilities
- faculty
- alumni
- downloads
- testimonials
- scholarships
- accreditation
- memberships
- organizations
- extension
- training
- FAQs

Depending on the section, admins may manage:

- text content
- section visibility
- logos and images
- structured list items
- downloads
- featured media

### 10.3 Manage departments and institutes

Departments and institutes can have their own subpages and structured sections such as:

- overview
- objectives
- programs
- awards
- research
- extension
- training
- facilities
- linkages
- alumni
- memberships
- organizations
- staff

### 10.4 Manage alumni

Current behavior:

- alumni are primarily stored through department alumni records
- college alumni pages aggregate alumni records from departments under that college
- college alumni display is currently compact and paginated

### 10.5 Manage news and announcements

Content can be:

- created manually in the admin panel
- synchronized from Facebook

Typical fields include:

- title
- slug
- body
- banner
- image gallery
- author
- publish date
- category
- college slug

### 10.6 Manage users

The system includes admin user management inside the CMS.

Typical maintenance tasks:

- create users
- update user roles
- assign college scope
- assign department scope
- assign organization scope

## 11. Media and Storage Behavior

This project uses Google Drive for many uploaded assets, including:

- faculty images
- college section images
- department media
- article images
- announcement images
- scholarships
- downloads
- organization media

Important storage notes:

- the default filesystem disk is controlled by `FILESYSTEM_DISK`
- many admin uploads use the `google` disk directly
- some appearance assets are stored locally under `public/images/settings`
- some legacy paths still point to local public assets

Optional command for public storage symlink:

```bash
php artisan storage:link
```

This is not the core media path for most CMS uploads, but it is still a normal Laravel maintenance command and may help if future local storage usage expands.

## 12. Google Drive Setup

This project uses Google Drive for many uploaded assets. In the current system, Google Drive credentials are read from the database first and fall back to `.env` only if database values are not set yet.

### 12.1 Required setting

If uploads should go to Google Drive, keep:

```env
FILESYSTEM_DISK=google
```

### 12.2 Prepare Google Cloud

1. Go to `https://console.cloud.google.com/`.
2. Create a new project or select an existing one.
3. Open `APIs & Services -> Library`.
4. Search for `Google Drive API` and enable it.
5. Open `APIs & Services -> Credentials`.
6. Click `Create Credentials -> OAuth client ID`.
7. Choose `Desktop app`.
8. Copy the Client ID and Client Secret.

### 12.3 Create the target Google Drive folder

1. Open `https://drive.google.com/`.
2. Create a folder such as `CLSU Images`.
3. Open the folder and copy the Folder ID from the URL.

Example:

```text
https://drive.google.com/drive/folders/XXXXXXXXX
                                       ^^^^^^^^^
                                       This is the Folder ID
```

### 12.4 Install the dependency if needed

```bash
composer require google/apiclient:^2.19
```

### 12.5 Recommended configuration flow

Use the SuperAdmin settings page as the normal long-term setup path.

1. Sign in as SuperAdmin.
2. Open `Admin > Settings`.
3. Enter:
   - Google Drive Folder ID
   - Google Drive Client ID
   - Google Drive Client Secret
4. Save the settings.
5. Use the Google Drive authorization action in Settings.
6. Sign in to Google and approve access.
7. After the callback completes, the refresh token will be stored in the database.

Typical values used by the application:

- Folder ID
- Client ID
- Client Secret
- Refresh Token

### 12.6 Optional `.env` fallback

If the admin panel is not available yet, these fallback values can be placed in `.env`:

```env
FILESYSTEM_DISK=google
GOOGLE_DRIVE_CLIENT_ID=your_client_id_here
GOOGLE_DRIVE_CLIENT_SECRET=your_client_secret_here
GOOGLE_DRIVE_REFRESH_TOKEN=your_refresh_token_here
GOOGLE_DRIVE_FOLDER_ID=your_folder_id_here
```

Then clear config:

```bash
php artisan config:clear
```

### 12.7 Optional bootstrap script

The repository also includes:

```bash
setup-google-drive.bat
```

This helper script:

- installs `google/apiclient`
- prompts for Client ID, Client Secret, and Folder ID
- generates a refresh token
- updates `.env`
- tests the connection
- clears Laravel config cache

Important:

- this script updates `.env`, not the database
- SuperAdmin Settings is still the preferred ongoing configuration flow

### 12.8 Token maintenance

Useful commands:

```bash
php artisan google:check-token
php artisan google:check-token --update
```

The system can also detect a newer token in `refresh_token.txt` and update the stored refresh token when needed.

If token-related errors appear:

1. generate a new refresh token
2. save it through SuperAdmin Settings or update `.env` if using fallback config
3. clear config cache if needed

If you are using the script-based flow:

- a URL will be generated
- open it in your browser
- sign in and authorize the app
- you may be redirected to `localhost:8080`; that is expected
- copy the `code` parameter from the URL and paste it into the terminal
- the new token will be written to `refresh_token.txt`

### 12.9 Google Drive troubleshooting

If images are not loading:

1. run:

```bash
php artisan google:check-token
```

2. confirm Google Drive is configured in Settings or `.env`
3. clear caches:

```bash
php artisan config:clear
php artisan cache:clear
```

Common causes:

- invalid Folder ID
- expired or revoked refresh token
- Google account used during authorization cannot access the folder
- files or folders have been trashed in Google Drive

Helpful repository files:

- `setup-google-drive.bat`
- `get_refresh_token.php`
- `find_drive_folder.php`
- `refresh_token.txt`

## 13. Facebook Integration

Facebook integration supports:

- global configuration
- college configuration
- department configuration
- organization configuration

Typical use:

- fetch posts from configured pages
- convert them into articles
- associate them with the proper college scope when applicable

### 13.1 Quick global `.env` setup

Add these to `.env`:

```env
FACEBOOK_APP_ID=your_app_id
FACEBOOK_APP_SECRET=your_app_secret
FACEBOOK_ACCESS_TOKEN=your_page_access_token
FACEBOOK_PAGE_ID=your_page_id
FACEBOOK_VERIFY_TOKEN=your_verify_token_here
```

Then fetch posts with:

```bash
php artisan facebook:fetch-posts
```

### 13.2 Recommended multi-entity setup

Recommended maintainer workflow:

- manage page-specific integrations through the admin UI
- use database-backed configs for multi-entity syncing

This project supports per-entity Facebook configuration records in `facebook_configs`.

Important fields:

- `entity_type`
- `entity_id`
- `page_name`
- `page_id`
- `access_token`
- `is_active`
- `fetch_limit`
- `article_category`
- `article_author`

Entity type values:

- `global`
- `college`
- `department`
- `organization`

### 13.3 Create Facebook app credentials

1. Go to `https://developers.facebook.com/`.
2. Create a Meta app or open an existing one.
3. Add the needed Facebook product(s) for your setup.
4. Copy:
   - App ID
   - App Secret

### 13.4 Get page access tokens

For each Facebook page:

1. Open Graph API Explorer.
2. Select your app.
3. Generate a token with the permissions required for your workflow.
4. For scheduled syncing, prefer a proper Page access token.

Common permissions used in this project:

- `pages_show_list`
- `pages_read_engagement`
- `pages_manage_metadata`
- `pages_manage_posts`

### 13.5 Configure through the admin UI

Recommended option:

1. Sign in to the CMS.
2. Open the Facebook configuration area in admin.
3. Create a configuration for `global`, `college`, `department`, or `organization`.
4. Fill in:
   - Entity Type
   - Entity ID
   - Page Name
   - Page ID
   - Access Token
   - Active flag
   - Fetch Limit
   - Article Category
   - Article Author
5. Save the record.

Notes:

- for colleges, `entity_id` is the college slug such as `agriculture`
- for departments and organizations, `entity_id` is usually the database ID

### 13.6 Configure through Tinker if needed

```bash
php artisan tinker
```

Example:

```php
use App\Models\FacebookConfig;

FacebookConfig::create([
    'entity_type' => 'college',
    'entity_id' => 'agriculture',
    'page_name' => 'College of Agriculture',
    'page_id' => 'your_page_id',
    'access_token' => 'your_page_access_token',
    'is_active' => true,
    'fetch_limit' => 5,
    'article_category' => 'College News',
    'article_author' => 'College of Agriculture',
]);
```

### 13.7 Fetch commands

Useful commands:

```bash
php artisan facebook:fetch-posts
php artisan facebook:fetch-posts --limit=10
php artisan facebook:fetch-posts --use-db
php artisan facebook:fetch-posts --use-db --limit=20
```

Configuration can come from:

- `.env`
- saved settings
- `facebook_configs` database records

### 13.8 Scheduled syncing

The system can run Facebook fetches on a schedule through Laravel's scheduler.

Run locally:

```bash
php artisan schedule:work
```

Production cron example:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### 13.9 How Facebook syncing works

Typical article sync flow:

1. the system fetches recent posts from the configured page
2. it converts the post into an article
3. it stores title, body, images, category, author, and scope data
4. it skips posts already processed

### 13.10 Facebook troubleshooting

Common issues:

- no posts fetched because `is_active` is false
- expired or invalid access token
- wrong Page ID
- missing permissions on the token
- duplicate checks preventing re-import of the same post

Useful first checks:

```bash
php artisan facebook:fetch-posts --use-db
php artisan optimize:clear
```

## 14. Facebook Webhook Setup

The project exposes a public webhook endpoint:

- `/facebook/webhook`

This is used for Meta webhook handling when configured.

### 14.1 What happens when it works

1. Meta sends a webhook event to `/facebook/webhook`
2. Laravel verifies the request for the GET challenge
3. Laravel receives POST events for the Page `feed`
4. the Facebook service processes the payload
5. the system matches the event to the correct configured page
6. the post can be converted into an article

### 14.2 Meta app setup

In the Meta app dashboard:

1. open `Webhooks`
2. select the `Page` object
3. set the callback URL
4. set the verify token
5. subscribe the `feed` field

Example callback URL:

```text
https://your-domain-or-ngrok-url/facebook/webhook
```

For local development with ngrok:

```bash
ngrok http 8001
```

Then use the HTTPS ngrok URL as the callback URL in Meta.

### 14.3 Verify token

The token in Meta must match your application setting:

```env
FACEBOOK_VERIFY_TOKEN=your_verify_token_here
```

If the token does not match, Meta verification fails.

### 14.4 Subscribe the actual Facebook Page to the app

App-level webhook setup alone is not enough. The Facebook Page itself must also be subscribed to the app.

Use Graph API Explorer with a proper Page token and check:

```text
{page-id}/subscribed_apps
```

If needed, subscribe the Page with a `POST` request to the same path.

### 14.5 Public reachability requirements

Before enabling webhooks:

- confirm the app is reachable from the public internet
- verify the callback URL in Meta
- verify the verify token
- verify the privacy policy URL if required by Meta
- make sure the target page configuration in the CMS is active

### 14.6 Webhook troubleshooting

If webhooks are not firing:

- confirm `/facebook/webhook` is publicly reachable
- confirm the Meta callback URL is correct
- confirm the verify token matches exactly
- confirm the `feed` field is subscribed
- confirm the Page is subscribed to the app
- confirm the token has the required Page permissions

## 15. Queue, Scheduler, and Logs

### 15.1 Queue

The project uses:

```env
QUEUE_CONNECTION=database
```

Run locally when needed:

```bash
php artisan queue:listen --tries=1
```

Useful queue commands:

```bash
php artisan queue:failed
php artisan queue:retry all
php artisan queue:flush
```

### 15.2 Scheduler

Use for recurring jobs:

```bash
php artisan schedule:run
php artisan schedule:work
php artisan schedule:list
```

Production cron example:

```bash
cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### 15.3 Logs

Read logs at:

- `storage/logs/laravel.log`

Helpful commands:

```bash
php artisan pail
php artisan optimize:clear
```

## 16. Testing and Verification

Run the test suite:

```bash
php artisan test
```

Composer wrapper:

```bash
composer test
```

Lint PHP files:

```bash
php -l app/Http/Controllers/Admin/CollegeController.php
php -l app/Http/Controllers/CollegePageController.php
```

Build frontend assets:

```bash
npm run build
```

Recommended release verification:

- admin login works
- homepage loads
- one college page loads
- one department page loads
- article and announcement pages load
- one upload flow works
- queue and scheduler do not error

## 17. Deployment Checklist

Before production deployment:

1. Pull the latest code.
2. Install PHP dependencies.
3. Install frontend dependencies and build assets.
4. Configure production `.env`.
5. Confirm database credentials.
6. Confirm Google Drive and Facebook credentials if used.
7. Run migrations.
8. Clear and rebuild caches.
9. Start queue workers and scheduler.
10. Verify login, content pages, and media behavior.

Suggested commands:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 18. Backup and Recovery

### 18.1 What to back up

At minimum:

- MySQL database
- `.env`
- `database/migrations/schema/initial-schema.sql`
- any local-only files not stored in Google Drive

### 18.2 Database backup

Example:

```bash
mysqldump -u root -p clsu > clsu-backup.sql
```

### 18.3 Restore database

Example:

```bash
mysql -u root -p clsu < clsu-backup.sql
```

### 18.4 Recovery workflow

If rebuilding from zero:

1. restore the repository
2. restore `.env`
3. recreate the database
4. run `php artisan migrate`
5. restore the database backup if needed
6. recreate or verify the admin user
7. verify login and media integrations

## 19. Maintenance Guide

### 19.1 Routine checks

Recommended routine:

- review `storage/logs/laravel.log`
- confirm admin login still works
- verify Google Drive token status
- verify Facebook tokens and page access
- check queue failures
- test a few public pages
- take database backups

### 19.2 Clear stale caches

```bash
php artisan optimize:clear
```

### 19.3 Rebuild frontend assets

After frontend changes:

```bash
npm run build
```

### 19.4 Update dependencies carefully

PHP:

```bash
composer update
```

Frontend:

```bash
npm update
```

After updates:

```bash
php artisan test
npm run build
```

### 19.5 Schema maintenance

Fresh installs currently rely on the baseline schema migration flow:

- `database/migrations/2026_03_30_000000_load_initial_schema.php`
- `database/migrations/schema/initial-schema.sql`

If the database structure changes in the future, choose one clear strategy:

- add normal migrations after the current baseline
- or regenerate the baseline intentionally

Do not casually replace the baseline file in a live project without confirming the deployment and upgrade path.

## 20. Troubleshooting

### Vite assets not loading

Check:

- `APP_URL=http://localhost:8001`
- `VITE_HMR_HOST=localhost`
- Vite is running on port `5173`
- Laravel is running on port `8001`

Restart:

```bash
npm run dev
php artisan serve --host=localhost --port=8001
```

### Database setup fails

Check:

- MySQL is running
- the database exists
- `.env` credentials are correct
- `.env` is not still using SQLite defaults

Then retry:

```bash
php artisan migrate
```

### Login fails

Reset admin:

```bash
php artisan admin:create-user
```

### Images or uploads fail

Check:

- Google Drive credentials
- refresh token validity
- configured folder ID
- whether credentials are stored in Admin Settings instead of `.env`

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
- whether the system is using `.env`, settings, or database configs

Then test:

```bash
php artisan facebook:fetch-posts --use-db
```

### Queue jobs are stuck or failing

Check:

- `QUEUE_CONNECTION=database`
- jobs table exists
- queue worker is running

Then inspect:

```bash
php artisan queue:failed
```

## 21. Operational Tips

- keep `.env` out of version control
- use one stable local URL during development
- back up the database before structural changes
- prefer admin settings for integration credentials during normal operations
- verify login, uploads, and public pages after deployment
- document database structure changes carefully

## 22. Repo-Specific Helper Files

Useful local helper files and scripts:

- `dev-server.bat`
- `dev-server.ps1`
- `setup-google-drive.bat`
- `get_refresh_token.php`
- `find_drive_folder.php`
- `google-drive-maintenance.php`
- `recover_images.php`

Use these with care and review their contents before running them in a production-like environment.

## 23. Recommended Onboarding Flow

For new maintainers:

1. Read this document fully.
2. Review `README.md`.
3. Review `GOOGLE_DRIVE_SETUP.md` and `FACEBOOK_SETUP.md` if integrations are in use.
4. Install dependencies.
5. Configure `.env`.
6. Run `php artisan migrate`.
7. Start the app with `dev-server.bat` or `composer dev`.
8. Log in at `/admin/login`.
9. Explore one college page in admin and public view.
10. Make one safe content update.
11. Learn backup and recovery steps before touching production.

## 24. Related Documentation

Also review:

- `README.md`
- `ADMIN.md`
- `GOOGLE_DRIVE_SETUP.md`
- `FACEBOOK_SETUP.md`
- `FACEBOOK_WEBHOOK_TUTORIAL.md`

When these documents conflict, prefer:

1. actual application behavior
2. this tutorial
3. older legacy notes
