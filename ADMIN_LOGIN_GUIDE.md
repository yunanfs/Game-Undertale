# ADMIN LOGIN - PANDUAN LENGKAP

## Status Setup ✓

Admin system sudah berhasil di-setup! Database sudah dibuat dengan:
- ✓ Tabel `admins` 
- ✓ Tabel `stories`
- ✓ Tabel `characters`
- ✓ Admin user sudah dibuat

## Cara Login Admin

### Step 1: Buka Admin Login Page
```
http://localhost/gameundertale/php/admin_login.php
```

### Step 2: Masukkan Credentials
- **Username:** `admin`
- **Password:** `admin123`

### Step 3: Klik LOGIN
Anda akan diredirect ke admin dashboard

## Admin Dashboard Features

Setelah login, Anda bisa mengakses:

### 1. **STORIES Management**
   - View semua stories
   - Add new story
   - Edit story
   - Delete story
   - URL: `http://localhost/gameundertale/admin/stories.php`

### 2. **CHARACTERS Management**
   - View semua characters
   - Add new character
   - Edit character
   - Delete character
   - URL: `http://localhost/gameundertale/admin/characters.php`

### 3. **USERS Management**
   - View semua users yang terdaftar
   - Lihat statistik battles
   - URL: `http://localhost/gameundertale/admin/users.php`

## Jika Login Gagal

Jika masih error, jalankan setup ulang:
```
http://localhost/gameundertale/php/admin_setup_direct.php
```

Setelah itu coba login lagi dengan credentials:
- Username: `admin`
- Password: `admin123`

## Debug Info

Untuk cek status database:
```
http://localhost/gameundertale/php/admin_debug.php
```

## Logout

Di dashboard, klik tombol "LOGOUT" untuk keluar dari admin panel.

---

**PENTING:** 
- Password admin saat ini: `admin123`
- Setelah production, ubah password ini!
- Setup file bisa dihapus setelah berhasil login

