# satuid-cas

[![GitHub license](https://img.shields.io/github/license/shamsulA6/satuid-cas)](https://github.com/shamsulA6/satuid-cas/blob/main/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/shamsulA6/satuid-cas)](https://github.com/shamsulA6/satuid-cas/issues)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.4-8892BF.svg)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/laravel-12.x-FF2D20.svg)](https://laravel.com/)

Pakej Laravel untuk integrasi **CAS Authentication (SATUID)** bagi sistem-sistem di bawah **Perbendaharaan Malaysia**.

Diedarkan melalui **OSDEC Package Registry** — tidak tersedia di Packagist awam.

---

## 📋 Keperluan Sistem

| Perkara | Versi Minimum | Disyorkan |
|---------|--------------|-----------|
| PHP | **8.4** | 8.4.x (production) |
| Laravel | **12.x** | 12.x terkini |
| Composer | 2.x | 2.x terkini |
| Akses SATUID | — | URL aplikasi mesti didaftarkan |

> ⚠️ **Nota Penting:** Pakej ini menggunakan `declare(strict_types=1)` dan ciri pengaturcaraan PHP 8.4. Versi PHP 8.2 atau 8.3 ke bawah **tidak disokong**.

---

## 🚀 Pemasangan

### Langkah 1 — Tambah OSDEC Registry dalam `composer.json` Projek

Buka fail `composer.json` aplikasi Laravel anda dan tambahkan konfigurasi repositori berikut:

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "[https://packages.osdec.gov.my](https://packages.osdec.gov.my)"
        }
    ]
}

Sekiranya organisasi anda menggunakan self-hosted GitLab/Gitea OSDEC, gunakan jenis "vcs":

{
    "repositories": [
        {
            "type": "vcs",
            "url": "[https://github.com/shamsulA6/satuid-cas.git](https://github.com/shamsulA6/satuid-cas.git)"
        }
    ]
}

Langkah 2 — Pemasangan Pakej
Jalankan perintah Composer berikut pada terminal anda:

composer require perbendaharaan/cas-auth:^1.0

Langkah 3 — Jalankan Perintah Pemasangan Artisan
Laksanakan perintah di bawah untuk menerbitkan fail-fail konfigurasi pakej:

php artisan cas:install

Perintah ini secara automatik akan menerbitkan:

Fail konfigurasi config/cas.php

Templat persekitaran .env.cas.example

Fail migrasi pangkalan data (migration)

Paparan halaman log masuk tempatan (local login views)
Langkah 4 — Konfigurasi Fail Persekitaran (.env)
Salin pemboleh ubah daripada fail .env.cas.example yang telah diterbitkan ke fail .env utama anda, kemudian kemas kini nilainya:

# Toggle: true = Menggunakan SATUID (Prod), false = Login Tempatan (Dev)
CAS_ENABLED=true

CAS_HOSTNAME=satuid.treasury.gov.my
CAS_PORT=443
CAS_URI=/gk

