<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - {{ config('app.name', 'CLSU CMS') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        :root {
            --admin-bg: #e8f5e9;
            --admin-accent: #198754;
            --admin-accent-hover: #157347;
            --admin-accent-soft: rgba(25, 135, 84, 0.12);
            --admin-gradient: linear-gradient(135deg, #0d6e42 0%, #198754 35%, #20c997 70%, #198754 100%);
        }
        * { -webkit-font-smoothing: antialiased; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            background: var(--admin-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #d4edda;
            box-shadow: 0 4px 24px rgba(25, 135, 84, 0.08);
            overflow: hidden;
        }
        .login-card .form-control {
            border-radius: 10px;
            border-color: #e2e8f0;
            padding: 0.625rem 1rem;
        }
        .login-card .form-control:focus {
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 3px var(--admin-accent-soft);
        }
        .btn-login {
            background: var(--admin-gradient);
            border: 0;
            color: #fff;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            transition: filter 0.2s ease, transform 0.15s ease;
        }
        .btn-login:hover {
            filter: brightness(1.08);
            color: #fff;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-card card border-0">
                    <div class="card-body p-5">
                        <h1 class="h4 fw-700 mb-2 text-center" style="letter-spacing: -0.02em;">Admin Login</h1>
                        <p class="text-muted small text-center mb-4">{{ config('app.name', 'CLSU') }} Content Management</p>

                        @if ($errors->any())
                            <div class="alert alert-danger rounded-3 border-0 mb-4">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label fw-500">Email</label>
                                <input type="email" name="email" id="email" class="form-control form-control-lg"
                                       value="{{ old('email') }}" required autofocus autocomplete="email">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label fw-500">Password</label>
                                <input type="password" name="password" id="password" class="form-control form-control-lg" required autocomplete="current-password">
                            </div>
                            <div class="mb-4">
                                <div class="form-check">
                                    <input type="checkbox" name="remember" id="remember" class="form-check-input rounded" {{ old('remember') ? 'checked' : '' }}>
                                    <label for="remember" class="form-check-label">Remember me</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-login btn-lg w-100">Sign in</button>
                        </form>

                        <p class="text-center mt-4 mb-0">
                            <a href="{{ url('/') }}" class="text-muted small text-decoration-none">← Back to site</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
