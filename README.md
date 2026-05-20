# perbendaharaan/cas-auth

Pakej Laravel untuk integrasi **CAS Authentication (SATUID)** bagi sistem-sistem di bawah **Perbendaharaan Malaysia**.

Diedarkan melalui **OSDEC Package Registry** — tidak tersedia di Packagist awam.

---

## Keperluan Sistem

| Perkara | Versi Minimum | Disyorkan |
|---------|--------------|-----------|
| PHP | **8.4** | 8.4.x (production) |
| Laravel | **12.x** | 12.x terkini |
| Composer | 2.x | 2.x terkini |
| Akses SATUID | — | URL aplikasi mesti didaftarkan |

> Pakej ini menggunakan `declare(strict_types=1)` dan ciri PHP 8.4. PHP 8.2/8.3 tidak disokong.

---

## Pemasangan

### Langkah 1 — Tambah OSDEC registry dalam `composer.json` projek

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://packages.osdec.gov.my"
        }
    ]
}
```

> Jika organisasi menggunakan self-hosted GitLab/Gitea OSDEC, guna jenis `"vcs"`:
> ```json
> {
>     "repositories": [
>         {
>             "type": "vcs",
>             "url": "https://git.osdec.gov.my/perbendaharaan/cas-auth"
>         }
>     ]
> }
> ```

### Langkah 2 — Install pakej

```bash
composer require perbendaharaan/cas-auth:^1.0
```

### Langkah 3 — Jalankan perintah pemasangan

```bash
php artisan cas:install
```

Perintah ini akan:
- Terbitkan `config/cas.php`
- Terbitkan `.env.cas.example`
- Terbitkan fail migration
- Terbitkan views login tempatan

### Langkah 4 — Konfigurasi `.env`

Salin dari `.env.cas.example` yang diterbitkan, kemudian isi nilai:

```env
# Toggle: true = SATUID (prod), false = login tempatan (dev)
CAS_ENABLED=true

CAS_HOSTNAME=satuid.treasury.gov.my
CAS_PORT=443
CAS_URI=/gk

# URL aplikasi — WAJIB didaftarkan dengan pihak SATUID
CAS_CLIENT_SERVICE=https://nama-sistem-anda.treasury.gov.my

CAS_REDIRECT_PATH=/dashboard
CAS_LOGOUT_URL=https://satuid.treasury.gov.my/gk/logout
CAS_LOGOUT_REDIRECT=https://nama-sistem-anda.treasury.gov.my
CAS_VALIDATE_PATH=/gk/serviceValidate

# 'ca' untuk production, 'none' untuk development
CAS_VALIDATION=ca
```

### Langkah 5 — Jalankan migration

```bash
php artisan migrate
```

Ini menambah kolum `id_pgn_ldap` ke jadual `users`.

### Langkah 6 — Pilih middleware dalam `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;

// Satu baris — auto-detect berdasarkan CAS_ENABLED dalam .env
$mid = config('cas.cas_enabled') ? ['cas.auth'] : ['cas.local'];

Route::middleware($mid)->group(function (): void {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
    // ... route-route lain yang memerlukan authentication
});
```

### Langkah 7 — Butang logout dalam blade

```blade
<form method="POST" action="{{ route('cas.logout') }}">
    @csrf
    <button type="submit">Log Keluar</button>
</form>
```

---

## Middleware

Pakej menyediakan dua middleware yang didaftarkan secara automatik:

| Alias | Kelas | Guna untuk |
|-------|-------|-----------|
| `cas.auth` | `AuthCas` | Production & staging — pengesahan melalui SATUID |
| `cas.local` | `AuthLocal` | Development tempatan — borang login `id_pgn_ldap` + password |

---

## Development Tempatan (`CAS_ENABLED=false`)

Apabila `CAS_ENABLED=false`, pakej menyediakan aliran login tempatan — **tanpa perlu akses ke SATUID**.

| Perkara | Production | Development |
|---------|-----------|-------------|
| Middleware | `cas.auth` | `cas.local` |
| Login | Redirect ke SATUID | Borang `/local-login` |
| Credential | CAS ticket | `id_pgn_ldap` + `password` |
| Logout | Redirect ke SATUID logout | Redirect ke `/local-login` |

### Tambah user untuk testing tempatan

```php
// php artisan tinker
\App\Models\User::create([
    'name'        => 'Ahmad Ali',
    'email'       => 'ahmad.ali@treasury.gov.my',
    'password'    => bcrypt('password'),
    'id_pgn_ldap' => 'ahmad.ali',
]);
```

Login dengan: LDAP ID = `ahmad.ali`, Kata Laluan = `password`

### Customise halaman login tempatan

```bash
php artisan vendor:publish --tag=cas-views
```

View disalin ke `resources/views/vendor/cas-auth/local/login.blade.php`.

---

## Semua Pemboleh Ubah `.env`

| Pemboleh Ubah | Keterangan | Default |
|---------------|------------|---------|
| `CAS_ENABLED` | `true` = SATUID, `false` = local | `false` |
| `CAS_HOSTNAME` | Hos SATUID | `satuid.treasury.gov.my` |
| `CAS_PORT` | Port SATUID | `443` |
| `CAS_URI` | Base URI CAS | `/gk` |
| `CAS_CLIENT_SERVICE` | URL aplikasi (daftar di SATUID) | nilai `APP_URL` |
| `CAS_REDIRECT_PATH` | Redirect selepas login | `/dashboard` |
| `CAS_LOGOUT_URL` | URL logout SATUID | `https://satuid.treasury.gov.my/gk/logout` |
| `CAS_LOGOUT_REDIRECT` | Redirect selepas logout | nilai `APP_URL` |
| `CAS_VALIDATE_PATH` | Endpoint validasi ticket | `/gk/serviceValidate` |
| `CAS_VALIDATION` | SSL: `ca` (prod) / `none` (dev) | `none` |
| `CAS_LDAP_COLUMN` | Kolum LDAP dalam jadual users | `id_pgn_ldap` |
| `CAS_USER_MODEL` | FQCN model User | `App\Models\User` |