# URL Aplikasi anda — WAJIB didaftarkan dengan pihak pentadbir SATUID
CAS_CLIENT_SERVICE=[https://nama-sistem-anda.treasury.gov.my](https://nama-sistem-anda.treasury.gov.my)

CAS_REDIRECT_PATH=/dashboard
CAS_LOGOUT_URL=[https://satuid.treasury.gov.my/gk/logout](https://satuid.treasury.gov.my/gk/logout)
CAS_LOGOUT_REDIRECT=[https://nama-sistem-anda.treasury.gov.my](https://nama-sistem-anda.treasury.gov.my)
CAS_VALIDATE_PATH=/gk/serviceValidate

# Guna 'ca' untuk production, 'none' untuk development
CAS_VALIDATION=ca

Langkah 5 — Jalankan Migrasi Pangkalan Data
php artisan migrate

Proses ini akan menambah kolum id_pgn_ldap ke dalam jadual users sedia ada anda.

Langkah 6 — Konfigurasi Middleware dalam routes/web.php

<?php

use Illuminate\Support\Facades\Route;

// Auto-detect middleware berdasarkan tetapan CAS_ENABLED dalam fail .env
$mid = config('cas.cas_enabled') ? ['cas.auth'] : ['cas.local'];

Route::middleware($mid)->group(function (): void {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
    // ... route-route lain yang memerlukan authentication/pengesahan
});

Langkah 7 — Sediakan Butang Log Keluar dalam Blade View

<form method="POST" action="{{ route('cas.logout') }}">
    @csrf
    <button type="submit">Log Keluar</button>
</form>

🛡️ Pengurusan Middleware

Pakej ini menyediakan dua middleware utama yang didaftarkan secara automatik oleh sistem:
Alias,Kelas,Kegunaan
cas.auth,AuthCas,Mod Staging & Production — Pengesahan penuh melalui sistem SATUID
cas.local,AuthLocal,Mod Development Tempatan — Log masuk menggunakan id_pgn_ldap + kata laluan

💻 Pembangunan Tempatan (CAS_ENABLED=false)

Apabila nilai CAS_ENABLED ditetapkan kepada false, pakej akan mengaktifkan aliran pengesahan tempatan tanpa memerlukan capaian rangkaian ke pelayan SATUID.

Perkara,Mod Production,Mod Pembangunan (Local)
Middleware,cas.auth,cas.local
Aliran Login,Integrasi lencongan ke SATUID,Paparan borang /local-login
Kredensial,Tiket CAS (CAS ticket),id_pgn_ldap + password
Aliran Logout,Lencongan ke fungsi logout SATUID,Lencongan terus ke /local-login

Menambah Pengguna Ujian untuk Sesi Pembangunan
Anda boleh mencipta akaun ujian tempatan menggunakan php artisan tinker:

\App\Models\User::create([
    'name'        => 'Ahmad Ali',
    'email'       => 'ahmad.ali@treasury.gov.my',
    'password'    => bcrypt('password'),
    'id_pgn_ldap' => 'ahmad.ali',
]);

Gunakan ID LDAP: ahmad.ali dan Kata Laluan: password untuk log masuk.

Mengubah Suai Paparan Halaman Login Tempatan
Jika anda ingin mengubah suai reka bentuk halaman log masuk tempatan, terbitkan fungsi views:

php artisan vendor:publish --tag=cas-views

Fail reka bentuk akan disalin ke direktori resources/views/vendor/cas-auth/local/login.blade.php.

⚙️ Senarai Penuh Pemboleh Ubah .env

Pemboleh Ubah,Keterangan,Nilai Standard (Default)
CAS_ENABLED,"Tetapan mod: true = SATUID, false = Tempatan",false
CAS_HOSTNAME,Alamat hos pelayan SATUID,satuid.treasury.gov.my
CAS_PORT,Port sambungan pelayan SATUID,443
CAS_URI,Pangkalan URI bagi CAS,/gk
CAS_CLIENT_SERVICE,URL sistem anda (perlu didaftarkan di SATUID),Berdasarkan nilai APP_URL
CAS_REDIRECT_PATH,Halaman lencongan utama selepas berjaya log masuk,/dashboard
CAS_LOGOUT_URL,Pautan penuh proses log keluar pelayan SATUID,https://satuid.treasury.gov.my/gk/logout
CAS_LOGOUT_REDIRECT,Halaman lencongan utama selepas proses log keluar,Berdasarkan nilai APP_URL
CAS_VALIDATE_PATH,Titik akhir (endpoint) pengesahan tiket CAS,/gk/serviceValidate
CAS_VALIDATION,Pengesahan SSL: ca (Prod) / none (Dev),none
CAS_LDAP_COLUMN,Nama nama kolum LDAP pada jadual users,id_pgn_ldap
CAS_USER_MODEL,Kedudukan Model User aplikasi,App\Models\User

🛣️ Laluan Sistem (Package Routes)
Pakej ini mendaftarkan beberapa laluan khas secara dalaman:

Kaedah (Method),URL,Nama Laluan (Route Name),Keterangan
POST,/cas-logout,cas.logout,Menguruskan fungsi log keluar (Autodetect Prod/Local)
GET,/auth-status,cas.status,Memaparkan status pengesahan dalam format JSON (Bagi tujuan debugging)
GET,/local-login,cas.local.login,Memaparkan borang log masuk tempatan
POST,/local-login,cas.local.login.submit,Memproses data log masuk tempatan

🗄️ Model Pengguna (User Model)
Sila pastikan anda menambah atribut id_pgn_ldap ke dalam susunan mewakili $fillable pada model User anda:

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'id_pgn_ldap',  // ← Wajib ditambah
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

🔄 Aliran Kerja CAS (Production Flow)

Pengguna akses URL dilindungi
        │
        ▼
[Middleware: cas.auth]
        │
        ├── Auth::check() = true ─────────────────► Lulus ke controller
        │
        ├── ?ticket= ada dalam URL
        │        │
        │        ▼
        │    GET [https://satuid.treasury.gov.my/gk/serviceValidate](https://satuid.treasury.gov.my/gk/serviceValidate)
        │        ?service=<URL>&ticket=<TICKET>
        │        │
        │        ├── authenticationSuccess
        │        │        └── Cari user WHERE id_pgn_ldap = <ldap_id>
        │        │                ├── Dijumpai → Auth::login() → redirect
        │        │                └── Tidak dijumpai → abort(401)
        │        │
        │        └── authenticationFailure → abort(401)
        │
        └── Tiada ticket
                 └── redirect → https://satuid.../gk/login?service=<URL>
