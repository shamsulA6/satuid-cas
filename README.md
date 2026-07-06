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
            "url": "https://packages.osdec.gov.my"
        }
    ]
}
Sekiranya organisasi anda menggunakan self-hosted GitLab/Gitea OSDEC, gunakan jenis "vcs":

