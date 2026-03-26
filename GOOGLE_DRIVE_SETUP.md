# Google Drive Setup Guide

This project uses Google Drive to store uploaded images (faculty photos, announcements, articles, etc.).

---

## Quick Setup (Automated)

Double-click or run:
```
setup-google-drive.bat
```
The script will walk you through everything step-by-step.

---

## Manual Setup

### 1. Google Cloud Console

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project (or select existing)
3. Go to **APIs & Services → Library**, search **Google Drive API**, and **Enable** it
4. Go to **APIs & Services → Credentials**
5. Click **Create Credentials → OAuth client ID**
6. Application type: **Desktop app**
7. Copy the **Client ID** and **Client Secret**

### 2. Create a Drive Folder

1. Open [Google Drive](https://drive.google.com/)
2. Create a folder (e.g., `CLSU Images`)
3. Open the folder — copy the **Folder ID** from the URL:
   ```
   https://drive.google.com/drive/folders/XXXXXXXXX
                                          ^^^^^^^^^
                                          This is the Folder ID
   ```

### 3. Install Dependencies

```bash
composer require google/apiclient:^2.19
```

### 4. Get Refresh Token

```bash
php get_refresh_token.php
```

### 5. Update Configuration

The refresh token will be automatically updated in your `.env` file and database when you run the setup script.

---

## Token Management

### Automatic Token Updates

The system now automatically detects and updates expired tokens:

- When the application starts, it checks for a new token in `refresh_token.txt`
- If a new token is found, it automatically updates the stored token
- No manual intervention required for token renewal

### Manual Token Check

To manually check token status:

```bash
php artisan google:check-token
```

To update token from file:

```bash
php artisan google:check-token --update
```

### When Token Expires

If you see authentication errors:

1. Run: `php get_refresh_token.php`
2. Follow the authorization flow
3. The new token will be automatically applied

### Scheduled Checks (Optional)

For production environments, you can set up a cron job to periodically check token status:

```bash
# Check daily at 2 AM
0 2 * * * cd /path/to/your/project && php artisan google:check-token --update
```

---

## Troubleshooting

### Images Not Loading

1. Check if Google Drive service is configured:
   ```bash
   php artisan google:check-token
   ```

2. If not configured, run the token setup:
   ```bash
   php get_refresh_token.php
   ```

3. Clear application cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

### Permission Errors

- Ensure the Google Drive folder is shared with your Google account
- The service account needs read access to all files
- Check that files are not trashed in Google Drive

### Token Errors

- Refresh tokens expire after 6 months of inactivity
- Re-run `php get_refresh_token.php` to get a new token
- The system will automatically use the new token

Follow the prompts:
- A URL will be generated — open it in your browser
- Sign in and authorize the app
- You'll be redirected to `localhost:8080` (it will say "can't be reached" — **that's OK**)
- Copy the `code` parameter from the URL bar and paste it back in the terminal
- The refresh token will be saved to `refresh_token.txt`

### 5. Update `.env`

```env
FILESYSTEM_DISK=google
GOOGLE_DRIVE_CLIENT_ID=your_client_id_here
GOOGLE_DRIVE_CLIENT_SECRET=your_client_secret_here
GOOGLE_DRIVE_REFRESH_TOKEN=your_refresh_token_here
GOOGLE_DRIVE_FOLDER_ID=your_folder_id_here
```

### 6. Clear Config Cache

```bash
php artisan config:clear
```

---

## Verify It Works

Upload an image through the admin panel (e.g., add a faculty photo or article image). If it uploads without errors, Google Drive is working!

## Troubleshooting

| Problem | Solution |
|---------|----------|
| `401 Unauthorized` | Refresh token expired — run `php get_refresh_token.php` again |
| `403 Forbidden` | Check that Google Drive API is enabled in Cloud Console |
| `File not found` | Verify the Folder ID in `.env` matches your Drive folder |
| Images not showing | Make sure the folder has public sharing or use the proxy route |
