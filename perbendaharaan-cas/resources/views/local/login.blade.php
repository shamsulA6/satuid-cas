<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk Tempatan — {{ config('app.name') }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.1), 0 4px 16px rgba(0,0,0,.06);
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 400px;
        }

        .badge {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 999px;
            margin-bottom: 1.25rem;
        }

        h1 {
            font-size: 1.375rem;
            font-weight: 600;
            color: #111;
            margin-bottom: .375rem;
        }

        .subtitle {
            font-size: .875rem;
            color: #6b7280;
            margin-bottom: 2rem;
        }

        .field { margin-bottom: 1.125rem; }

        label {
            display: block;
            font-size: .8125rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: .375rem;
        }

        input[type="text"],
        input[type="password"] {
            display: block;
            width: 100%;
            padding: .625rem .875rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: .9375rem;
            color: #111;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }

        input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }

        .btn {
            display: block;
            width: 100%;
            padding: .6875rem 1rem;
            background: #4f46e5;
            color: #fff;
            font-size: .9375rem;
            font-weight: 500;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 1.5rem;
            transition: background .15s;
        }

        .btn:hover { background: #4338ca; }
        .btn:active { background: #3730a3; }

        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            color: #991b1b;
            font-size: .875rem;
            padding: .75rem 1rem;
            margin-bottom: 1.25rem;
        }

        .divider {
            border: none;
            border-top: 1px solid #f3f4f6;
            margin: 1.75rem 0 1.25rem;
        }

        .hint {
            font-size: .8125rem;
            color: #9ca3af;
            text-align: center;
            line-height: 1.5;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="badge">⚠ Local Development</div>

    <h1>Log Masuk Tempatan</h1>
    <p class="subtitle">
        <code>CAS_ENABLED=false</code> — guna akaun tempatan untuk pembangunan.
    </p>

    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('cas.local.login.submit') }}">
        @csrf

        <div class="field">
            <label for="ldap_id">LDAP ID / Username</label>
            <input
                type="text"
                id="ldap_id"
                name="ldap_id"
                value="{{ old('ldap_id') }}"
                placeholder="contoh: ahmad.ali"
                autocomplete="username"
                autofocus
                required
            >
        </div>

        <div class="field">
            <label for="password">Kata Laluan</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="••••••••"
                autocomplete="current-password"
                required
            >
        </div>

        <button type="submit" class="btn">Log Masuk</button>
    </form>

    <hr class="divider">
    <p class="hint">
        Login ini hanya untuk persekitaran <strong>development</strong>.<br>
        Pengesahan sebenar menggunakan SATUID.
    </p>
</div>
</body>
</html>
