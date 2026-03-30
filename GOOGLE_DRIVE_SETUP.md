# Google Drive Setup Guide

This project uses Google Drive to store uploaded images such as faculty photos, announcements, and article images.

---

## Recommended Setup

Use the **SuperAdmin Settings** page for Google Drive configuration.

The application reads Google Drive credentials from the database first. If no database values are saved yet, it falls back to `.env`.

### 1. Prepare Google Cloud

1. Go to [Google Cloud Console](https://console.cloud.google.com/).
2. Create a new project or select an existing one.
3. Open **APIs & Services -> Library**.
4. Search for **Google Drive API** and enable it.
5. Open **APIs & Services -> Credentials**.
6. Click **Create Credentials -> OAuth client ID**.
7. Choose **Desktop app**.
8. Copy the **Client ID** and **Client Secret**.

### 2. Create a Drive Folder

1. Open [Google Drive](https://drive.google.com/).
2. Create a folder such as `CLSU Images`.
3. Open the folder and copy the **Folder ID** from the URL:

```text
https://drive.google.com/drive/folders/XXXXXXXXX
                                       ^^^^^^^^^
                                       This is the Folder ID
```

### 3. Install Dependency

If the Google API client is not installed yet, run:

```bash
composer require google/apiclient:^2.19
```

### 4. Save Credentials in SuperAdmin

1. Sign in as **SuperAdmin**.
2. Open the **Settings** page.
3. Enter:
   - Google Drive Folder ID
   - Google Drive Client ID
   - Google Drive Client Secret
4. Save the settings.

### 5. Authorize Google Drive

After saving the Client ID and Client Secret:

1. Use the Google Drive authorization action from the **SuperAdmin Settings** page.
2. Sign in to Google and approve access.
3. After the callback completes, the refresh token will be saved in the database.

---

## Optional Bootstrap Script

You can still use the helper script:

```bash
setup-google-drive.bat
```

What it does:

- Installs `google/apiclient`
- Prompts for Client ID, Client Secret, and Folder ID
- Generates a refresh token
- Updates `.env`
- Tests the connection
- Clears Laravel config cache

Important:

- The batch script updates `.env`, not the database.
- The SuperAdmin Settings page is still the preferred place for long-term configuration.

---

## Manual `.env` Fallback

If you need to configure Google Drive before accessing the admin panel, you can set these values in `.env`:

```env
FILESYSTEM_DISK=google
GOOGLE_DRIVE_CLIENT_ID=your_client_id_here
GOOGLE_DRIVE_CLIENT_SECRET=your_client_secret_here
GOOGLE_DRIVE_REFRESH_TOKEN=your_refresh_token_here
GOOGLE_DRIVE_FOLDER_ID=your_folder_id_here
```

Then clear cached config:

```bash
php artisan config:clear
```

---

## Token Management

The system can detect a newer token in `refresh_token.txt` and update the stored refresh token when needed.

### Manual Token Check

```bash
php artisan google:check-token
```

To update from `refresh_token.txt`:

```bash
php artisan google:check-token --update
```

### When Token Expires

If you get authentication errors:

1. Generate a new refresh token.
2. Save it through the SuperAdmin Settings flow, or update `.env` if you are still using fallback config.
3. Clear config cache if needed.

If you are using the script-based flow, follow the prompts:

- A URL will be generated
- Open it in your browser
- Sign in and authorize the app
- You may be redirected to `localhost:8080`; that is expected
- Copy the `code` parameter from the URL and paste it into the terminal
- The refresh token will be written to `refresh_token.txt`

### Scheduled Check (Optional)

For production environments, you can run a periodic token check:

```bash
# Check daily at 2 AM
0 2 * * * cd /path/to/your/project && php artisan google:check-token --update
```

---

## Troubleshooting

### Images Not Loading

1. Check token/config status:

```bash
php artisan google:check-token
```

2. If Google Drive is not configured, complete the SuperAdmin setup or update `.env`.

3. Clear caches:

```bash
php artisan config:clear
php artisan cache:clear
```

### Permission Errors

- Make sure the selected Google Drive folder is valid.
- Make sure the Google account used during authorization can access that folder.
- Check that the files or folders are not trashed in Google Drive.

### Token Errors

- Refresh tokens may stop working if they are revoked or become inactive.
- Re-authorize Google Drive to generate a new refresh token.
- If you use `refresh_token.txt`, run `php artisan google:check-token --update`.

### Common Problems

| Problem | Solution |
|---------|----------|
| `401 Unauthorized` | Re-authorize Google Drive and save a new refresh token |
| `403 Forbidden` | Make sure Google Drive API is enabled in Google Cloud Console |
| `File not found` | Check that the configured Folder ID matches the target Drive folder |
| Images not showing | Verify that upload/auth completed successfully and the stored file URL is valid |

---

## Verify It Works

Upload an image through the admin panel. If the upload succeeds and the image displays correctly, Google Drive is working.
