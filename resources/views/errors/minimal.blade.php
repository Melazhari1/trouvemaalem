<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Error') — trouvemaalem</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .error-card {
            text-align: center;
            max-width: 480px;
            width: 100%;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, #113559, #d78126);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
        }
        .error-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #64748b;
            font-size: 1.05rem;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }
        .btn {
            display: inline-block;
            padding: 0.85rem 2rem;
            background: linear-gradient(135deg, #d78126, #cba346);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(215, 129, 38, 0.35);
        }
        .logo {
            display: block;
            font-size: 1.25rem;
            font-weight: 900;
            color: #113559;
            text-decoration: none;
            margin-bottom: 3rem;
            letter-spacing: -0.03em;
        }
        .logo span { color: #d78126; }
    </style>
</head>
<body>
    <div class="error-card">
        <a href="{{ url('/') }}" class="logo">trouve<span>maalem</span></a>
        <div class="error-code">@yield('code', '?')</div>
        <h1 class="error-title">@yield('title', 'An error occurred')</h1>
        <p class="error-message">@yield('message', 'Something went wrong.')</p>
        @yield('action')
    </div>
</body>
</html>
