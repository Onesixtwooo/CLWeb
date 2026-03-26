<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - {{ config('app.name') }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; max-width: 800px; margin: 0 auto; padding: 40px 20px; background: #f9f9f9; }
        .card { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        h1 { color: #111; font-size: 2rem; margin-bottom: 24px; border-bottom: 2px solid #eee; padding-bottom: 12px; }
        h2 { color: #2c3e50; font-size: 1.4rem; margin: 32px 0 16px; }
        p, li { font-size: 1rem; color: #555; }
        ul { padding-left: 20px; }
        .footer { text-align: center; margin-top: 40px; font-size: 0.9rem; color: #888; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Privacy Policy</h1>
        <p>Effective Date: {{ date('F d, Y') }}</p>

        <h2>1. Information We Collect</h2>
        <p>Our application integrates with the Facebook Graph API to fetch and synchronize feed posts from authorized Facebook Pages belonging to our institution's colleges. We do NOT collect or store personal user data from general visitors.</p>

        <h2>2. How We Use Data</h2>
        <ul>
            <li>To display Facebook posts as articles on our website for academic and news-sharing purposes.</li>
            <li>To keep synchronization active using secure Access Tokens managed exclusively by authorized administrators.</li>
        </ul>

        <h2>3. Data Protection</h2>
        <p>Access tokens and configurations are encrypted and saved securely within our database. We never share, sell, or disclose Facebook graph credentials to third parties.</p>

        <h2>4. Contact Us</h2>
        <p>If you have any questions about this Privacy Policy, please contact the System Administrator.</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</body>
</html>
