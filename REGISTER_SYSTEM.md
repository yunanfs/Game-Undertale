# 📝 REGISTER SYSTEM

## Gambaran Umum

Halaman **Register** adalah tempat user baru dapat membuat akun untuk bermain game UNDERTALE. Sistem ini terintegrasi dengan sistem login terpadu, sehingga setelah registrasi berhasil, user akan langsung login otomatis.

---

## 🎯 Fitur Utama

✅ **User-Friendly Form** - Form registrasi yang intuitif dan mudah digunakan  
✅ **Real-time Validation** - Validasi client & server side  
✅ **Auto-Login** - Login otomatis setelah registrasi berhasil  
✅ **Secure** - Password hashing dengan `password_hash()`  
✅ **Error Handling** - Pesan error yang jelas dan informatif  

---

## 🔐 Validasi Form

### Username
- ✅ Minimal 3 karakter
- ✅ Hanya huruf, angka, dan underscore (_)
- ✅ Tidak boleh duplikat di database
- ❌ Contoh invalid: `ab`, `user@name`, `user name`

### Email
- ✅ Format email valid (xxx@xxx.xxx)
- ✅ Tidak boleh duplikat di database
- ❌ Contoh invalid: `notanemail`, `user@`, `@example.com`

### Password
- ✅ Minimal 6 karakter
- ✅ Harus sama dengan confirm password
- ✅ Di-hash dengan `PASSWORD_DEFAULT` algorithm
- ❌ Contoh invalid: `12345`, `pass123` (tidak sama confirm)

---

## 📂 File Structure

```
gameundertale/
├── php/
│   ├── login.php           ← Login page (dengan link register)
│   ├── register.php        ← NEW! Register page
│   ├── admin_logout.php
│   └── ... (other files)
│
└── index.php               ← Login button di navbar
```

---

## 🎨 UI/UX Design

### Register Page Layout
```
┌─────────────────────────────────────┐
│          ❤ REGISTER ❤              │
│   Create your account and begin     │
│      your journey                   │
├─────────────────────────────────────┤
│                                     │
│  [Error/Success Message Area]       │
│                                     │
│  ★ USERNAME                         │
│  [Input Field]                      │
│                                     │
│  ★ EMAIL                            │
│  [Input Field]                      │
│                                     │
│  ★ PASSWORD                         │
│  [Input Field]                      │
│                                     │
│  ★ CONFIRM PASSWORD                 │
│  [Input Field]                      │
│                                     │
│  [★ REGISTER ★ Button]              │
│                                     │
│  Already have account?              │
│  [Login here] link                  │
│                                     │
|     │                               |
└─────────────────────────────────────┘
```

