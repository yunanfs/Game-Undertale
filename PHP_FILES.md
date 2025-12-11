# 📁 PHP Files Documentation

## Overview
Folder `/php` berisi script-script backend untuk sistem login, setup, dan user management.

---

## 📋 File List & Deskripsi

### 1. **login.php** ⭐ MAIN
- **Fungsi**: Unified login system untuk admin dan player
- **Fitur**:
  - Auto-detection tipe user (admin/player)
  - Check admin table terlebih dahulu
  - Check users table jika admin tidak ditemukan
  - Session management untuk kedua tipe user
  - AJAX login handling
- **Session Variables**:
  - Admin: `admin_logged_in`, `admin_id`, `admin_username`
  - Player: `logged_in`, `user_id`, `username`
- **Redirect**:
  - Admin → `../admin/dashboard.php`
  - Player → `../index.php`
- **Security**: Password hashing, prepared statements

### 2. **admin_logout.php**
- **Fungsi**: Logout untuk admin
- **Proses**:
  - Destroy session variables
  - Clear $_SESSION
  - Redirect ke `login.php` (unified login)
- **Used by**: Admin dashboard logout button

### 3. **admin_setup.php**
- **Fungsi**: Setup wizard untuk membuat admin dan database tables
- **Membuat**:
  - Table: `admins`
  - Table: `stories`
  - Table: `characters`
- **Default Admin**:
  - Username: `admin`
  - Password: `admin123`
- **Run**: `http://localhost/gameundertale/php/admin_setup.php`
- **Frequency**: Run sekali saat setup awal

### 4. **config.php**
- **Fungsi**: Database configuration (helper functions)
- **Contain**: Database connection setup
- **Used by**: Beberapa script yang mungkin butuh helper functions
- **Note**: Login.php menggunakan direct connection, tidak tergantung file ini

### 5. **setup.php**
- **Fungsi**: Setup script untuk membuat database dan tables default
- **Membuat**: Database `undertale_game` dan tables `users`
- **Run**: `http://localhost/gameundertale/php/setup.php`
- **Frequency**: Run sekali saat first-time setup

### 6. **create_user.php**
- **Fungsi**: API endpoint untuk membuat user baru (registrasi)
- **Method**: POST
- **Parameter**: 
  - `username` - Username baru
  - `email` - Email user
  - `password` - Password (akan di-hash)
- **Response**: JSON success/error
- **Security**: Password hashing dengan `password_hash()`

---

## 🚀 Setup Process

### Step 1: Create Database & Users Table
```bash
Visit: http://localhost/gameundertale/php/setup.php
```

### Step 2: Create Admin & Tables
```bash
Visit: http://localhost/gameundertale/php/admin_setup.php
```

### Step 3: Ready to Use
```bash
Login: http://localhost/gameundertale/php/login.php
Admin: admin / admin123
```

---

## 📊 Database Connection Flow

```
login.php (unified login)
├── Direct mysqli connection (no dependency)
├── Check admins table
├── Check users table
└── Set appropriate session

Other files:
├── setup.php (create database)
├── admin_setup.php (create admin + tables)
├── create_user.php (register new user)
└── config.php (helper - optional)
```

---

## 🔐 Security Features

✅ **Password Hashing**: `password_hash()` & `password_verify()`  
✅ **SQL Injection Prevention**: Prepared statements with bind_param  
✅ **Session Management**: Secure session handling with different variables  
✅ **Error Logging**: Debug logs in `/logs/` folder  
✅ **Input Validation**: Trim & validate all inputs  

---

## 🗑️ Deleted Files (Not Needed)

| File | Reason |
|------|--------|
| `admin_login.php` | Duplikat dengan login.php |
| `admin_login_test.php` | Test file, tidak perlu |
| `admin_login_v2.php` | Versi lama, tidak perlu |
| `admin_debug.php` | Debug file, tidak perlu |
| `admin_setup_direct.php` | Versi lama, sudah ada admin_setup.php |
| `login_api.php` | Test/duplikat, tidak perlu |
| `migrate_status.php` | Migration file, sudah selesai |

---

## 📝 File Sizes

| File | Size | Purpose |
|------|------|---------|
| login.php | ~16KB | Core login system ⭐ |
| admin_setup.php | ~8KB | Admin setup wizard |
| setup.php | ~10KB | Database setup |
| admin_logout.php | ~251B | Logout handler |
| create_user.php | ~5KB | User registration |
| config.php | ~1.5KB | Config helper |

**Total**: ~41KB (vs ~70KB sebelumnya) ✨

---

## ✅ Cleanup Results

- ✅ Deleted 7 unnecessary files
- ✅ Reduced folder size by ~30%
- ✅ Kept only essential files
- ✅ No functionality lost

---

**Last Updated**: December 11, 2025  
**Status**: Clean & Optimized ✨
