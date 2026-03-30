# Admin CMS (Content Management System)

The admin area lets you manage **News** and **Announcements** (articles) for the College of Engineering site.

## Setup

### 1. Database (MySQL)

In your `.env` file, set:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clsu_cis
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database (e.g. `clsu_cis`) in MySQL, then run:

```bash
php artisan migrate
php artisan db:seed
```

### 2. Admin user

**Option A – Create/reset admin (recommended):**

```bash
php artisan admin:create-user
```

This creates or updates the admin user with:

- **Email:** `admin@clsu.edu.ph`
- **Password:** `password`

To use a different email or password:

```bash
php artisan admin:create-user your@email.com yourpassword
```

**Option B – Seed the database:**

```bash
php artisan db:seed
```

The seeder also creates/updates the admin user with email `admin@clsu.edu.ph` and password `password`.

**If login still fails:** run `php artisan admin:create-user admin@clsu.edu.ph password` to reset the admin password, then try again.

### 3. Access

- **Login:** `/admin/login`
- **Dashboard:** `/admin` (after login)
- **Articles:** `/admin/articles` (create, edit, delete News & Announcements)

Only users with `is_admin = true` can access `/admin` (except the login page).

## Articles

- **Type:** News or Announcement
- **Body:** HTML is allowed (e.g. `<p>`, `<strong>`).
- **Banner:** Image filename only (e.g. `banner-graduation.jpg`). Store files in `public/images/news-board/`.
- **Published at:** Optional; used for ordering and display on the front.

The public News & Announcement Board and article detail pages can be wired to use the `Article` model from the database (replace the hardcoded arrays in `routes/web.php` with queries to `Article::`).
