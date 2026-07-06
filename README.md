Markdown# 🛠️ Panduan Integrasi Pakej `perbendaharaan/cas-auth`

### Langkah 1 — Konfigurasi Repositori (`composer.json`)
Sila tambahkan tetapan repositori berikut ke dalam fail `composer.json` anda mengikut kaedah hos organisasi anda:

**Pilihan A: Menggunakan OSDEC Package Registry**
```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "[https://packages.osdec.gov.my](https://packages.osdec.gov.my)"
        }
    ]
}
Pilihan B: Menggunakan self-hosted GitLab/Gitea OSDEC (VCS)JSON{
    "repositories": [
        {
            "type": "vcs",
            "url": "[https://github.com/shamsulA6/satuid-cas.git](https://github.com/shamsulA6/satuid-cas.git)"
        }
    ]
}
Langkah 2 — Pemasangan PakejJalankan perintah Composer berikut pada terminal anda:Bashcomposer require perbendaharaan/cas-auth:^1.0
Langkah 3 — Jalankan Perintah Pemasangan ArtisanLaksanakan perintah di bawah untuk menerbitkan fail-fail konfigurasi pakej:Bashphp artisan cas:install
Perintah ini secara automatik akan menerbitkan fail berikut:📄 Fail konfigurasi: config/cas.php📄 Templat persekitaran: .env.cas.example🗄️ Fail migrasi pangkalan data (migration)🎨 Paparan halaman log masuk tempatan (local login views)Langkah 4 — Konfigurasi Fail Persekitaran (.env)Salin pemboleh ubah daripada fail .env.cas.example yang telah diterbitkan ke fail .env utama anda, kemudian kemas kini nilainya:Ini, TOML# Toggle: true = Menggunakan SATUID (Prod), false = Login Tempatan (Dev)
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
Langkah 5 — Jalankan Migrasi Pangkalan DataJalankan arahan migrasi untuk mengemas kini struktur pangkalan data:Bashphp artisan migrate
Nota: Proses ini akan menambah kolum id_pgn_ldap ke dalam jadual users sedia ada anda.Langkah 6 — Konfigurasi Middleware dalam routes/web.phpGunakan tetapan dinamik untuk mengesan mod pengesahan secara automatik:PHP<?php

use Illuminate\Support\Facades\Route;

// Auto-detect middleware berdasarkan tetapan CAS_ENABLED dalam fail .env
$mid = config('cas.cas_enabled') ? ['cas.auth'] : ['cas.local'];

Route::middleware($mid)->group(function (): void {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
    // ... route-route lain yang memerlukan authentication/pengesahan
});
Langkah 7 — Sediakan Butang Log Keluar dalam Blade ViewTambahkan borang log keluar ini pada sistem menu/navigasi anda:HTML<form method="POST" action="{{ route('cas.logout') }}">
    @csrf
    <button type="submit">Log Keluar</button>
</form>
🛡️ Pengurusan MiddlewarePakej ini menyediakan dua middleware utama yang didaftarkan secara automatik oleh sistem:AliasKelasKegunaancas.authAuthCasMod Staging & Production — Pengesahan penuh melalui sistem SATUIDcas.localAuthLocalMod Development Tempatan — Log masuk menggunakan id_pgn_ldap + kata laluan💻 Pembangunan Tempatan (CAS_ENABLED=false)Apabila nilai CAS_ENABLED ditetapkan kepada false, pakej akan mengaktifkan aliran pengesahan tempatan tanpa memerlukan capaian rangkaian ke pelayan SATUID.PerkaraMod ProductionMod Pembangunan (Local)Middlewarecas.authcas.localAliran LoginIntegrasi lencongan ke SATUIDPaparan borang /local-loginKredensialTiket CAS (CAS ticket)id_pgn_ldap + passwordAliran LogoutLencongan ke fungsi logout SATUIDLencongan terus ke /local-loginMenambah Pengguna Ujian untuk Sesi PembangunanAnda boleh mencipta akaun ujian tempatan menggunakan php artisan tinker:PHP\App\Models\User::create([
    'name'        => 'Ahmad Ali',
    'email'       => 'ahmad.ali@treasury.gov.my',
    'password'    => bcrypt('password'),
    'id_pgn_ldap' => 'ahmad.ali',
]);
ID LDAP: ahmad.aliKata Laluan: passwordMengubah Suai Paparan Halaman Login TempatanJika anda ingin mengubah suai reka bentuk halaman log masuk tempatan, terbitkan fail paparan (views):Bashphp artisan vendor:publish --tag=cas-views
Fail reka bentuk akan disalin ke direktori resources/views/vendor/cas-auth/local/login.blade.php.⚙️ Senarai Penuh Pemboleh Ubah .envPemboleh UbahKeteranganNilai Standard (Default)CAS_ENABLEDTetapan mod: true = SATUID, false = TempatanfalseCAS_HOSTNAMEAlamat hos pelayan SATUIDsatuid.treasury.gov.myCAS_PORTPort sambungan pelayan SATUID443CAS_URIPangkalan URI bagi CAS/gkCAS_CLIENT_SERVICEURL sistem anda (perlu didaftarkan di SATUID)Berdasarkan nilai APP_URLCAS_REDIRECT_PATHHalaman lencongan utama selepas berjaya log masuk/dashboardCAS_LOGOUT_URLPautan penuh proses log keluar pelayan SATUIDhttps://satuid.treasury.gov.my/gk/logoutCAS_LOGOUT_REDIRECTHalaman lencongan utama selepas proses log keluarBerdasarkan nilai APP_URLCAS_VALIDATE_PATHTitik akhir (endpoint) pengesahan tiket CAS/gk/serviceValidateCAS_VALIDATIONPengesahan SSL: ca (Prod) / none (Dev)noneCAS_LDAP_COLUMNNama nama kolum LDAP pada jadual usersid_pgn_ldapCAS_USER_MODELKedudukan Model User aplikasiApp\Models\User🛣️ Laluan Sistem (Package Routes)Pakej ini mendaftarkan beberapa laluan khas secara dalaman:Kaedah (Method)URLNama Laluan (Route Name)KeteranganPOST/cas-logoutcas.logoutMenguruskan fungsi log keluar (Autodetect Prod/Local)GET/auth-statuscas.statusMemaparkan status pengesahan dalam format JSON (Bagi tujuan debugging)GET/local-logincas.local.loginMemaparkan borang log masuk tempatanPOST/local-logincas.local.login.submitMemproses data log masuk tempatan🗄️ Model Pengguna (User Model)Sila pastikan anda menambah atribut id_pgn_ldap ke dalam susunan mewakili $fillable pada model User anda (app/Models/User.php):PHP<?php

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
🔄 Aliran Kerja CAS (Production Flow)PlaintextPengguna akses URL dilindungi
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
        │        │                 ├── Dijumpai → Auth::login() → redirect
        │        │                 └── Tidak dijumpai → abort(401)
        │        │
        │        └── authenticationFailure → abort(401)
        │
        └── Tiada ticket
                 └── redirect → https://satuid.../gk/login?service=<URL>
