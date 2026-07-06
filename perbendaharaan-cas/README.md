# SatuID CAS (Central Authentication Service)

[![GitHub license](https://img.shields.io/github/license/shamsulA6/satuid-cas)](https://github.com/shamsulA6/satuid-cas/blob/main/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/shamsulA6/satuid-cas)](https://github.com/shamsulA6/satuid-cas/issues)

SatuID CAS ialah sistem Pengesahan Berpusat (*Central Authentication Service*) yang direka untuk menyediakan fungsi *Single Sign-On* (SSO) yang selamat, efisien, dan mudah disepadukan untuk pelbagai aplikasi di bawah ekosistem SatuID.

---

## 🚀 Ciri-Ciri Utama

* **Single Sign-On (SSO):** Log masuk sekali sahaja untuk mengakses pelbagai aplikasi yang berbeza.
* **Pengurusan Sesi Selamat:** Kawalan token dan sesi pengguna yang ketat bagi mengelakkan akses tanpa kebenaran.
* **Integrasi Mudah:** Menyokong protokol pengesahan standard (seperti OAuth2 / OIDC / SAML — *sesuaikan mengikut teknologi projek anda*).
* **Antaramuka Mesra Pengguna:** Halaman log masuk dan log keluar yang responsif dan bersih.

---

## 🛠️ Teknologi & Framework

Projek ini dibangunkan menggunakan kombinasi teknologi berikut:

* **Backend:** [Sila nyatakan: cth. Node.js / Laravel / Java Spring Boot]
* **Frontend:** [Sila nyatakan: cth. Blade / React / Vue.js / Tailwind CSS]
* **Pangkalan Data:** [Sila nyatakan: cth. MySQL / PostgreSQL / Redis]

---

## 💻 Cara Pemasangan & Setup Lokalan

Ikuti langkah-langkah ini untuk menjalankan projek di komputer peranti anda:

### 1. Klon Repositori
```bash
git clone [https://github.com/shamsulA6/satuid-cas.git](https://github.com/shamsulA6/satuid-cas.git)
cd satuid-cas

2. Konfigurasi Fail Persekitaran (.env)
Salin fail .env.example kepada .env dan kemas kini tetapan pangkalan data serta kunci aplikasi anda.

cp .env.example .env

3. Pasang Dependensi
Jika menggunakan Node.js:

npm install

Jika menggunakan PHP/Laravel:

composer install

4. Jalankan Migrasi Pangkalan Data

# Sesuaikan mengikut framework anda
php artisan migrate --seed
# atau
npm run migrate

5. Jalankan Aplikasi

npm run dev
# atau
php artisan serve

📂 Struktur Projek

satuid-cas/
├── app/               # Logik aplikasi utama (Backend)
├── config/            # Fail konfigurasi sistem
├── public/            # Fail aset statik (CSS, JS, Imej)
├── resources/         # Fail pandangan (Views/Templates)
├── routes/            # Pengurusan laluan URL (Routing)
└── README.md          # Dokumentasi utama