---

## Routes Pakej

| Method | URL | Nama route | Keterangan |
|--------|-----|-----------|------------|
| `POST` | `/cas-logout` | `cas.logout` | Log keluar (auto-detect prod/local) |
| `GET` | `/auth-status` | `cas.status` | Status auth dalam JSON (debugging) |
| `GET` | `/local-login` | `cas.local.login` | Borang login tempatan |
| `POST` | `/local-login` | `cas.local.login.submit` | Proses login tempatan |

---

## Model User

Tambah `id_pgn_ldap` dalam `$fillable`:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'id_pgn_ldap',  // ← wajib ada
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
}
```

---

## Aliran CAS — Production

```
Pengguna akses URL dilindungi
        │
        ▼
[Middleware: cas.auth]
        │
        ├── Auth::check() = true ─────────────────► Lulus ke controller
        │
        ├── ?ticket= ada dalam URL
        │       │
        │       ▼
        │   GET https://satuid.treasury.gov.my/gk/serviceValidate
        │       ?service=<URL>&ticket=<TICKET>
        │       │
        │       ├── authenticationSuccess
        │       │       └── Cari user WHERE id_pgn_ldap = <ldap_id>
        │       │               ├── Dijumpai → Auth::login() → redirect
        │       │               └── Tidak dijumpai → abort(401)
        │       │
        │       └── authenticationFailure → abort(401)
        │
        └── Tiada ticket
                └── redirect → https://satuid.../gk/login?service=<URL>
```

---

## Debugging

Log tersimpan di `storage/logs/laravel.log`:

```
CAS: User already authenticated   {"user_id": 42}
CAS: Ticket received
CAS: Validating ticket            {"ticket": "ST-..."}
CAS: Authentication successful    {"ldap_id": "ahmad.ali"}
CAS: LDAP ID not found in database {"ldap_id": "ahmad.ali"}
CAS: Failed to reach CAS server   {"url": "https://..."}
CAS: Redirecting to SATUID login  {"url": "https://..."}
```

Semak status: `GET /auth-status`

---

## Penerbitan ke OSDEC (Untuk Penyelenggara Pakej)

> Bahagian ini untuk kakitangan ICT Perbendaharaan yang menyelenggara pakej ini.

### Workflow Git

```bash
# 1. Clone repo
git clone https://git.osdec.gov.my/perbendaharaan/cas-auth.git
cd cas-auth

# 2. Buat perubahan dan commit
git add .
git commit -m "feat: tambah sokongan fitur X"

# 3. Tag versi mengikut Semantic Versioning
git tag v1.1.0
git push origin main
git push origin v1.1.0
```

### Semantic Versioning

| Jenis perubahan | Contoh versi |
|----------------|-------------|
| Patch (bugfix) | `v1.0.0` → `v1.0.1` |
| Minor (feature baharu, backward-compatible) | `v1.0.1` → `v1.1.0` |
| Major (breaking change) | `v1.1.0` → `v2.0.0` |

### Struktur repo yang diperlukan

```
cas-auth/                   ← root repo
├── src/
│   ├── CasAuthServiceProvider.php
│   ├── Config/cas.php
│   ├── Http/Middleware/
│   │   ├── AuthCas.php
│   │   └── AuthLocal.php
│   └── Console/Commands/CasInstall.php
├── database/
│   └── migrations/
├── resources/
│   └── views/local/login.blade.php
├── routes/
│   └── web.php
├── composer.json           ← WAJIB ada di root
├── README.md
├── .env.cas.example
└── .gitignore
```

> **Penting:** Jangan commit folder `vendor/`, fail `.env`, atau credentials dalam repo.

---

## Keperluan PHP 8.4

Pakej menggunakan ciri PHP 8.4:

- `declare(strict_types=1)` — semua fail
- `final class` — middleware & service provider
- Named arguments: `in_array(..., strict: true)`
- `#[AsCommand]` attribute pada artisan command
- `static` closures dalam migration & routes
- Nullsafe operator `?->` untuk optional chaining
- `never` return type pada logout route

---

## Lesen

MIT License — Bahagian ICT, Perbendaharaan Malaysia.
