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

=================================================================================================================
# UNDERTALE Game - Dokumentasi Fungsi-Fungsi

## 📋 Ringkasan

Game UNDERTALE adalah RPG berbasis web dengan sistem pertempuran interaktif. Pemain menggunakan empat aksi (FIGHT, ACT, ITEM, MERCY) untuk mengalahkan musuh atau menyelesaikan pertempuran secara damai.

---

## 🎮 Fungsi-Fungsi Game Utama

### 1. **scrollToSection(id)**
- **Deskripsi**: Scroll halaman ke section tertentu dengan animasi smooth
- **Parameter**: `id` - ID elemen yang ingin dituju
- **Contoh**: `scrollToSection('battle')` - scroll ke section battle
- **Digunakan**: Tombol navigasi (START GAME, STORY, CHARACTERS, MUSIC)

### 2. **updateHP(newHp)**
- **Deskripsi**: Update HP pemain dan tampilkan progress bar
- **Parameter**: `newHp` - Nilai HP baru
- **Logika**:
  - Membatasi HP antara 0 hingga maxHp (20)
  - Update lebar progress bar
  - Update teks HP display
  - Trigger game over jika HP <= 0
- **Digunakan**: Saat pemain terkena damage atau heal

### 3. **handleFight()**
- **Deskripsi**: Aksi FIGHT - Serang musuh
- **Mekanisme**:
  - Damage: 5-12 (random)
  - Menambah turn count
  - Menambah score: damage * 10
  - Trigger dodge phase (musuh menyerang balik)
  - Jika musuh HP <= 0 → Victory
- **Battle Phase**: 'choose' → 'dodge'

### 4. **handleAct()**
- **Deskripsi**: Aksi ACT - Lakukan aksi spesial
- **Mekanisme**:
  - Menampilkan 4 random aksi berbeda:
    - Check (info musuh)
    - Compliment (pujian)
    - Threaten (ancaman)
    - Joke (bercanda)
  - Menambah score: +50
  - Tidak melanjutkan dodge phase
  - Cocok untuk menunda atau explore
- **Battle Phase**: 'choose' → 'choose'

### 5. **handleItem()**
- **Deskripsi**: Aksi ITEM - Konsumsi Monster Candy untuk heal
- **Mekanisme**:
  - Heal: +10 HP
  - Menambah turn count
  - Menambah score: +30
  - Tidak trigger dodge phase
- **Battle Phase**: 'choose' → 'choose'

### 6. **handleMercy()**
- **Deskripsi**: Aksi MERCY - Akhiri pertempuran secara damai
- **Mekanisme**:
  - Menambah turn count
  - Menambah score: +200
  - Langsung trigger victory (tanpa perlu mengalahkan musuh)
  - Ending paling damai
- **Battle Phase**: 'choose' → victory

### 7. **updateStats()**
- **Deskripsi**: Update tampilan statistik pertempuran
- **Update**:
  - `turnCount` - Jumlah turn yang telah dijalankan
  - `totalDamage` - Total damage yang diberikan
  - `score` - Total score yang diperoleh

### 8. **startDodgePhase()**
- **Deskripsi**: Mulai fase dodge - musuh menyerang dengan peluru
- **Mekanisme**:
  - Clear bullet sebelumnya
  - Generate pola peluru random (4 jenis)
  - Pemain harus menggerakkan soul untuk menghindari
  - Setelah 4 detik: return ke phase 'choose'
- **Battle Phase**: 'dodge' → 'choose'

### 9. **createBulletPattern()**
- **Deskripsi**: Pilih dan buat pola peluru random
- **Pola Tersedia**:
  1. **createHorizontalWave()** - Gelombang peluru dari kiri
  2. **createVerticalRain()** - Peluru jatuh dari atas
  3. **createCirclePattern()** - Peluru meledak melingkar
  4. **createRandomPattern()** - Peluru random di area

