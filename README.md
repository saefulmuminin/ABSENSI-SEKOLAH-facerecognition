# 📘 ABSENSI SEKOLAH QRCode - CodeIgniter 4

Sistem absensi sekolah berbasis QR Code menggunakan framework **CodeIgniter 4**.

---

## 🧱 Requirement

- PHP 8.2
- MySQL / MariaDB
- XAMPP / Laragon (disarankan)

---

## 📦 Langkah Instalasi

### 1. Clone atau Download Proyek

Ekstrak file ke dalam folder `htdocs` atau `www`:


---

### 2. Buat dan Import Database

- Buka **phpMyAdmin**: `http://localhost/phpmyadmin`
- Buat database absensi

- Import file `.sql` dari folder `_db` ke dalam database tersebut.

---

### 3. Konfigurasi File `.env`

Rename file `.env.example` menjadi `.env`, lalu edit isinya sesuai koneksi database lokalmu:

```env
database.default.hostname = localhost
database.default.database = absensi_sekolah
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```
### 3. jelankan projecr
```
composer install
npm install
php spark serve
```
### 3. login password
```
Role	Username	Password
Admin	admin	admin
```