### Color Scheme
- **Background**: Black (#000)
- **Border**: White (#fff), Dotted
- **Text**: White (#fff)
- **Error**: Red (#ff0000)
- **Success**: Green (#00ff00)
- **Heart Animation**: Red (#ff0000)

---

## 🔄 Registration Flow

### Step 1: User Submit Form
```
Input: username, email, password, confirm_password
        ↓
```

### Step 2: Validation
```
Client-side validation (basic)
        ↓
Server-side validation (strict):
- Check username length & format
- Check email format & validity
- Check password length
- Check password matching
- Check duplicate username
- Check duplicate email
        ↓
```

### Step 3: Register
```
IF validation passes:
- Hash password dengan password_hash()
- INSERT into users table
- Create session untuk auto-login
- Return success JSON
        ↓
ELSE:
- Return error message
```

### Step 4: Auto-Login & Redirect
```
IF success:
- Show success message: "Registration successful!"
- Set session variables:
  - $_SESSION['logged_in'] = true
  - $_SESSION['user_id'] = <id>
  - $_SESSION['username'] = <username>
- Auto redirect to ../index.php (game homepage)
        ↓
ELSE:
- Show error message
- User dapat memperbaiki dan retry
```

---

## 📊 Database Integration

### users table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);
```

### Data yang Disimpan
```php
// Saat registrasi:
username    → User-inputted value (trimmed, validated)
email       → User-inputted email (trimmed, validated)
password    → Hashed password (password_hash + PASSWORD_DEFAULT)
created_at  → Current timestamp (CURRENT_TIMESTAMP)
last_login  → NULL (set saat user login pertama kali)
```

---

## 🔒 Security Features

### Password Security
```php
// Hashing saat registrasi
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Verification saat login
if (password_verify($inputPassword, $hashedPassword)) {
    // Password matches!
}
```

### Input Validation
```php
// Server-side validation (strict)
- Trim all inputs
- Check format dengan regex
- Check email dengan filter_var()
- Check duplicate di database
- Check length requirements
```

### SQL Injection Prevention
```php
// Prepared statements dengan bind_param
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
```

### Session Security
```php
// Session variables set secara aman
$_SESSION['logged_in'] = true;      // Auth flag
$_SESSION['user_id'] = $userId;     // User ID
$_SESSION['username'] = $username;  // Username
```

---

## 📝 Error Messages

| Error | Penyebab | Solusi |
|-------|---------|--------|
| Username is required | Username kosong | Isi username |
| Username must be at least 3 characters | Username < 3 karakter | Minimal 3 karakter |
| Username can only contain letters, numbers, and underscores | Username ada karakter invalid | Hanya gunakan a-z, 0-9, _ |
| Username already exists | Username sudah terdaftar | Gunakan username lain |
| Email is required | Email kosong | Isi email |
| Invalid email format | Format email salah | Gunakan format xxx@xxx.xxx |
| Email already registered | Email sudah terdaftar | Gunakan email lain |
| Password is required | Password kosong | Isi password |
| Password must be at least 6 characters | Password < 6 karakter | Minimal 6 karakter |
| Passwords do not match | Password ≠ Confirm | Pastikan sama |

---

## 🔗 Navigation Links

### From Login Page
```
Don't have an account? → Register here
      ↓ (link ke register.php)
Register Page
```

### From Register Page
```
Already have account? → Login here
      ↓ (link ke login.php)
Login Page
```

### From Home Page
```
★ LOGIN ★ (navbar)
      ↓ (link ke php/login.php)
Login Page
      ↓
Don't have account? → Register here
      ↓ (link ke register.php)
Register Page
```

---

## 🎯 User Journey

### Scenario 1: New User (Happy Path)
```
1. Visit homepage (index.php)
2. Click "★ LOGIN ★" button
3. Click "Register here" link
4. Fill form:
   - Username: sans
   - Email: sans@example.com
   - Password: password123
   - Confirm: password123
5. Click "★ REGISTER ★"
6. Success! Auto-login & redirect to homepage
7. User dapat mulai bermain game
```

### Scenario 2: Validation Error
```
1. Fill form dengan data invalid
2. Click "★ REGISTER ★"
3. Error message muncul:
   - "Username must be at least 3 characters"
   - "Passwords do not match"
4. User perbaiki form
5. Retry register
6. Success!
```

### Scenario 3: Duplicate Account
```
1. User mencoba register dengan username "admin"
2. Error message: "Username already exists"
3. User gunakan username lain
4. Success!
```

---

## 🚀 Testing Checklist

- ✅ Form validation works (client + server)
- ✅ Error messages display correctly
- ✅ Success message shows on registration
- ✅ Auto-login after registration
- ✅ Redirect to index.php after success
- ✅ Password hashing works
- ✅ Duplicate checking works
- ✅ Email validation works
- ✅ Link to login page works
- ✅ Back to home link works
- ✅ AJAX submission works
- ✅ Session variables set correctly

---

## 📋 Default Test Data

### Valid Registration Example
```
Username: frisk
Email: frisk@undertale.com
Password: frisk123
Confirm: frisk123
Result: ✅ Success
```

### Invalid Registration Example
```
Username: fn                    ❌ (< 3 chars)
Email: frisk@undertale          ❌ (invalid format)
Password: 12345                 ❌ (< 6 chars)
Confirm: 12346                  ❌ (not matching)
Result: ✅ Shows errors
```

---

## 🔧 Integration Points

### From Login Page
```php
// login.php line ~436
<a href="register.php">Register here</a>
```

### From Register Page
```php
// register.php line ~279
<a href="login.php">Login here</a>
```

### Session Check (Other Pages)
```php
// Add to protected pages
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: php/register.php');
    exit;
}
```

---

## 📊 Database Query Log

### Check duplicate username
```sql
SELECT id FROM users WHERE username = ?
```

### Check duplicate email
```sql
SELECT id FROM users WHERE email = ?
```

### Insert new user
```sql
INSERT INTO users (username, email, password, created_at) 
VALUES (?, ?, ?, CURRENT_TIMESTAMP)
```

---

## 🎓 Best Practices

✅ **Always hash passwords** with `password_hash()`  
✅ **Always validate server-side** (client-side validation bisa dibypass)  
✅ **Use prepared statements** to prevent SQL injection  
✅ **Trim & sanitize inputs** before validation  
✅ **Check email format** dengan filter_var()  
✅ **Check uniqueness** sebelum insert  
✅ **Auto-login after register** untuk better UX  
✅ **Clear error messages** untuk user guidance  
✅ **Log errors** untuk debugging  

---

**Last Updated**: December 11, 2025  
**System Version**: 1.0 - Registration System
