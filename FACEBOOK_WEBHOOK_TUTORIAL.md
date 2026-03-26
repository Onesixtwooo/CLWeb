# Facebook Webhook Setup Tutorial

This guide explains how to set up Facebook Page webhooks for this CLSU Laravel project so that each published Page post can be saved as an article.

This document covers:
- Meta app setup
- Page webhook subscription
- Local/server-side Laravel setup
- How this project processes Facebook posts
- Troubleshooting

## 1. What This Project Uses

This project exposes this webhook endpoint:

```text
/facebook/webhook
```

Current route:

```php
Route::match(['get', 'post'], 'facebook/webhook', [\App\Http\Controllers\FacebookWebhookController::class, 'handle']);
```

Relevant files:
- `routes/web.php`
- `app/Http/Controllers/FacebookWebhookController.php`
- `app/Services/FacebookService.php`
- `app/Models/FacebookConfig.php`

## 2. What Happens When It Works

The flow is:

1. Meta sends a webhook event to `/facebook/webhook`
2. Laravel verifies the request for the GET challenge
3. Laravel receives POST webhook events for the Page `feed`
4. `FacebookService` processes the payload
5. The system matches the event to the correct `facebook_configs.page_id`
6. The Facebook post is converted into an `articles` row
7. The post text and images are displayed on the site

## 3. Meta Side Setup

### Step 1. Create or Open a Meta App

Go to:

```text
https://developers.facebook.com/
```

Use an app for your Facebook Page integration.

### Step 2. Add Webhooks

In the Meta App Dashboard:

1. Open `Webhooks`
2. Select the `Page` object
3. Set the callback URL
4. Set the verify token
5. Subscribe the `feed` field

Example callback URL:

```text
https://your-domain-or-ngrok-url/facebook/webhook
```

Example local development callback URL using ngrok:

```text
https://your-ngrok-subdomain.ngrok-free.app/facebook/webhook
```

### Step 3. Set the Verify Token

The verify token in Meta must match your Laravel app token:

```env
FACEBOOK_VERIFY_TOKEN=your_verify_token_here
```

If the token does not match, Meta verification fails.

### Step 4. Subscribe the `feed` Field

Inside `Webhooks -> Page`, make sure:

- `feed` is set to `Subscribed`

Without this, Page posts will not trigger webhook events.

## 4. Subscribe the Actual Facebook Page to the App

This is separate from subscribing the `feed` field.

You need both:
- App-level webhook field subscription
- Page-level app subscription

### Required Permissions

When using Graph API Explorer, generate a token with:

- `pages_show_list`
- `pages_read_engagement`
- `pages_manage_metadata`
- `pages_manage_posts`

### Check if the Page Is Subscribed

In Graph API Explorer:

- Method: `GET`
- Path:

```text
{page-id}/subscribed_apps
```

Example:

```text
1061969980324250/subscribed_apps
```

Important:
- Use the actual Page token, not just a generic user token
- In Graph API Explorer, choose the Page under `User or Page`

Expected result if subscribed:

```json
{
  "data": [
    {
      "id": "YOUR_APP_ID",
      "name": "YOUR_APP_NAME"
    }
  ]
}
```

If you get:

```json
{
  "data": []
}
```

the Page is not subscribed to the app.

### Subscribe the Page

In Graph API Explorer:

- Method: `POST`
- Path:

```text
{page-id}/subscribed_apps
```

Example:

```text
1061969980324250/subscribed_apps
```

Then run the GET check again.

## 5. Laravel Server-Side Setup

### Step 1. Add Environment Variables

Add these to `.env`:

```env
FACEBOOK_APP_ID=your_app_id
FACEBOOK_APP_SECRET=your_app_secret
FACEBOOK_ACCESS_TOKEN=your_page_access_token
FACEBOOK_PAGE_ID=your_page_id
FACEBOOK_VERIFY_TOKEN=your_verify_token_here
```

Notes:
- `FACEBOOK_VERIFY_TOKEN` is for webhook verification
- `FACEBOOK_ACCESS_TOKEN` is used to fetch Page posts or full post details
- `FACEBOOK_PAGE_ID` is the Facebook Page ID

### Step 2. Set Up Per-Page Config in the Database

This project supports per-entity Facebook configurations in the `facebook_configs` table.

Fields used:
- `entity_type`
- `entity_id`
- `page_name`
- `page_id`
- `access_token`
- `is_active`
- `fetch_limit`
- `article_category`
- `article_author`

Example:

```php
use App\Models\FacebookConfig;

FacebookConfig::create([
    'entity_type' => 'college',
    'entity_id' => 'engineering',
    'page_name' => 'College of Engineering',
    'page_id' => '1061969980324250',
    'access_token' => 'YOUR_PAGE_ACCESS_TOKEN',
    'is_active' => true,
    'fetch_limit' => 5,
    'article_category' => 'College News',
    'article_author' => 'College of Engineering',
]);
```