### 10. **animateBulletHorizontal(bullet)**
- **Deskripsi**: Animate peluru bergerak horizontal dari kiri ke kanan
- **Mekanisme**:
  - Update posisi setiap 20ms
  - Cek collision dengan soul
  - Hapus peluru saat keluar area

### 11. **animateBulletVertical(bullet)**
- **Deskripsi**: Animate peluru bergerak vertikal dari atas ke bawah
- **Mekanisme**: Similar dengan horizontal

### 12. **animateBulletRadial(bullet, angle)**
- **Deskripsi**: Animate peluru bergerak radial (dari tengah ke luar)
- **Parameter**: `angle` - Sudut gerakan peluru (dalam radian)

### 13. **checkCollision(bullet)**
- **Deskripsi**: Cek apakah peluru mengenai soul pemain
- **Mekanisme**:
  - Gunakan `getBoundingClientRect()` untuk koordinat
  - Jika collision: damage -3 HP, hapus peluru, flash screen
- **Digunakan**: Saat animate peluru

### 14. **flashScreen()**
- **Deskripsi**: Flash screen merah saat pemain terkena damage
- **Mekanisme**:
  - Battle arena background menjadi merah
  - Kembali ke hitam setelah 100ms

### 15. **clearBullets()**
- **Deskripsi**: Hapus semua peluru dari arena
- **Digunakan**: Setiap akhir dodge phase dan game over

### 16. **moveSoul()**
- **Deskripsi**: Handle gerakan soul pemain dengan keyboard
- **Kontrol**:
  - Arrow Keys: Directional movement
  - WASD: Directional movement (alternative)
- **Batasan**: Soul tidak bisa keluar dari arena

### 17. **gameOver()**
- **Deskripsi**: Trigger game over screen
- **Mekanisme**:
  - Clear semua bullet
  - Tampilkan game over modal dengan button CONTINUE
  - Reset battle dengan `resetBattle()`

### 18. **victory()**
- **Deskripsi**: Trigger victory screen dengan statistik
- **Tampilkan**:
  - Turn yang digunakan
  - Total damage yang diberikan
  - Final score
  - Button CONTINUE untuk bermain lagi

### 19. **resetBattle()**
- **Deskripsi**: Reset semua variabel game ke state awal
- **Reset**:
  - playerHp = 20
  - enemyHp = 50
  - turnCount = 0
  - totalDamage = 0
  - score = 0
  - battlePhase = 'choose'
  - Clear game over dan victory screens
  - Update UI (HP bar, stats, text)

### 20. **showCharacterModal(char)**
- **Deskripsi**: Tampilkan modal informasi karakter
- **Parameter**: `char` - nama karakter ('frisk', 'sans', 'papyrus', dll)
- **Konten**:
  - Icon karakter
  - Nama
  - Deskripsi
  - Abilities
  - Quote
- **Data**: Dari object `characterData`

### 21. **closeModal(modalId)**
- **Deskripsi**: Tutup modal dengan ID tertentu
- **Parameter**: `modalId` - ID modal atau kosong untuk character modal
- **Digunakan**: Saat klik tombol close/X

### 22. **togglePlay()** (Music Player)
- **Deskripsi**: Toggle play/pause musik
- **Update**: Button text antara '▶' (play) dan '❚❚' (pause)

### 23. **selectTrack(index)**
- **Deskripsi**: Pilih track musik tertentu
- **Parameter**: `index` - nomor track (0-4)
- **Update**: Highlight track aktif

### 24. **previousTrack()**
- **Deskripsi**: Putar track musik sebelumnya

### 25. **nextTrack()**
- **Deskripsi**: Putar track musik berikutnya

---

## 📊 Game Variables

