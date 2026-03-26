# Facebook Integration Setup Guide

This project automatically captures Facebook page posts and converts them into articles. You can configure Facebook integration for:
- **Global** (all colleges, departments, organizations)
- **College-specific** (individual colleges)
- **Department-specific** (individual departments)
- **Organization-specific** (student organizations)

---

## Quick Setup (Global Configuration)

Add to your `.env` file:

```env
FACEBOOK_APP_ID=your_app_id
FACEBOOK_APP_SECRET=your_secret
FACEBOOK_ACCESS_TOKEN=your_page_token
FACEBOOK_PAGE_ID=your_page_id
```

Then run:

```bash
php artisan facebook:fetch-posts
```

---

## Advanced Setup (Per-Entity Configuration)

### 1. Run Database Migration

```bash
php artisan migrate
```

### 2. Add Facebook Configs via Database

Use the database to configure Facebook for different entities:

#### Global Configuration

```php
use App\Models\FacebookConfig;

FacebookConfig::create([
    'entity_type' => 'global',
    'entity_id' => null,
    'page_name' => 'Main CLSU Page',
    'page_id' => 'your_main_page_id',
    'access_token' => 'your_main_token',
    'is_active' => true,
    'fetch_limit' => 5,
    'article_category' => 'News',
    'article_author' => 'CLSU',
]);
```

#### College-Specific Configuration

```php
FacebookConfig::create([
    'entity_type' => 'college',
    'entity_id' => 'agriculture', // college slug
    'page_name' => 'College of Agriculture',
    'page_id' => 'agriculture_page_id',
    'access_token' => 'agriculture_token',
    'is_active' => true,
    'fetch_limit' => 5,
    'article_category' => 'College News',
    'article_author' => 'College of Agriculture',
]);
```

#### Department-Specific Configuration

```php
FacebookConfig::create([
    'entity_type' => 'department',
    'entity_id' => '1', // department id
    'page_name' => 'Agronomy Department',
    'page_id' => 'agronomy_page_id',
    'access_token' => 'agronomy_token',
    'is_active' => true,
    'fetch_limit' => 5,
    'article_category' => 'Department Updates',
    'article_author' => 'Agronomy Department',
]);
```

#### Organization-Specific Configuration

```php
FacebookConfig::create([
    'entity_type' => 'organization',
    'entity_id' => '1', // organization id
    'page_name' => 'CLSU Student Council',
    'page_id' => 'student_council_page_id',
    'access_token' => 'student_council_token',
    'is_active' => true,
    'fetch_limit' => 3,
    'article_category' => 'Organization News',
    'article_author' => 'Student Council',
]);
```

### 3. Full Setup Steps

#### 1. Create Facebook Apps

For each entity (college, department, organization), create a separate Facebook page and app:

1. Go to [Facebook Developers](https://developers.facebook.com/)
2. Create a new app or select existing
3. Add **Facebook Login** product to your app
4. Go to **Settings → Basic** and copy:
   - **App ID**
   - **App Secret**

#### 2. Get Page Access Tokens

For each Facebook page:

1. Go to [Facebook Graph API Explorer](https://developers.facebook.com/tools/explorer/)
2. Select your app from the dropdown
3. Click **Get Token → Get User Access Token**
4. Select permissions: `pages_read_engagement`, `pages_show_list`
5. Exchange for a long-lived token (60 days) or page access token
6. Copy the **Access Token**

#### 3. Get Page IDs

For each Facebook page:

1. Go to the Facebook page
2. The Page ID is in the URL: `https://www.facebook.com/YOUR_PAGE_NAME`
3. Or use the Graph API Explorer to get page details

#### 4. Configure via Database or Tinker

**Option A: Using Laravel Tinker**

```bash
php artisan tinker
```

Then use the examples above to create FacebookConfig records.

**Option B: Via Admin Dashboard** (Recommended)

1. Go to `/admin/facebook` (requires admin login)
2. Click **Create** to add a new configuration
3. Fill in the form:
   - **Entity Type**: Select `global`, `college`, `department`, or `organization`
   - **Entity ID**: For colleges, use the college slug (e.g., `agriculture`). For departments/organizations, use the ID.
   - **Page Name**: Display name (e.g., "College of Agriculture")
   - **Page ID**: Facebook page ID
   - **Access Token**: Facebook page access token
   - **Active**: Check to enable this configuration
   - **Fetch Limit**: Number of posts to fetch per run (1-100)
   - **Article Category**: Category for created articles
   - **Article Author**: Author name for created articles
4. Click **Save**

You can also **Edit** or **Delete** existing configurations from the list.

---

## Command Usage

### Fetch from Database Configs (Recommended)

```bash
php artisan facebook:fetch-posts --use-db
```

This fetches posts from ALL active Facebook configurations in the database.

### Fetch from Global Config

```bash
php artisan facebook:fetch-posts --limit=10
```

This uses the `.env` global configuration.

### Fetch with Custom Limit

```bash
php artisan facebook:fetch-posts --use-db --limit=20
```

---

## Automatic Scheduling

The system automatically runs every hour using Laravel's scheduler:

```bash
php artisan schedule:work
```

This command should run continuously (in production, use a cron job):

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## How It Works

1. **Fetches Posts**: Retrieves recent posts from configured Facebook pages
2. **Creates Articles**: Converts posts into articles with:
   - **Title**: First line of the post
   - **Body**: Remaining content
   - **Images**: Post images and attachments
   - **Category**: Configured category
   - **Author**: Configured author name
   - **College**: Associated with college if department/org belongs to one
3. **Deduplicates**: Skips posts already processed
4. **Per-Entity**: Each configuration processes independently

---

## Configuration Fields

| Field | Description |
|-------|-------------|
| `entity_type` | `global`, `college`, `department`, or `organization` |
| `entity_id` | College slug, or ID for department/org. `null` for global |
| `page_name` | Display name for logging/admin reference |
| `page_id` | Facebook page ID |
| `access_token` | Facebook page access token |
| `is_active` | Boolean to enable/disable this configuration |
| `fetch_limit` | Number of posts to fetch per run |
| `article_category` | Category for created articles |
| `article_author` | Author name for created articles |

---

## Troubleshooting

- **No posts fetched**: Check that configurations are active (`is_active = true`)
- **API errors**: Verify access tokens haven't expired
- **Images not loading**: Facebook images may have access restrictions
- **Duplicates**: The system checks for existing articles; clear cache if needed

## Security Notes

- Keep access tokens secure — don't commit them to version control
- Use page access tokens instead of user tokens
- Regularly audit active configurations
- Monitor logs for Facebook API errors