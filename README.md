# ADMIN LOGIN - PANDUAN LENGKAP

## Status Setup ✓

Admin system sudah berhasil di-setup! Database sudah dibuat dengan:
- ✓ Tabel `admins` 
- ✓ Tabel `stories`
- ✓ Tabel `characters`
- ✓ Tabel `Music`
- ✓ Tabel `gallery`
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

### 4. **Manage Gallery**
   - View semua gallery yang terdaftar
   - Add new Gallery
   - Edit Gallery
   - Delete Gallery
   - URL: `http://localhost/gameundertale/admin/gallery.php`

### 5. **Manage Music**
   - View semua Music yang terdaftar
   - Add new Music
   - Edit Music
   - Delete Music
   - URL: `http://localhost/gameundertale/admin/music.php`

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
- Jumlah Music yang dimasukkan
- Jumlah Gallery yang tersedia
- Akses cepat ke halaman management

**Database error:**
- Pastikan MySQL running
- Periksa credentials di config.php
- Periksa permission database user

==================================================================================
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
