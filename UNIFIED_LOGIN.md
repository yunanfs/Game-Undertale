# 🔐 UNIFIED LOGIN SYSTEM

## Gambaran Umum

Sistem login telah dikonsolidasikan menjadi **satu halaman login terpadu** yang dapat menangani baik login admin maupun login pemain (player) biasa. Sistem secara otomatis mendeteksi jenis user dan mengarahkan ke dashboard yang sesuai.

---

## 📋 Fitur Utama

✅ **Single Login Page** - Satu halaman untuk semua tipe user  
✅ **Auto-Detection** - Otomatis deteksi tipe user (admin/player)  
✅ **Secure** - Menggunakan prepared statements & password hashing  
✅ **Intuitive** - UI yang user-friendly dengan info system  
✅ **Responsive** - Bekerja di semua ukuran device  

---

## 🔑 Credentials

### Admin Login
- **Username**: `admin`
- **Password**: `admin123`
- **Redirect**: `admin/dashboard.php`

### Player Login
- **Username**: Any registered player account (e.g., `sans`)
- **Password**: Player's registered password
- **Redirect**: `index.php` (back to homepage)

---

## 🏗️ Arsitektur Login Flow

### Step 1: User Submit Form
```
Input: username, password
      ↓
```

### Step 2: Check Admin Table
```
SELECT FROM admins WHERE username = ? AND is_active = 1
      ↓
   IF FOUND:
   - Verify password with password_verify()
   - Set admin session variables:
     - $_SESSION['admin_logged_in'] = true
     - $_SESSION['admin_id'] = <id>
     - $_SESSION['admin_username'] = <username>
   - Return: type = 'admin'
   - Redirect to: ../admin/dashboard.php
      
   IF NOT FOUND:
   - Continue to Step 3
```

### Step 3: Check Users Table
```
SELECT FROM users WHERE username = ?
      ↓
   IF FOUND:
   - Verify password with password_verify()
   - Set player session variables:
     - $_SESSION['logged_in'] = true
     - $_SESSION['user_id'] = <id>
     - $_SESSION['username'] = <username>
   - Return: type = 'user'
   - Redirect to: ../index.php
      
   IF NOT FOUND:
   - Return error: "Invalid username or password"
```

---

## 📂 File Structure

```
gameundertale/
├── php/
│   ├── login.php              ← UNIFIED LOGIN (NEW)
│   ├── admin_login.php        ← OLD (admin-only, deprecated)
│   ├── admin_logout.php       ← Logout script
│   ├── admin_setup.php        ← Admin setup wizard
│   └── config.php             ← Database config
│
├── admin/
│   └── dashboard.php          ← Admin dashboard (after login)
│
├── index.php                  ← Player homepage (after login)
└── login.html                 ← OLD (deprecated)
```

---

## 🔄 Session Management

### Admin Session
```php
$_SESSION['admin_logged_in'] = true;      // Authentication flag
$_SESSION['admin_id'] = <user_id>;        // Admin ID
$_SESSION['admin_username'] = '<name>';   // Admin username
```

### Player Session
```php
$_SESSION['logged_in'] = true;            // Authentication flag
$_SESSION['user_id'] = <user_id>;         // Player ID
$_SESSION['username'] = '<name>';         // Player username
```

### Check Session in Other Pages
```php
// Check if admin logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // User is admin
}

// Check if player logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // User is player
}
```

---

## 🔐 Security Features

### Password Verification
```php
// Hashing (on registration/setup)
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Verification (on login)
if (password_verify($inputPassword, $hashedPassword)) {
    // Password matches!
}
```

### Prepared Statements
```php
// Prevents SQL Injection
$stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND is_active = 1");
$stmt->bind_param("s", $username);
$stmt->execute();
```

### Logging
```php
// Debug logging in: logs/debug.log
logDebug("Login attempt for username: " . $username);
logDebug("Admin found: " . $admin['username']);
logDebug("Password verification: SUCCESS");
```

---

## 🎨 UI Components