Important:
- `page_id` must match the Page that will post
- `is_active` must be `true`

### Step 3. Make Sure the Webhook Route Is Public

Meta must reach:

```text
https://your-public-domain/facebook/webhook
```

For local development, use ngrok:

```bash
ngrok http 8000
```

Then copy the HTTPS ngrok URL into Meta.

If ngrok restarts, the URL usually changes. Update the Meta callback URL when that happens.

## 6. Current Server Implementation

### Webhook Controller

Current controller:

- `app/Http/Controllers/FacebookWebhookController.php`

It does:

- GET verification for Meta challenge
- POST receive for webhook events
- logs the payload
- forwards the payload to `FacebookService`

### Facebook Service

Current service:

- `app/Services/FacebookService.php`

It does:

- process webhook payloads
- listen for `feed` changes
- ignore unsupported or unpublished events
- match webhook events to the correct `FacebookConfig`
- fetch full post details from Graph API when needed
- create or update an article
- capture text and images

## 7. How Article Saving Works

When a valid `feed` event is received:

- Facebook post ID becomes article slug:

```text
fb-{post_id}
```

- Article `title` comes from the first line of the post
- Article `body` comes from the remaining content
- Article `banner` comes from the first detected image
- Article `images` stores all detected image URLs
- `college_slug` is resolved from the Facebook config

This prevents duplicate articles for the same Facebook post.

If the post is edited later, the same article can be updated.

## 8. How to Test

### Test 1. Meta Verification

In Meta Webhooks, click verify and save.

Expected:
- Meta verification succeeds
- Laravel responds with the `hub_challenge`

### Test 2. Meta Feed Test

In the Meta Webhooks dashboard, use the `Test` button on the `feed` field.

Expected:
- ngrok receives a POST request
- Laravel logs the webhook
- an article may be created if the payload contains enough data or can be resolved through Graph API

### Test 3. Real Facebook Page Post

Create a real published post on the connected Facebook Page.

Expected:
- Meta sends POST `/facebook/webhook`
- the project creates an article automatically

## 9. Logs to Check

Project logs:

- `storage/logs/laravel.log`
- `storage/logs/facebook_webhook_debug.txt`

What to check:
- whether GET verification hit the endpoint
- whether POST events reached the server
- whether the payload was processed
- whether article creation succeeded
- whether Graph API returned an error

## 10. Common Problems

### Problem: Meta test works but real post does not

Check:
- `feed` is subscribed
- the Page is subscribed to the app
- the webhook URL is still correct
- the ngrok URL has not changed
- the Facebook Page token is still valid
- the correct `page_id` is stored in `facebook_configs`
- the config is active
- the post is actually published

### Problem: `GET /{page-id}/subscribed_apps` returns empty data

Example:

```json
{
  "data": []
}
```

This means the Page is not subscribed to the app yet.

Run:

- `POST /{page-id}/subscribed_apps`

using a Page token with:

- `pages_manage_metadata`

### Problem: Meta cannot verify the webhook

Check:
- callback URL is correct
- route exists
- verify token matches
- app is publicly reachable
- HTTPS is valid

### Problem: No article is created

Check:
- `facebook_configs.page_id` matches the posting Page
- `facebook_configs.access_token` is valid
- `facebook_configs.is_active = true`
- Laravel logs show no Graph API errors

### Problem: Text formatting looks unarranged

The frontend now preserves line breaks for plain-text article bodies in:

- `resources/views/news-announcement-detail.blade.php`

### Problem: Images do not appear

The importer tries to capture:
- `full_picture`
- attachment images
- attachment URLs

If images are missing, inspect the Graph API response for that specific Facebook post.

## 11. Recommended Deployment Notes

For production:

- use a stable HTTPS domain instead of ngrok
- keep the callback URL permanent
- store Page tokens securely
- monitor `laravel.log`
- rotate expired tokens when needed

## 12. Quick Checklist

- Meta app exists
- Webhooks product is configured
- `Page` object selected
- `feed` field subscribed
- callback URL points to `/facebook/webhook`
- verify token matches `FACEBOOK_VERIFY_TOKEN`
- Page is subscribed via `/{page-id}/subscribed_apps`
- Page token has required permissions
- `facebook_configs` contains the correct Page ID and token
- config is active
- real published Page post triggers an article

## 13. Related Project Files

- `FACEBOOK_SETUP.md`
- `routes/web.php`
- `app/Http/Controllers/FacebookWebhookController.php`
- `app/Services/FacebookService.php`
- `app/Models/FacebookConfig.php`

## 14. Official References

Official Meta documentation:

- https://developers.facebook.com/docs/graph-api/webhooks/reference/page/
- https://developers.facebook.com/docs/graph-api/reference/page/subscribed_apps/
- https://developers.facebook.com/docs/graph-api/reference/app/subscriptions/