```javascript
let playerHp = 20;              // HP pemain (current)
let maxHp = 20;                 // HP pemain (max)
let enemyHp = 50;               // HP musuh (current)
let maxEnemyHp = 50;            // HP musuh (max)
let turnCount = 0;              // Jumlah turn
let totalDamage = 0;            // Total damage yang diberikan
let score = 0;                  // Score pemain
let battlePhase = 'choose';     // Phase pertempuran: 'choose', 'dodge'
let gameActive = true;          // Status game
let bullets = [];               // Array peluru aktif
let keys = {};                  // Keyboard input tracking
```

---

## 🎯 Battle Flow

```
┌─────────────────────────────────────┐
│  Battle Starts (Choose Phase)       │
├─────────────────────────────────────┤
│  ┌─ FIGHT   → Random Damage → Dodge │
│  ├─ ACT     → Info/Reaction         │
│  ├─ ITEM    → Heal                  │
│  └─ MERCY   → Victory (Peacefully)  │
├─────────────────────────────────────┤
│  Dodge Phase (Musuh Serang)         │
│  ├─ Hindari Bullet Pattern 4 detik  │
│  └─ Kembali ke Choose Phase         │
├─────────────────────────────────────┤
│  Repeat hingga:                     │
│  ├─ Enemy HP = 0 → Victory          │
│  ├─ Player HP = 0 → Game Over       │
│  └─ Mercy dipilih → Victory         │
└─────────────────────────────────────┘
```

---

## 🔧 Perbaikan yang Sudah Dilakukan

1. ✅ **Path Script.js** - Diubah dari `../js/script.js` menjadi `js/script.js`
2. ✅ **Modal CSS** - Menambahkan `pointer-events: none` agar tidak menghalangi klik
3. ✅ **Character Modal** - Memperbaiki fungsi `closeModal()` agar mendukung parameter

---

## 🚀 Cara Bermain

1. **Klik tombol START GAME** untuk scroll ke battle arena
2. **Pilih aksi** setiap turn:
   - **FIGHT**: Serang musuh (damage 5-12)
   - **ACT**: Lakukan aksi spesial (info/reaksi)
   - **ITEM**: Heal dengan Monster Candy (+10 HP)
   - **MERCY**: Akhiri pertempuran secara damai
3. **Dodge Phase**: Gunakan Arrow Keys atau WASD untuk menghindari peluru
4. **Win Conditions**:
   - Musuh HP = 0 → Victory
   - Gunakan MERCY → Peaceful Victory
5. **Game Over**: Terjadi saat Player HP = 0

---

## 📝 Keyboard Controls

| Key | Fungsi |
|-----|--------|
| Arrow Up | Gerak soul ke atas |
| Arrow Down | Gerak soul ke bawah |
| Arrow Left | Gerak soul ke kiri |
| Arrow Right | Gerak soul ke kanan |
| W | Gerak soul ke atas |
| A | Gerak soul ke kiri |
| S | Gerak soul ke bawah |
| D | Gerak soul ke kanan |

---

## 🎨 Scoring System

| Aksi | Score |
|------|-------|
| FIGHT | Damage × 10 |
| ACT | +50 |
| ITEM | +30 |
| MERCY | +200 |

---

## ✨ Fitur Tambahan

- **Character Modal**: Klik karakter untuk melihat info detail
- **Music Player**: Putar/pause track musik game
- **Smooth Scroll**: Navigasi halaman yang smooth
- **Dynamic Bullet Patterns**: 4 pola peluru berbeda yang random
- **Collision Detection**: Real-time collision checking
- **Stats Tracking**: Track turn, damage, dan score

---

## 🐛 Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Game tidak berjalan | Pastikan script.js loaded (check console F12) |
| Tombol tidak diklik | Clear cache browser atau hard refresh (Ctrl+F5) |
| Peluru tidak muncul | Check CSS `.bullet` ada positioning dan styling |
| Soul tidak bergerak | Pastikan arena punya focus (click di arena) |
| Modal tidak tutup | Pastikan `closeModal()` mendapat ID yang benar |

---

**Last Updated**: December 11, 2025  
**Game Version**: 1.0
