

## {
## "repositories": [
## {
## "type": "composer",
Masalah ini berlaku disebabkan format teks atau markdown yang diekstrak daripada fail asal
(atau hasil salinan copy-paste) tidak dibersihkan dengan betul, menyebabkan sintaks kod
bercampur aduk dengan format pautan Markdown [Text](URL).
Selain itu, teks dokumentasi asal yang berbentuk senarai dan jadual telah bertukar menjadi
perenggan rapat yang sukar dibaca.
Mari kita kemaskan keseluruhan dokumentasi tersebut supaya tersusun, bersih, dan mudah
dirujuk semula.
 Panduan Integrasi Pakej perbendaharaan/cas-auth
## Langkah 1 — Konfigurasi Repositori (composer.json)
Sila tambahkan tetapan repositori berikut ke dalam fail composer.json anda mengikut
kaedah hos organisasi anda:
Pilihan A: Menggunakan OSDEC Package Registry
Pilihan B: Menggunakan self-hosted GitLab/Gitea OSDEC (VCS)
## JSON
## {
## "repositories": [
## {
## "type": "composer",
## "url": "https://packages.osdec.gov.my"
## }
## ]
## }
## 
## 

## Langkah 2 — Pemasangan Pakej
Jalankan perintah Composer berikut pada terminal anda:

## Langkah 3 — Jalankan Perintah Pemasangan Artisan
Laksanakan perintah di bawah untuk menerbitkan fail-fail konfigurasi pakej:
Perintah ini secara automatik akan menerbitkan fail berikut:
 Fail konfigurasi: config/cas.php
 Templat persekitaran: .env.cas.example
 Fail migrasi pangkalan data (migration)
 Paparan halaman log masuk tempatan (local login views)

## Langkah 4 — Konfigurasi Fail Persekitaran (.env)
Salin pemboleh ubah daripada fail .env.cas.example yang telah diterbitkan ke fail .env
utama anda, kemudian kemas kini nilainya:
## JSON
## {
## "repositories": [
## {
## "type": "vcs",
"url": "https://github.com/shamsulA6/satuid-cas.git"
## }
## ]
## }
## 
## Bash
composer require perbendaharaan/cas-auth:^1.0
## 
## Bash
php artisan cas:install
## 
Ini, TOML
## 

## Langkah 5 — Jalankan Migrasi Pangkalan Data
Jalankan arahan migrasi untuk mengemas kini struktur pangkalan data:
Nota: Proses ini akan menambah kolum id_pgn_ldap ke dalam jadual users
sedia ada anda.