### Login Header Info Box
```
ℹ UNIFIED LOGIN SYSTEM
• Admin: username admin, password admin123
• Player: use your registered account
→ System auto-detects your role
```

### Error Messages
- Dynamic error display based on server response
- Shows specific error messages (e.g., "Invalid username or password")

### Success Message
- "Login successful!"
- "Redirecting to game..."
- Auto-redirect based on user type

---

## 🚀 Usage

### 1. Access Login Page
```
URL: http://localhost/gameundertale/php/login.php
```

### 2. Login as Admin
```
Username: admin
Password: admin123
Click: LOGIN
Result: Redirected to admin/dashboard.php
```

### 3. Login as Player
```
Username: <registered_username>
Password: <player_password>
Click: LOGIN
Result: Redirected to index.php
```

### 4. Login from Home Page
```
Click: LOGIN button in navbar
→ Redirected to php/login.php
```

---

## 🔗 Integration Points

### Update navbar LOGIN button
Current href should point to: `php/login.php`
```html
<a href="php/login.php" class="login-btn">LOGIN</a>
```

### Check auth in admin pages
```php
// Add to top of admin pages
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../php/login.php');
    exit;
}
```

### Check auth in player pages
```php
// Add to top of player pages
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: php/login.php');
    exit;
}
```

---

## 📝 Database Tables

### admins table
```sql
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,          -- hashed password
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active TINYINT(1) DEFAULT 1
);
```

### users table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100),
    password VARCHAR(255) NOT NULL,          -- hashed password
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);
```

---

## 🔧 Troubleshooting

| Problem | Solution |
|---------|----------|
| Admin login not working | Run `admin_setup.php` to create admin user |
| Wrong redirect after login | Check `data.type` in JavaScript - should be 'admin' or 'user' |
| Password always fails | Ensure password is hashed with `password_hash()` |
| Session not persisting | Check if sessions enabled in PHP config |
| Login page blank | Check browser console (F12) for errors |

---

## 🎯 Default Credentials

| Role | Username | Password | Purpose |
|------|----------|----------|---------|
| Admin | `admin` | `admin123` | Manage stories, characters, users |
| Player | (any registered) | (their password) | Play the game, update profile |

---

## 📊 Flow Diagram

```
┌─────────────────────────────────────┐
│   User Accesses login.php           │
├─────────────────────────────────────┤
│   ↓                                  │
│   Submit username & password         │
│   ↓                                  │
│   AJAX POST to login.php             │
└─────────────────────────────────────┘
            ↓
┌─────────────────────────────────────┐
│   SERVER: Check Admin Table         │
├─────────────────────────────────────┤
│   Found? → Password OK?              │
│     YES → Set admin session          │
│     NO  → Continue to next step      │
└─────────────────────────────────────┘
            ↓
┌─────────────────────────────────────┐
│   SERVER: Check Users Table         │
├─────────────────────────────────────┤
│   Found? → Password OK?              │
│     YES → Set player session         │
│     NO  → Return error               │
└─────────────────────────────────────┘
            ↓
┌─────────────────────────────────────┐
│   CLIENT: Receive JSON response      │
├─────────────────────────────────────┤
│   Success?                           │
│     YES → Show success message       │
│     → Redirect based on type:        │
│        - admin → admin/dashboard.php │
│        - user  → index.php           │
│     NO  → Show error message         │
└─────────────────────────────────────┘
```

---

## 📝 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | Dec 11, 2025 | Initial unified login system |
| 0.1 | Dec 11, 2025 | Separate admin and player logins (deprecated) |

---

## 🎓 Best Practices

✅ **Always hash passwords** with `password_hash()`  
✅ **Always verify passwords** with `password_verify()`  
✅ **Use prepared statements** to prevent SQL injection  
✅ **Check session variables** before allowing access to protected pages  
✅ **Log login attempts** for security auditing  
✅ **Use HTTPS** in production environment  
✅ **Set secure session cookies** configuration  

---

**Last Updated**: December 11, 2025  
**System Version**: 1.0 - Unified Login
