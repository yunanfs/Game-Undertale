# ADMIN PANEL SETUP GUIDE

## Overview
Sistem admin untuk mengelola Stories dan Characters di UNDERTALE game.

## Setup Instructions

### 1. Initial Setup
Kunjungi URL berikut untuk melakukan setup admin:
```
http://localhost/gameundertale/php/admin_setup.php
```

Klik tombol "SETUP ADMIN" untuk:
- Membuat tabel admins di database
- Membuat tabel stories
- Membuat tabel characters
- Membuat admin user dengan kredensial default
- Menginsersi sample stories dan characters

### 2. Admin Login
Setelah setup, login ke admin panel:
```
URL: http://localhost/gameundertale/php/admin_login.php
Username: admin
Password: admin123
```

## Admin Features

### Dashboard
Dashboard menampilkan:
- Jumlah Stories yang ada
- Jumlah Characters yang ada
- Jumlah Users yang terdaftar
- Akses cepat ke halaman management

### Story Management
**Lokasi:** `http://localhost/gameundertale/admin/stories.php`

Fitur:
- ✓ VIEW - Melihat daftar semua stories
- ✓ ADD - Membuat story baru
- ✓ EDIT - Mengubah story yang ada
- ✓ DELETE - Menghapus story

Fields:
- Title: Judul story
- Description: Deskripsi singkat
- Order Number: Urutan tampilan
- Content: Konten lengkap story

### Character Management
**Lokasi:** `http://localhost/gameundertale/admin/characters.php`

Fitur:
- ✓ VIEW - Melihat daftar semua characters
- ✓ ADD - Membuat character baru
- ✓ EDIT - Mengubah character yang ada
- ✓ DELETE - Menghapus character

Fields:
- Name: Nama character (unique)
- Role: Peran/tipe character
- Description: Deskripsi singkat
- Bio: Biografi lengkap
- Image URL: URL untuk gambar character

### Users Management
**Lokasi:** `http://localhost/gameundertale/admin/users.php`

Fitur:
- ✓ VIEW - Melihat daftar semua users terdaftar
- Info ditampilkan: Username, Email, Joined Date, Last Login, Battles Won/Lost

## Database Tables

### admins table
```sql
id (INT PRIMARY KEY)
username (VARCHAR 50, UNIQUE)
password (VARCHAR 255 - hashed)
created_at (TIMESTAMP)
last_login (TIMESTAMP)
is_active (TINYINT)
```

### stories table
```sql
id (INT PRIMARY KEY)
title (VARCHAR 255)
content (LONGTEXT)
description (TEXT)
order_number (INT)
created_by (INT - FK to admins)
created_at (TIMESTAMP)
updated_at (TIMESTAMP)
```

### characters table
```sql
id (INT PRIMARY KEY)
name (VARCHAR 100, UNIQUE)
description (TEXT)
role (VARCHAR 50)
image_url (VARCHAR 255)
bio (LONGTEXT)
created_by (INT - FK to admins)
created_at (TIMESTAMP)
updated_at (TIMESTAMP)
```

## Security Features

✓ Password hashing menggunakan PHP password_hash()
✓ Session-based authentication
✓ SQL injection prevention dengan prepared statements
✓ Input validation dan sanitization
✓ Protected admin pages (redirect jika belum login)

## Default Credentials

**Username:** admin
**Password:** admin123

⚠️ PENTING: Ubah password admin setelah login pertama!
(Feature untuk change password akan ditambahkan di update berikutnya)

## File Structure

```
/admin/
├── dashboard.php          - Admin dashboard
├── stories.php            - Stories list
├── story_add.php          - Add new story
├── story_edit.php         - Edit story
├── story_delete.php       - Delete story
├── characters.php         - Characters list
├── character_add.php      - Add new character
├── character_edit.php     - Edit character
├── character_delete.php   - Delete character
└── users.php              - Users list

/php/
├── admin_login.php        - Admin login form
├── admin_logout.php       - Logout handler
└── admin_setup.php        - Initial setup
```

## Usage Example

### Add New Story
1. Login ke admin panel
2. Klik "STORIES" di dashboard
3. Klik "ADD NEW STORY"
4. Isi form:
   - Title: "Chapter 1: The Beginning"
   - Description: "First encounter with Flowey"
   - Order Number: 1
   - Content: [Story content here]
5. Klik "SAVE STORY"

### Edit Character
1. Login ke admin panel
2. Klik "CHARACTERS" di dashboard
3. Klik "EDIT" pada character yang ingin diubah
4. Update field yang diperlukan
5. Klik "UPDATE CHARACTER"

### Delete Content
1. Pada halaman Stories atau Characters list
2. Klik "DELETE" button
3. Konfirmasi penghapusan
4. Data akan dihapus dari database

## Notes

- Semua data sensitif di-hash dan divalidasi
- Session otomatis logout jika ditutup browser
- Semua aksi dicatat dengan timestamp
- Support untuk penambahan fitur di masa depan

## Troubleshooting

**Admin login tidak berfungsi:**
- Pastikan sudah menjalankan admin_setup.php
- Periksa username dan password
- Clear browser cache

**Tidak bisa menambah story/character:**
- Pastikan sudah login sebagai admin
- Periksa database connection di config.php
- Validasi input (jangan kosong)

**Database error:**
- Pastikan MySQL running
- Periksa credentials di config.php
- Periksa permission database user