Langkah 6 — Konfigurasi Middleware dalam routes/web.php
Gunakan tetapan dinamik untuk mengesan mod pengesahan secara automatik:
# Toggle: true = Menggunakan SATUID (Prod), false = Login Tempatan (Dev)
CAS_ENABLED=true
CAS_HOSTNAME=satuid.treasury.gov.my
## CAS_PORT=443
CAS_URI=/gk
# URL Aplikasi anda — WAJIB didaftarkan dengan pihak pentadbir SATUID
CAS_CLIENT_SERVICE=https://nama-sistem-anda.treasury.gov.my
CAS_REDIRECT_PATH=/dashboard
CAS_LOGOUT_URL=https://satuid.treasury.gov.my/gk/logout
CAS_LOGOUT_REDIRECT=https://nama-sistem-anda.treasury.gov.my
CAS_VALIDATE_PATH=/gk/serviceValidate
# Guna 'ca' untuk production, 'none' untuk development
CAS_VALIDATION=ca
## Bash
php artisan migrate
## 
## PHP
## <?php
useIlluminate\Support\Facades\Route;
// Auto-detect middleware berdasarkan tetapan CAS_ENABLED dalam fail .env
$mid = config('cas.cas_enabled') ? ['cas.auth'] : ['cas.local'];
Route::middleware($mid)->group(function (): void{
Route::get('/dashboard', fn () => view('dashboard'])->name('dashboard');
## 

Langkah 7 — Sediakan Butang Log Keluar dalam Blade View
Tambahkan borang log keluar ini pada sistem menu/navigasi anda:
##  Pengurusan Middleware
Pakej ini menyediakan dua middleware utama yang didaftarkan secara automatik oleh
sistem:
 Pembangunan Tempatan (CAS_ENABLED=false)
Apabila nilai CAS_ENABLED ditetapkan kepada false, pakej akan mengaktifkan aliran
pengesahan tempatan tanpa memerlukan capaian rangkaian ke pelayan SATUID.
// ... route-route lain yang memerlukan authentication/pengesahan
## });
## HTML
<formmethod="POST"action="{{ route('cas.logout') }}">
## @csrf
<buttontype="submit">Log Keluar</button>
## </form>
## 
AliasKelasKegunaan
cas.authAuthCas
## Mod Staging & Production —
Pengesahan penuh melalui sistem
## SATUID
cas.localAuthLocal
## Mod Development Tempatan — Log
masuk menggunakan id_pgn_ldap +
kata laluan
## 
PerkaraMod ProductionMod Pembangunan (Local)
## Middleware
cas.authcas.local

Menambah Pengguna Ujian untuk Sesi Pembangunan
Anda boleh mencipta akaun ujian tempatan menggunakan php artisan tinker:
ID LDAP:ahmad.ali
## Kata Laluan:password
## Mengubah Suai Paparan Halaman Login Tempatan
Jika anda ingin mengubah suai reka bentuk halaman log masuk tempatan, terbitkan fail
paparan (views):
Fail reka bentuk akan disalin ke direktori resources/views/vendor/cas-
auth/local/login.blade.php.
## ⚙ Senarai Penuh Pemboleh Ubah .env
PerkaraMod ProductionMod Pembangunan (Local)
Aliran LoginIntegrasi lencongan ke SATUIDPaparan borang /local-
login
KredensialTiket CAS (CAS ticket)id_pgn_ldap + password
Aliran LogoutLencongan ke fungsi logout
## SATUID
Lencongan terus ke /local-
login
## 
## PHP
\App\Models\User::create([
'name'        => 'Ahmad Ali',
## 'email'       => 'ahmad.ali@treasury.gov.my',
'password'    => bcrypt('password'),
## 'id_pgn_ldap' => 'ahmad.ali',
## ]);
## 
## Bash
php artisan vendor:publish --tag=cas-views
## 

 Laluan Sistem (Package Routes)
Pemboleh UbahKeteranganNilai Standard (Default)
## CAS_ENABLED
Tetapan mod: true = SATUID,
false = Tempatan
false
## CAS_HOSTNAME
Alamat hos pelayan SATUID
satuid.treasury.gov.my
## CAS_PORT
Port sambungan pelayan
## SATUID
## 443
## CAS_URI
Pangkalan URI bagi CAS
## /gk
## CAS_CLIENT_SERVICE
URL sistem anda (perlu
didaftarkan di SATUID)
Berdasarkan nilai APP_URL
## CAS_REDIRECT_PATH
Halaman lencongan utama
selepas berjaya log masuk
## /dashboard
## CAS_LOGOUT_URL
Pautan penuh proses log keluar
pelayan SATUID
https://satuid.treasury.g
ov.my/gk/logout
## CAS_LOGOUT_REDIREC
## T
Halaman lencongan utama
selepas proses log keluar
Berdasarkan nilai APP_URL
## CAS_VALIDATE_PATH
Titik akhir (endpoint)
pengesahan tiket CAS
/gk/serviceValidate
## CAS_VALIDATION
Pengesahan SSL: ca (Prod) /
none (Dev)
none
## CAS_LDAP_COLUMN
Nama nama kolum LDAP pada
jadual users
id_pgn_ldap
## CAS_USER_MODEL
Kedudukan Model User aplikasi
App\Models\User
## 

Pakej ini mendaftarkan beberapa laluan khas secara dalaman:
 Model Pengguna (User Model)
Sila pastikan anda menambah atribut id_pgn_ldap ke dalam susunan mewakili $fillable
pada model User anda (app/Models/User.php):
Kaedah (Method)URLNama Laluan (Route
## Name)
## Keterangan
## POST
## /cas-logoutcas.logout
## Menguruskan
log keluar (Au
Prod/Local)
## GET
## /auth-statuscas.status
## Memaparkan
pengesahan d
format JSON
tujuan debugg
## GET
## /local-logincas.local.login
## Memaparkan
log masuk tem
## POST
## /local-logincas.local.login.su
bmit
Memproses d
masuk tempat
Kaedah (Method)URLNama Laluan (Route
## Name)
## Keterangan
## POST
## /cas-logoutcas.logout
## Menguruskan
log keluar (Au
Prod/Local)
## GET
## /auth-statuscas.status
## Memaparkan
pengesahan d
format JSON
tujuan debugg
## GET
## /local-logincas.local.login
## Memaparkan
log masuk tem
## POST
## /local-logincas.local.login.su
bmit
Memproses d
masuk tempat
## 
## PHP
## <?php
namespaceApp\Models;
useIlluminate\Foundation\Auth\UserasAuthenticatable;
classUserextendsAuthenticatable
## {
protected$fillable = [
## 'name',
## 'email',
## 'password',
'id_pgn_ldap',  // ← Wajib ditambah
## ];
## 

 Aliran Kerja CAS (Production Flow)
protected$hidden = [
## 'password',
## 'remember_token',
## ];
protectedfunctioncasts(): array
## {
return [
## 'email_verified_at' => 'datetime',
## 'password'          => 'hashed',
## ];
## }
## }
## Plaintext
Pengguna akses URL dilindungi
## │
## ▼
[Middleware: cas.auth]
## │
├── Auth::check() = true ─────────────────► Lulus ke controller
## │
├── ?ticket= ada dalam URL
## │        │
## │        ▼
│    GET https://satuid.treasury.gov.my/gk/serviceValidate
│        ?service=<URL>&ticket=<TICKET>
## │        │
│        ├── authenticationSuccess
│        │        └── Cari user WHERE id_pgn_ldap = <ldap_id>
│        │                 ├── Dijumpai → Auth::login() → redirect
│        │                 └── Tidak dijumpai → abort(401)
## │        │
│        └── authenticationFailure → abort(401)
## │
└── Tiada ticket
└── redirect → https://satuid.../gk/login?service=<URL>
## 

aku kena copy satu-satu ke? takde cara mudah lain ke ? ni
readme.md
Tidak perlu salin satu-satu secara manual! Memandangkan ini adalah fail README.md
(Markdown), anda boleh salin keseluruhan kod mentah (source code) dokumentasi yang telah
saya kemaskan tadi dan terus tampal (paste) ke dalam projek anda.
Berikut adalah 2 cara paling mudah dan cepat untuk anda lakukan:
Cara 1: Salin Terus Kod Mentah Markdown (Paling Cepat)
Anda hanya perlu salin teks dalam kotak kod di bawah, buka fail README.md di dalam code
editor anda (seperti VS Code), padam semua kandungan lama, dan tampal (paste) kod ini:
Pilihan B: Menggunakan self-hosted GitLab/Gitea OSDEC (VCS)
## Markdown
## #  Panduan Integrasi Pakej `perbendaharaan/cas-auth`
## ### Langkah 1 — Konfigurasi Repositori (`composer.json`)
Sila tambahkan tetapan repositori berikut ke dalam fail `composer.json` anda mengikut k
**Pilihan A: Menggunakan OSDEC Package Registry**
## ```json
## {
## "repositories": [
## {
## "type": "composer",
## "url": "[https://packages.osdec.gov.my](https://packages.osdec.gov.my)"
## }
## ]
## }
## 
## JSON
## {
## "repositories": [
## {
## "type": "vcs",
"url": "[https://github.com/shamsulA6/satuid-cas.git](https://github.com/sh
## }
## 

## Langkah 2 — Pemasangan Pakej
Jalankan perintah Composer berikut pada terminal anda:
## Langkah 3 — Jalankan Perintah Pemasangan Artisan
Laksanakan perintah di bawah untuk menerbitkan fail-fail konfigurasi pakej:
Perintah ini secara automatik akan menerbitkan fail berikut:
 Fail konfigurasi: config/cas.php
 Templat persekitaran: .env.cas.example
 Fail migrasi pangkalan data (migration)
 Paparan halaman log masuk tempatan (local login views)
## Langkah 4 — Konfigurasi Fail Persekitaran (.env)
Salin pemboleh ubah daripada fail .env.cas.example yang telah diterbitkan ke fail .env
utama anda, kemudian kemas kini nilainya:
## ]
## }
## Bash
composer require perbendaharaan/cas-auth:^1.0
## 
## Bash
php artisan cas:install
## 
Ini, TOML
# Toggle: true = Menggunakan SATUID (Prod), false = Login Tempatan (Dev)
CAS_ENABLED=true
CAS_HOSTNAME=satuid.treasury.gov.my
## CAS_PORT=443
CAS_URI=/gk
# URL Aplikasi anda — WAJIB didaftarkan dengan pihak pentadbir SATUID
## 

## Langkah 5 — Jalankan Migrasi Pangkalan Data
Jalankan arahan migrasi untuk mengemas kini struktur pangkalan data:
Nota: Proses ini akan menambah kolum id_pgn_ldap ke dalam jadual users
sedia ada anda.
Langkah 6 — Konfigurasi Middleware dalam routes/web.php
Gunakan tetapan dinamik untuk mengesan mod pengesahan secara automatik:
Langkah 7 — Sediakan Butang Log Keluar dalam Blade View
Tambahkan borang log keluar ini pada sistem menu/navigasi anda:
CAS_CLIENT_SERVICE=[https://nama-sistem-anda.treasury.gov.my](https://nama-sistem-anda.
CAS_REDIRECT_PATH=/dashboard
CAS_LOGOUT_URL=[https://satuid.treasury.gov.my/gk/logout](https://satuid.treasury.gov.m
CAS_LOGOUT_REDIRECT=[https://nama-sistem-anda.treasury.gov.my](https://nama-sistem-anda
CAS_VALIDATE_PATH=/gk/serviceValidate
# Guna 'ca' untuk production, 'none' untuk development
CAS_VALIDATION=ca
## Bash
php artisan migrate
## 
## PHP
## <?php
useIlluminate\Support\Facades\Route;
// Auto-detect middleware berdasarkan tetapan CAS_ENABLED dalam fail .env
$mid = config('cas.cas_enabled') ? ['cas.auth'] : ['cas.local'];
Route::middleware($mid)->group(function (): void{
Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
// ... route-route lain yang memerlukan authentication/pengesahan
## });
## 

##  Pengurusan Middleware
Pakej ini menyediakan dua middleware utama yang didaftarkan secara automatik oleh
sistem:
 Pembangunan Tempatan (CAS_ENABLED=false)
Apabila nilai CAS_ENABLED ditetapkan kepada false, pakej akan mengaktifkan aliran
pengesahan tempatan tanpa memerlukan capaian rangkaian ke pelayan SATUID.
## HTML
<formmethod="POST"action="{{ route('cas.logout') }}">
## @csrf
<buttontype="submit">Log Keluar</button>
## </form>
## 
AliasKelasKegunaan
cas.authAuthCas
## Mod Staging & Production —
Pengesahan penuh melalui sistem
## SATUID
cas.localAuthLocal
## Mod Development Tempatan — Log
masuk menggunakan id_pgn_ldap +
kata laluan
## 
PerkaraMod ProductionMod Pembangunan (Local)
## Middleware
cas.authcas.local
Aliran LoginIntegrasi lencongan ke SATUIDPaparan borang /local-
login
KredensialTiket CAS (CAS ticket)id_pgn_ldap + password

Menambah Pengguna Ujian untuk Sesi Pembangunan
Anda boleh mencipta akaun ujian tempatan menggunakan php artisan tinker:
ID LDAP:ahmad.ali
## Kata Laluan:password
## Mengubah Suai Paparan Halaman Login Tempatan
Jika anda ingin mengubah suai reka bentuk halaman log masuk tempatan, terbitkan fail
paparan (views):
Fail reka bentuk akan disalin ke direktori resources/views/vendor/cas-
auth/local/login.blade.php.
## ⚙ Senarai Penuh Pemboleh Ubah .env
PerkaraMod ProductionMod Pembangunan (Local)
Aliran LogoutLencongan ke fungsi logout
## SATUID
Lencongan terus ke /local-
login
## 
## PHP
\App\Models\User::create([
'name'        => 'Ahmad Ali',
## 'email'       => 'ahmad.ali@treasury.gov.my',
'password'    => bcrypt('password'),
## 'id_pgn_ldap' => 'ahmad.ali',
## ]);
## 
## Bash
php artisan vendor:publish --tag=cas-views
## 

 Laluan Sistem (Package Routes)
Pemboleh UbahKeteranganNilai Standard (Default)
## CAS_ENABLED
Tetapan mod: true = SATUID,
false = Tempatan
false
## CAS_HOSTNAME
Alamat hos pelayan SATUID
satuid.treasury.gov.my
## CAS_PORT
Port sambungan pelayan
## SATUID
## 443
## CAS_URI
Pangkalan URI bagi CAS
## /gk
## CAS_CLIENT_SERVICE
URL sistem anda (perlu
didaftarkan di SATUID)
Berdasarkan nilai APP_URL
## CAS_REDIRECT_PATH
Halaman lencongan utama
selepas berjaya log masuk
## /dashboard
## CAS_LOGOUT_URL
Pautan penuh proses log keluar
pelayan SATUID
https://satuid.treasury.g
ov.my/gk/logout
## CAS_LOGOUT_REDIREC
## T
Halaman lencongan utama
selepas proses log keluar
Berdasarkan nilai APP_URL
## CAS_VALIDATE_PATH
Titik akhir (endpoint)
pengesahan tiket CAS
/gk/serviceValidate
## CAS_VALIDATION
Pengesahan SSL: ca (Prod) /
none (Dev)
none
## CAS_LDAP_COLUMN
Nama nama kolum LDAP pada
jadual users
id_pgn_ldap
## CAS_USER_MODEL
Kedudukan Model User aplikasi
App\Models\User
## 

Pakej ini mendaftarkan beberapa laluan khas secara dalaman:
 Model Pengguna (User Model)
Sila pastikan anda menambah atribut id_pgn_ldap ke dalam susunan mewakili $fillable
pada model User anda (app/Models/User.php):
Kaedah (Method)URLNama Laluan (Route
## Name)
## Keterangan
## POST
## /cas-logoutcas.logout
## Menguruskan
log keluar (Au
Prod/Local)
## GET
## /auth-statuscas.status
## Memaparkan
pengesahan d
format JSON
tujuan debugg
## GET
## /local-logincas.local.login
## Memaparkan
log masuk tem
## POST
## /local-logincas.local.login.su
bmit
Memproses d
masuk tempat
Kaedah (Method)URLNama Laluan (Route
## Name)
## Keterangan
## POST
## /cas-logoutcas.logout
## Menguruskan
log keluar (Au
Prod/Local)
## GET
## /auth-statuscas.status
## Memaparkan
pengesahan d
format JSON
tujuan debugg
## GET
## /local-logincas.local.login
## Memaparkan
log masuk tem
## POST
## /local-logincas.local.login.su
bmit
Memproses d
masuk tempat
## 
## PHP
## <?php
namespaceApp\Models;
useIlluminate\Foundation\Auth\UserasAuthenticatable;
classUserextendsAuthenticatable
## {
protected$fillable = [
## 'name',
## 'email',
## 'password',
'id_pgn_ldap',  // ← Wajib ditambah
## ];
## 

 Aliran Kerja CAS (Production Flow)
protected$hidden = [
## 'password',
## 'remember_token',
## ];
protectedfunctioncasts(): array
## {
return [
## 'email_verified_at' => 'datetime',
## 'password'          => 'hashed',
## ];
## }
## }
## Plaintext
Pengguna akses URL dilindungi
## │
## ▼
[Middleware: cas.auth]
## │
├── Auth::check() = true ─────────────────► Lulus ke controller
## │
├── ?ticket= ada dalam URL
## │        │
## │        ▼
│    GET [https://satuid.treasury.gov.my/gk/serviceValidate](https://satuid.tre
│        ?service=<URL>&ticket=<TICKET>
## │        │
│        ├── authenticationSuccess
│        │        └── Cari user WHERE id_pgn_ldap = <ldap_id>
│        │                 ├── Dijumpai → Auth::login() → redirect
│        │                 └── Tidak dijumpai → abort(401)
## │        │
│        └── authenticationFailure → abort(401)
## │
└── Tiada ticket
└── redirect → https://satuid.../gk/login?service=<URL>
## 
## ---

### Cara 2: Saya Tolong Buatkan Fail Markdown untuk Anda
Jika anda mahu, saya boleh terus **janakan fail `README.md`** yang lengkap dan cantik i
Selepas fail itu siap dimasukkan ke Drive, anda hanya perlu muat turun fail tersebut da
jana ters la .md
Pilihan B: Menggunakan self-hosted GitLab/Gitea OSDEC (VCS)
## Langkah 2 — Pemasangan Pakej
Jalankan perintah Composer berikut pada terminal anda:
## Langkah 3 — Jalankan Perintah Pemasangan Artisan
Laksanakan perintah di bawah untuk menerbitkan fail-fail konfigurasi pakej:
## JSON
## {
## "repositories": [
## {
## "type": "vcs",
"url": "[https://github.com/shamsulA6/satuid-cas.git](https://github.com/sh
## }
## ]
## }
## 
## Bash
composer require perbendaharaan/cas-auth:^1.0
## 
## Bash
## 
Show code
code

Perintah ini secara automatik akan menerbitkan fail berikut:
 Fail konfigurasi: config/cas.php
 Templat persekitaran: .env.cas.example
 Fail migrasi pangkalan data (migration)
 Paparan halaman log masuk tempatan (local login views)
## Langkah 4 — Konfigurasi Fail Persekitaran (.env)
Salin pemboleh ubah daripada fail .env.cas.example yang telah diterbitkan ke fail .env
utama anda, kemudian kemas kini nilainya:
## Langkah 5 — Jalankan Migrasi Pangkalan Data
Jalankan arahan migrasi untuk mengemas kini struktur pangkalan data:
php artisan cas:install
Ini, TOML
# Toggle: true = Menggunakan SATUID (Prod), false = Login Tempatan (Dev)
CAS_ENABLED=true
CAS_HOSTNAME=satuid.treasury.gov.my
## CAS_PORT=443
CAS_URI=/gk
# URL Aplikasi anda — WAJIB didaftarkan dengan pihak pentadbir SATUID
CAS_CLIENT_SERVICE=[https://nama-sistem-anda.treasury.gov.my](https://nama-sistem-anda.
CAS_REDIRECT_PATH=/dashboard
CAS_LOGOUT_URL=[https://satuid.treasury.gov.my/gk/logout](https://satuid.treasury.gov.m
CAS_LOGOUT_REDIRECT=[https://nama-sistem-anda.treasury.gov.my](https://nama-sistem-anda
CAS_VALIDATE_PATH=/gk/serviceValidate
# Guna 'ca' untuk production, 'none' untuk development
CAS_VALIDATION=ca
## 
## Bash
php artisan migrate
## 

Nota: Proses ini akan menambah kolum id_pgn_ldap ke dalam jadual users
sedia ada anda.
Langkah 6 — Konfigurasi Middleware dalam routes/web.php
Gunakan tetapan dinamik untuk mengesan mod pengesahan secara automatik:
Langkah 7 — Sediakan Butang Log Keluar dalam Blade View
Tambahkan borang log keluar ini pada sistem menu/navigasi anda:
##  Pengurusan Middleware
Pakej ini menyediakan dua middleware utama yang didaftarkan secara automatik oleh
sistem:
## PHP
## <?php
useIlluminate\Support\Facades\Route;
// Auto-detect middleware berdasarkan tetapan CAS_ENABLED dalam fail .env
$mid = config('cas.cas_enabled') ? ['cas.auth'] : ['cas.local'];
Route::middleware($mid)->group(function (): void{
Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
// ... route-route lain yang memerlukan authentication/pengesahan
## });
## 
## HTML
<formmethod="POST"action="{{ route('cas.logout') }}">
## @csrf
<buttontype="submit">Log Keluar</button>
## </form>
## 
AliasKelasKegunaan
cas.authAuthCas
## Mod Staging & Production —
Pengesahan penuh melalui sistem
## SATUID

 Pembangunan Tempatan (CAS_ENABLED=false)
Apabila nilai CAS_ENABLED ditetapkan kepada false, pakej akan mengaktifkan aliran
pengesahan tempatan tanpa memerlukan capaian rangkaian ke pelayan SATUID.
Menambah Pengguna Ujian untuk Sesi Pembangunan
Anda boleh mencipta akaun ujian tempatan menggunakan php artisan tinker:
ID LDAP:ahmad.ali
AliasKelasKegunaan
cas.localAuthLocal
## Mod Development Tempatan — Log
masuk menggunakan id_pgn_ldap +
kata laluan
## 
PerkaraMod ProductionMod Pembangunan (Local)
## Middleware
cas.authcas.local
Aliran LoginIntegrasi lencongan ke SATUIDPaparan borang /local-
login
KredensialTiket CAS (CAS ticket)id_pgn_ldap + password
Aliran LogoutLencongan ke fungsi logout
## SATUID
Lencongan terus ke /local-
login
## 
## PHP
\\App\\Models\\User::create([
'name'        => 'Ahmad Ali',
## 'email'       => 'ahmad.ali@treasury.gov.my',
'password'    => bcrypt('password'),
## 'id_pgn_ldap' => 'ahmad.ali',
## ]);
## 

## Kata Laluan:password
## Mengubah Suai Paparan Halaman Login Tempatan
Jika anda ingin mengubah suai reka bentuk halaman log masuk tempatan, terbitkan fail
paparan (views):
Fail reka bentuk akan disalin ke direktori resources/views/vendor/cas-
auth/local/login.blade.php.
## ⚙ Senarai Penuh Pemboleh Ubah .env
## Bash
php artisan vendor:publish --tag=cas-views
## 
Pemboleh UbahKeteranganNilai Standard (Default)
## CAS_ENABLED
Tetapan mod: true = SATUID,
false = Tempatan
false
## CAS_HOSTNAME
Alamat hos pelayan SATUID
satuid.treasury.gov.my
## CAS_PORT
Port sambungan pelayan
## SATUID
## 443
## CAS_URI
Pangkalan URI bagi CAS
## /gk
## CAS_CLIENT_SERVICE
URL sistem anda (perlu
didaftarkan di SATUID)
Berdasarkan nilai APP_URL
## CAS_REDIRECT_PATH
Halaman lencongan utama
selepas berjaya log masuk
## /dashboard
## CAS_LOGOUT_URL
Pautan penuh proses log keluar
pelayan SATUID
https://satuid.treasury.g
ov.my/gk/logout
## CAS_LOGOUT_REDIREC
## T
Halaman lencongan utama
selepas proses log keluar
Berdasarkan nilai APP_URL

 Laluan Sistem (Package Routes)
Pakej ini mendaftarkan beberapa laluan khas secara dalaman:
 Model Pengguna (User Model)
Pemboleh UbahKeteranganNilai Standard (Default)
## CAS_VALIDATE_PATH
Titik akhir (endpoint)
pengesahan tiket CAS
/gk/serviceValidate
## CAS_VALIDATION
Pengesahan SSL: ca (Prod) /
none (Dev)
none
## CAS_LDAP_COLUMN
Nama nama kolum LDAP pada
jadual users
id_pgn_ldap
## CAS_USER_MODEL
Kedudukan Model User aplikasi
App\\Models\\User
## 
Kaedah (Method)URLNama Laluan (Route
## Name)
## Keterangan
## POST
## /cas-logoutcas.logout
## Menguruskan
log keluar (Au
Prod/Local)
## GET
## /auth-statuscas.status
## Memaparkan
pengesahan d
format JSON
tujuan debugg
## GET
## /local-logincas.local.login
## Memaparkan
log masuk tem
## POST
## /local-logincas.local.login.su
bmit
Memproses d
masuk tempat
Kaedah (Method)URLNama Laluan (Route
## Name)
## Keterangan
## POST
## /cas-logoutcas.logout
## Menguruskan
log keluar (Au
Prod/Local)
## GET
## /auth-statuscas.status
## Memaparkan
pengesahan d
format JSON
tujuan debugg
## GET
## /local-logincas.local.login
## Memaparkan
log masuk tem
## POST
## /local-logincas.local.login.su
bmit
Memproses d
masuk tempat
## 

Sila pastikan anda menambah atribut id_pgn_ldap ke dalam susunan mewakili $fillable
pada model User anda (app/Models/User.php):
 Aliran Kerja CAS (Production Flow)
## PHP
## <?php
namespaceApp\\Models;
useIlluminate\\Foundation\\Auth\\UserasAuthenticatable;
classUserextendsAuthenticatable
## {
protected$fillable = [
## 'name',
## 'email',
## 'password',
'id_pgn_ldap',  // ← Wajib ditambah
## ];
protected$hidden = [
## 'password',
## 'remember_token',
## ];
protectedfunctioncasts(): array
## {
return [
## 'email_verified_at' => 'datetime',
## 'password'          => 'hashed',
## ];
## }
## }
## 
## Plaintext
Pengguna akses URL dilindungi
## │
## ▼
[Middleware: cas.auth]
## │
├── Auth::check() = true ─────────────────► Lulus ke controller
## │
## 

## """
with open("README.md", "w", encoding="utf-8") as f:
f.write(markdown_content)
print("File README.md successfully created.")
├── ?ticket= ada dalam URL
## │        │
## │        ▼
│    GET [https://satuid.treasury.gov.my/gk/serviceValidate](https://satuid.tre
│        ?service=<URL>&ticket=<TICKET>
## │        │
│        ├── authenticationSuccess
│        │        └── Cari user WHERE id_pgn_ldap = <ldap_id>
│        │                 ├── Dijumpai → Auth::login() → redirect
│        │                 └── Tidak dijumpai → abort(401)
## │        │
│        └── authenticationFailure → abort(401)
## │
└── Tiada ticket
└── redirect → https://satuid.../gk/login?service=<URL>
Your MD file is ready
[file-tag: code-generated-file-0-1783303367272911548]
Fail `README.md` yang lengkap, bersih, dan tersusun rapi telah berjaya dijana.
Anda kini boleh memuat turun fail tersebut menggunakan pautan di atas, kemudian masukka
kat mana aku download
Sila klik pada butang muat turun yang disediakan oleh sistem (seperti kotak atau ikon fail)
pada bahagian permulaan mesej saya sebelum ini yang bertulis:
[file-tag: code-generated-file-0-1783303367272911548]
Jika anda menggunakan antaramuka web atau aplikasi, biasanya akan ada ikon Download 
di sebelah teks tersebut. Selepas siap muat turun, anda cuma perlu masukkan fail
README.md itu terus ke dalam folder utama projek Laravel anda.

takde pun
Alamak, maaf! Kadang-kadang sistem antaramuka ini tak paparkan butang muat turun
dengan betul kalau fail dijana secara automatik.
Jangan risau, ini cara paling senang dan "komfirm" menjadi. Anda buat sendiri fail itu guna VS
Code (atau apa-apa editor yang anda guna) dalam masa 10 saat:
Berikut adalah teks penuh dokumentasi bersih yang anda perlukan:
Pilihan B: Menggunakan self-hosted GitLab/Gitea OSDEC (VCS)
Buka projek Laravel anda di VS Code.1.
Cipta satu fail baru dan namakan sebagai README.md.2.
Salin (copy) keseluruhan kod teks di bawah dan tampal (paste) ke dalam fail tersebut.3.
Tekan Ctrl + S (atau Cmd + S pada Mac) untuk simpan (save). Selesai!4.
## Markdown
## #  Panduan Integrasi Pakej `perbendaharaan/cas-auth`
## ### Langkah 1 — Konfigurasi Repositori (`composer.json`)
Sila tambahkan tetapan repositori berikut ke dalam fail `composer.json` anda mengikut k
**Pilihan A: Menggunakan OSDEC Package Registry**
## ```json
## {
## "repositories": [
## {
## "type": "composer",
## "url": "[https://packages.osdec.gov.my](https://packages.osdec.gov.my)"
## }
## ]
## }
## 
## JSON
## {
## "repositories": [
## 

## Langkah 2 — Pemasangan Pakej
Jalankan perintah Composer berikut pada terminal anda:
## Langkah 3 — Jalankan Perintah Pemasangan Artisan
Laksanakan perintah di bawah untuk menerbitkan fail-fail konfigurasi pakej:
Perintah ini secara automatik akan menerbitkan fail berikut:
 Fail konfigurasi: config/cas.php
 Templat persekitaran: .env.cas.example
 Fail migrasi pangkalan data (migration)
 Paparan halaman log masuk tempatan (local login views)
## Langkah 4 — Konfigurasi Fail Persekitaran (.env)
Salin pemboleh ubah daripada fail .env.cas.example yang telah diterbitkan ke fail .env
utama anda, kemudian kemas kini nilainya:
## {
## "type": "vcs",
"url": "[https://github.com/shamsulA6/satuid-cas.git](https://github.com/sh
## }
## ]
## }
## Bash
composer require perbendaharaan/cas-auth:^1.0
## 
## Bash
php artisan cas:install
## 
Ini, TOML
# Toggle: true = Menggunakan SATUID (Prod), false = Login Tempatan (Dev)
CAS_ENABLED=true
CAS_HOSTNAME=satuid.treasury.gov.my
## 

## Langkah 5 — Jalankan Migrasi Pangkalan Data
Jalankan arahan migrasi untuk mengemas kini struktur pangkalan data:
Nota: Proses ini akan menambah kolum id_pgn_ldap ke dalam jadual users
sedia ada anda.
Langkah 6 — Konfigurasi Middleware dalam routes/web.php
Gunakan tetapan dinamik untuk mengesan mod pengesahan secara automatik:
## CAS_PORT=443
CAS_URI=/gk
# URL Aplikasi anda — WAJIB didaftarkan dengan pihak pentadbir SATUID
CAS_CLIENT_SERVICE=[https://nama-sistem-anda.treasury.gov.my](https://nama-sistem-anda.
CAS_REDIRECT_PATH=/dashboard
CAS_LOGOUT_URL=[https://satuid.treasury.gov.my/gk/logout](https://satuid.treasury.gov.m
CAS_LOGOUT_REDIRECT=[https://nama-sistem-anda.treasury.gov.my](https://nama-sistem-anda
CAS_VALIDATE_PATH=/gk/serviceValidate
# Guna 'ca' untuk production, 'none' untuk development
CAS_VALIDATION=ca
## Bash
php artisan migrate
## 
## PHP
## <?php
useIlluminate\Support\Facades\Route;
// Auto-detect middleware berdasarkan tetapan CAS_ENABLED dalam fail .env
$mid = config('cas.cas_enabled') ? ['cas.auth'] : ['cas.local'];
Route::middleware($mid)->group(function (): void{
Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
// ... route-route lain yang memerlukan authentication/pengesahan
## });
## 

Langkah 7 — Sediakan Butang Log Keluar dalam Blade View
Tambahkan borang log keluar ini pada sistem menu/navigasi anda:
##  Pengurusan Middleware
Pakej ini menyediakan dua middleware utama yang didaftarkan secara automatik oleh
sistem:
 Pembangunan Tempatan (CAS_ENABLED=false)
Apabila nilai CAS_ENABLED ditetapkan kepada false, pakej akan mengaktifkan aliran
pengesahan tempatan tanpa memerlukan capaian rangkaian ke pelayan SATUID.
## HTML
<formmethod="POST"action="{{ route('cas.logout') }}">
## @csrf
<buttontype="submit">Log Keluar</button>
## </form>
## 
AliasKelasKegunaan
cas.authAuthCas
## Mod Staging & Production —
Pengesahan penuh melalui sistem
## SATUID
cas.localAuthLocal
## Mod Development Tempatan — Log
masuk menggunakan id_pgn_ldap +
kata laluan
## 
PerkaraMod ProductionMod Pembangunan (Local)
## Middleware
cas.authcas.local
Aliran LoginIntegrasi lencongan ke SATUIDPaparan borang /local-
login
KredensialTiket CAS (CAS ticket)id_pgn_ldap + password

Menambah Pengguna Ujian untuk Sesi Pembangunan
Anda boleh mencipta akaun ujian tempatan menggunakan php artisan tinker:
ID LDAP:ahmad.ali
## Kata Laluan:password
## Mengubah Suai Paparan Halaman Login Tempatan
Jika anda ingin mengubah suai reka bentuk halaman log masuk tempatan, terbitkan fail
paparan (views):
Fail reka bentuk akan disalin ke direktori resources/views/vendor/cas-
auth/local/login.blade.php.
## ⚙ Senarai Penuh Pemboleh Ubah .env
PerkaraMod ProductionMod Pembangunan (Local)
Aliran LogoutLencongan ke fungsi logout
## SATUID
Lencongan terus ke /local-
login
## 
## PHP
\App\Models\User::create([
'name'        => 'Ahmad Ali',
## 'email'       => 'ahmad.ali@treasury.gov.my',
'password'    => bcrypt('password'),
## 'id_pgn_ldap' => 'ahmad.ali',
## ]);
## 
## Bash
php artisan vendor:publish --tag=cas-views
## 

 Laluan Sistem (Package Routes)
Pemboleh UbahKeteranganNilai Standard (Default)
## CAS_ENABLED
Tetapan mod: true = SATUID,
false = Tempatan
false
## CAS_HOSTNAME
Alamat hos pelayan SATUID
satuid.treasury.gov.my
## CAS_PORT
Port sambungan pelayan
## SATUID
## 443
## CAS_URI
Pangkalan URI bagi CAS
## /gk
## CAS_CLIENT_SERVICE
URL sistem anda (perlu
didaftarkan di SATUID)
Berdasarkan nilai APP_URL
## CAS_REDIRECT_PATH
Halaman lencongan utama
selepas berjaya log masuk
## /dashboard
## CAS_LOGOUT_URL
Pautan penuh proses log keluar
pelayan SATUID
https://satuid.treasury.g
ov.my/gk/logout
## CAS_LOGOUT_REDIREC
## T
Halaman lencongan utama
selepas proses log keluar
Berdasarkan nilai APP_URL
## CAS_VALIDATE_PATH
Titik akhir (endpoint)
pengesahan tiket CAS
/gk/serviceValidate
## CAS_VALIDATION
Pengesahan SSL: ca (Prod) /
none (Dev)
none
## CAS_LDAP_COLUMN
Nama nama kolum LDAP pada
jadual users
id_pgn_ldap
## CAS_USER_MODEL
Kedudukan Model User aplikasi
App\Models\User
## 

Pakej ini mendaftarkan beberapa laluan khas secara dalaman:
 Model Pengguna (User Model)
Sila pastikan anda menambah atribut id_pgn_ldap ke dalam susunan mewakili $fillable
pada model User anda (app/Models/User.php):
Kaedah (Method)URLNama Laluan (Route
## Name)
## Keterangan
## POST
## /cas-logoutcas.logout
## Menguruskan
log keluar (Au
Prod/Local)
## GET
## /auth-statuscas.status
## Memaparkan
pengesahan d
format JSON
tujuan debugg
## GET
## /local-logincas.local.login
## Memaparkan
log masuk tem
## POST
## /local-logincas.local.login.su
bmit
Memproses d
masuk tempat
Kaedah (Method)URLNama Laluan (Route
## Name)
## Keterangan
## POST
## /cas-logoutcas.logout
## Menguruskan
log keluar (Au
Prod/Local)
## GET
## /auth-statuscas.status
## Memaparkan
pengesahan d
format JSON
tujuan debugg
## GET
## /local-logincas.local.login
## Memaparkan
log masuk tem
## POST
## /local-logincas.local.login.su
bmit
Memproses d
masuk tempat
## 
## PHP
## <?php
namespaceApp\Models;
useIlluminate\Foundation\Auth\UserasAuthenticatable;
classUserextendsAuthenticatable
## {
protected$fillable = [
## 'name',
## 'email',
## 'password',
'id_pgn_ldap',  // ← Wajib ditambah
## ];
## 

 Aliran Kerja CAS (Production Flow)
protected$hidden = [
## 'password',
## 'remember_token',
## ];
protectedfunctioncasts(): array
## {
return [
## 'email_verified_at' => 'datetime',
## 'password'          => 'hashed',
## ];
## }
## }
## Plaintext
Pengguna akses URL dilindungi
## │
## ▼
[Middleware: cas.auth]
## │
├── Auth::check() = true ─────────────────► Lulus ke controller
## │
├── ?ticket= ada dalam URL
## │        │
## │        ▼
│    GET [https://satuid.treasury.gov.my/gk/serviceValidate](https://satuid.tre
│        ?service=<URL>&ticket=<TICKET>
## │        │
│        ├── authenticationSuccess
│        │        └── Cari user WHERE id_pgn_ldap = <ldap_id>
│        │                 ├── Dijumpai → Auth::login() → redirect
│        │                 └── Tidak dijumpai → abort(401)
## │        │
│        └── authenticationFailure → abort(401)
## │
└── Tiada ticket
└── redirect → https://satuid.../gk/login?service=<URL>
## 
## 
