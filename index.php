<?php
session_start();

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'undertale_game';
$conn = new mysqli($host, $user, $pass, $db);

// Fetch Music
$music_tracks = [];
if (!$conn->connect_error) {
    $result = $conn->query("SELECT * FROM music ORDER BY order_number ASC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $music_tracks[] = $row;
        }
    }
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

if (!$isLoggedIn) {
    header("Location: php/login.php");
    exit();
}

$username = $isLoggedIn ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNDERTALE - The RPG Game</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Navbar Styles */
        .top-navbar {
            background: #000;
            border-bottom: 3px solid #fff;
            padding: 8px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'Press Start 2P', cursive, monospace;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .navbar-logo {
            color: #fff;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .navbar-center {
            display: flex;
            gap: 20px;
            flex: 1;
            justify-content: center;
            margin-left: -80px;
        }

        .navbar-center a {
            color: #fff;
            text-decoration: none;
            border: 2px solid #fff;
            padding: 10px 18px;
            font-family: 'Press Start 2P', cursive, monospace;
            font-size: 0.65rem;
            transition: all 0.3s;
        }

        .navbar-center a:hover {
            background: #fff;
            color: #000;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .profile-dropdown {
            position: relative;
        }

        .profile-btn {
            background: #fff;
            color: #000;
            border: 2px solid #fff;
            padding: 8px 20px;
            font-family: 'Press Start 2P', cursive, monospace;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .profile-btn:hover {
            background: #000;
            color: #fff;
            transform: scale(1.05);
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background: #000;
            border: 2px solid #fff;
            min-width: 180px;
            margin-top: 5px;
            z-index: 1001;
        }

        .dropdown-menu.active {
            display: block;
        }

        .dropdown-menu a,
        .dropdown-menu button {
            display: block;
            width: 100%;
            text-align: left;
            padding: 12px 15px;
            color: #fff;
            border: none;
            background: none;
            border-bottom: 1px solid #fff;
            font-family: 'Press Start 2P', cursive, monospace;
            font-size: 0.6rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .dropdown-menu a:hover,
        .dropdown-menu button:hover {
            background: #fff;
            color: #000;
        }

        .dropdown-menu a:last-child,
        .dropdown-menu button:last-child {
            border-bottom: none;
        }

        .login-btn {
            background: #ff0000;
            color: #fff;
            border: 2px solid #ff0000;
            padding: 8px 20px;
            font-family: 'Press Start 2P', cursive, monospace;
            font-size: 0.7rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .login-btn:hover {
            background: #fff;
            color: #ff0000;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            overflow: auto;
            pointer-events: none;
            visibility: hidden;
        }

        .modal.active {
            display: flex;
            justify-content: center;
            align-items: center;
            pointer-events: auto;
            visibility: visible;
        }

        .modal-content {
            background: #000;
            border: 5px solid #fff;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            font-family: 'Press Start 2P', cursive, monospace;
        }

        .modal-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #fff;
            padding-bottom: 20px;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.7rem;
            color: #fff;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            background: #000;
            border: 2px solid #fff;
            color: #fff;
            font-family: 'Press Start 2P', cursive, monospace;
            font-size: 0.7rem;
            box-sizing: border-box;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ff0000;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .modal-buttons button {
            flex: 1;
            padding: 10px;
            background: #fff;
            color: #000;
            border: 2px solid #fff;
            font-family: 'Press Start 2P', cursive, monospace;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .modal-buttons button:hover {
            background: #000;
            color: #fff;
        }

        .modal-buttons button.cancel {
            background: #f00;
            border-color: #f00;
            color: #fff;
        }

        .modal-buttons button.cancel:hover {
            background: #fff;
            color: #f00;
        }

        .profile-info {
            margin-bottom: 20px;
            padding: 20px;
            background: #111;
            border: 3px solid #fff;
        }

        .profile-info-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #fff;
        }

        .profile-info-header h3 {
            margin: 0;
            font-size: 1rem;
            color: #0f0;
        }

        .profile-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background: #000;
            border: 1px dotted #aaa;
            font-size: 0.7rem;
        }

        .profile-info-row:last-child {
            margin-bottom: 0;
        }

        .profile-info-label {
            color: #fff;
            font-weight: bold;
            min-width: 120px;
        }

        .profile-info-value {
            color: #0f0;
            font-weight: bold;
            text-align: right;
            flex: 1;
            margin-left: 10px;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border: 2px solid #fff;
            font-size: 0.7rem;
            text-align: center;
            border-radius: 3px;
        }

        .message.success {
            background: rgba(0, 255, 0, 0.2);
            border-color: #0f0;
            color: #0f0;
        }

        .message.error {
            background: rgba(255, 0, 0, 0.2);
            border-color: #f00;
            color: #f00;
        }

        body {
            margin: 0;
            padding: 0;
            margin-top: 55px;
        }

        /* Force menu button width */
        .menu-btn {
            width: 350px !important;
            padding: 15px 0 !important;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Top Navbar -->
    <div class="top-navbar">
        <div class="navbar-logo">★ UNDERTALE ★</div>
        <div class="navbar-center">
            <a href="#hero">HOME</a>
            <a href="#battle">BATTLE</a>
            <a href="#story">STORY</a>
            <a href="#characters">CHARACTERS</a>
            <a href="#tips">TIPS</a>
            <a href="#gallery">GALLERY</a>
            <a href="#music">MUSIC</a>
        </div>
        <div class="navbar-right">
            <?php if ($isLoggedIn): ?>
                <div class="profile-dropdown">
                    <button class="profile-btn" onclick="toggleDropdown()">★ <?php echo htmlspecialchars($username); ?> ★</button>
                    <div class="dropdown-menu" id="dropdownMenu">
                        <a href="#" onclick="openModal('profileModal'); loadProfileData(); return false;">VIEW PROFILE</a>
                        <a href="#" onclick="openModal('editModal'); loadEditData(); return false;">EDIT PROFILE</a>
                        <a href="#" onclick="openModal('passwordModal'); return false;">CHANGE PASSWORD</a>
                        <a href="#" onclick="openModal('statusModal'); return false;">ADD STATUS</a>
                        <a href="#" onclick="logout(); return false;" style="color: #ff0000;">LOGOUT</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="php/login.php" class="login-btn">★ LOGIN ★</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modals -->
    <!-- View Profile Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>★ MY PROFILE ★</h2>
            </div>
            <div id="profileContent"></div>
            <div class="modal-buttons">
                <button class="cancel" onclick="closeModal('profileModal')">CLOSE</button>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>★ EDIT PROFILE ★</h2>
            </div>
            <div id="editMessage"></div>
            <form id="editForm">
                <div class="form-group">
                    <label for="editUsername">USERNAME</label>
                    <input type="text" id="editUsername" disabled>
                </div>
                <div class="form-group">
                    <label for="editEmail">EMAIL</label>
                    <input type="email" id="editEmail" required>
                </div>
                <div class="modal-buttons">
                    <button type="submit" class="save-btn">SAVE</button>
                    <button type="button" class="cancel" onclick="closeModal('editModal')">CANCEL</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>★ CHANGE PASSWORD ★</h2>
            </div>
            <div id="passwordMessage"></div>
            <form id="passwordForm">
                <div class="form-group">
                    <label for="currentPassword">CURRENT PASSWORD</label>
                    <input type="password" id="currentPassword" required>
                </div>
                <div class="form-group">
                    <label for="newPassword">NEW PASSWORD</label>
                    <input type="password" id="newPassword" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="confirmPassword">CONFIRM PASSWORD</label>
                    <input type="password" id="confirmPassword" required minlength="6">
                </div>
                <div class="modal-buttons">
                    <button type="submit" class="save-btn">UPDATE</button>
                    <button type="button" class="cancel" onclick="closeModal('passwordModal')">CANCEL</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Status Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>★ ADD STATUS ★</h2>
            </div>
            <div id="statusMessage"></div>
            <form id="statusForm">
                <div class="form-group">
                    <label for="statusText">STATUS</label>
                    <textarea id="statusText" required maxlength="200" placeholder="What's on your mind?"></textarea>
                </div>
                <p style="font-size: 0.6rem; color: #aaa; text-align: right;">
                    <span id="charCount">0</span>/200
                </p>
                <div class="modal-buttons">
                    <button type="submit" class="save-btn">POST</button>
                    <button type="button" class="cancel" onclick="closeModal('statusModal')">CANCEL</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="hero" id="hero">
        <div class="dotted-border">
            <div class="heart"></div>
            <h1 class="title">UNDERTALE</h1>
            <p class="subtitle">
                The RPG game where you<br>
                don't have to destroy anyone.
            </p>
            <div class="menu-buttons">
                <button class="menu-btn" onclick="scrollToSection('battle')">★ START GAME</button>
                <button class="menu-btn" onclick="scrollToSection('story')">★ STORY</button>
                <button class="menu-btn" onclick="scrollToSection('characters')">★ CHARACTERS</button>
                <button class="menu-btn" onclick="scrollToSection('music')">★ MUSIC</button>
            </div>
        </div>
    </div>

    <!-- Battle Game Section -->
    <section id="battle">
        <div class="container">
            <h2>★ BATTLE SYSTEM ★</h2>
            <div class="battle-container">
                <div class="battle-header">
                    <div class="player-info">
                        <div class="player-name" id="playerName"><?php echo strtoupper($username ?: 'FRISK'); ?></div>
                        <div class="player-lv">LV 1</div>
                    </div>
                    <div class="hp-section">
                        <div class="hp-label">HP</div>
                        <div class="hp-text" id="hpText">20 / 20</div>
                        <div class="hp-bar-container">
                            <div class="hp-bar" id="playerHp"></div>
                        </div>
                    </div>
                </div>

                <div class="battle-arena" id="battleArena">
                    <div class="enemy-sprite" id="enemySprite">
                        <div class="enemy-name">FROGGIT</div>
                        <div class="enemy-icon">🐸</div>
                    </div>
                    <div class="player-soul" id="playerSoul"></div>
                </div>

                <div class="battle-text-box" id="battleTextBox">
                    <div class="battle-text" id="battleText">
                        * A wild FROGGIT appeared!<br>
                        * It doesn't seem to know why it's here.
                    </div>
                </div>

                <div class="battle-buttons" id="battleButtons">
                    <button class="battle-btn fight-btn" onclick="handleFight()">
                        <span class="btn-icon">★</span> FIGHT
                    </button>
                    <button class="battle-btn act-btn" onclick="handleAct()">
                        <span class="btn-icon">★</span> ACT
                    </button>
                    <button class="battle-btn item-btn" onclick="handleItem()">
                        <span class="btn-icon">★</span> ITEM
                    </button>
                    <button class="battle-btn mercy-btn" onclick="handleMercy()">
                        <span class="btn-icon">★</span> MERCY
                    </button>
                </div>

                <div class="battle-stats">
                    <div class="stat-item">
                        <span>TURNS:</span>
                        <span id="turnCount">0</span>
                    </div>
                    <div class="stat-item">
                        <span>DAMAGE:</span>
                        <span id="totalDamage">0</span>
                    </div>
                    <div class="stat-item">
                        <span>SCORE:</span>
                        <span id="score">0</span>
                    </div>
                </div>
            </div>

            <div class="victory-screen" id="victory">
                <h2>★ VICTORY! ★</h2>
                <p class="victory-text">
                    * You won the battle!<br>
                    * You earned 0 EXP and 0 gold.<br>
                    * Your LOVE increased!
                </p>
                <div class="victory-stats">
                    <div>Turns Used: <span id="finalTurns">0</span></div>
                    <div>Total Damage: <span id="finalDamage">0</span></div>
                    <div>Final Score: <span id="finalScore">0</span></div>
                </div>
                <button class="menu-btn" onclick="resetBattle()">CONTINUE</button>
            </div>
        </div>
    </section>

    <!-- Story Section -->
    <section id="story">
        <div class="container">
            <h2>★ THE STORY ★</h2>
            <div class="story-timeline">
                <div class="story-item">
                    <h3 class="story-title">THE LEGEND</h3>
                    <p class="story-text">
                        Long ago, two races ruled over Earth: HUMANS and MONSTERS.
                        One day, war broke out between the two races.
                        After a long battle, the humans were victorious.
                        They sealed the monsters underground with a magic spell.
                    </p>
                </div>

                <div class="story-item">
                    <h3 class="story-title">MT. EBOTT - 201X</h3>
                    <p class="story-text">
                        Legends say that those who climb the mountain never return.
                        A child falls into the Underground, the land where monsters have been banished.
                        This is where your journey begins.
                    </p>
                </div>

                <div class="story-item">
                    <h3 class="story-title">THE BARRIER</h3>
                    <p class="story-text">
                        The monsters are trapped behind a magical barrier.
                        It takes the SOUL of a human and the SOUL of a monster to cross it.
                        But there is no way for a monster to leave without taking a human SOUL.
                    </p>
                </div>

                <div class="story-item">
                    <h3 class="story-title">YOUR CHOICES MATTER</h3>
                    <p class="story-text">
                        In this world, you can choose to FIGHT or show MERCY.
                        Every monster has their own personality and story.
                        Your actions will determine not just your fate, but theirs too.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Characters Section -->
    <section id="characters">
        <div class="container">
            <h2>★ CHARACTERS ★</h2>
            <div class="characters-grid">
                <?php
                // Fetch characters from database
                try {
                    $char_conn = new mysqli('localhost', 'root', '', 'undertale_game');
                    if ($char_conn->connect_error) {
                        throw new Exception("Connection failed");
                    }
                    
                    $char_conn->set_charset("utf8mb4");
                    $result = $char_conn->query("SELECT id, name, description, role, image_url FROM characters ORDER BY id ASC");
                    
                    if ($result && $result->num_rows > 0) {
                        while ($char = $result->fetch_assoc()) {
                            $char_id = htmlspecialchars($char['id']);
                            $char_name = htmlspecialchars($char['name']);
                            $char_desc = htmlspecialchars($char['description']);
                            $char_img = htmlspecialchars($char['image_url'] ?? '');
                            
                            // Determine if image is a URL or file path
                            $is_url = !empty($char_img) && (strpos($char_img, 'http') === 0);
                            $is_file = !empty($char_img) && file_exists(__DIR__ . '/' . $char_img);
                            
                            echo '<div class="character-card" onclick="showCharacterModal(\'' . addslashes(strtolower($char_name)) . '\')">';
                            echo '    <div class="character-img">';
                            
                            if ($is_url || $is_file) {
                                echo '<img src="' . $char_img . '" alt="' . $char_name . '" style="width: 100%; height: 100%; object-fit: contain;">';
                            } else {
                                $char_icons = [
                                    'frisk' => '👤',
                                    'sans' => '💀',
                                    'papyrus' => '🦴',
                                    'toriel' => '👑',
                                    'undyne' => '🐟',
                                    'flowey' => '🌻',
                                    'flower pot' => '🪴',
                                    'fahri' => '🧑',
                                    'agoyy' => '💀'
                                ];
                                $lower_name = strtolower($char_name);
                                $icon = isset($char_icons[$lower_name]) ? $char_icons[$lower_name] : '👤';
                                echo '<div style="font-size: 4rem;">' . $icon . '</div>';
                            }
                            
                            echo '    </div>';
                            echo '    <h3 class="character-name">' . strtoupper($char_name) . '</h3>';
                            echo '    <p class="character-desc">' . $char_desc . '</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p style="grid-column: 1/-1; text-align: center; color: #aaa;">No characters found.</p>';
                    }
                    $char_conn->close();
                } catch (Exception $e) {
                    echo '<p style="grid-column: 1/-1; text-align: center; color: #ff0000;">Error loading characters</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Tips Section -->
    <section id="tips">
        <div class="container">
            <h2>★ TIPS & TRICKS ★</h2>
            <div class="tips-container">
                <div class="tip-box">
                    <h3 class="tip-title">BATTLE MECHANICS</h3>
                    <div class="tip-content">
                        <ul>
                            <li>Use WASD or Arrow Keys to move your SOUL</li>
                            <li>Avoid white bullets during enemy attacks</li>
                            <li>Blue attacks: Stand still to avoid damage</li>
                            <li>Orange attacks: Keep moving to avoid damage</li>
                            <li>Perfect timing on FIGHT increases damage</li>
                        </ul>
                    </div>
                </div>

                <div class="tip-box">
                    <h3 class="tip-title">PACIFIST ROUTE</h3>
                    <div class="tip-content">
                        <ul>
                            <li>Never kill any monsters (0 EXP)</li>
                            <li>Spare every enemy you encounter</li>
                            <li>Complete all friendship quests</li>
                            <li>Get the best ending!</li>
                        </ul>
                    </div>
                </div>

                <div class="tip-box">
                    <h3 class="tip-title">SECRETS</h3>
                    <div class="tip-content">
                        <ul>
                            <li>Call Toriel from different rooms</li>
                            <li>Play piano in Waterfall correctly</li>
                            <li>Find the Mystery Man (Gaster)</li>
                            <li>Pet the Annoying Dog</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery">
        <div class="container">
            <h2>★ GALLERY ★</h2>
            <div class="gallery-grid">
                <div class="gallery-item"><div class="gallery-img">🏛️</div><h3>THE RUINS</h3></div>
                <div class="gallery-item"><div class="gallery-img">❄️</div><h3>SNOWDIN</h3></div>
                <div class="gallery-item"><div class="gallery-img">💧</div><h3>WATERFALL</h3></div>
                <div class="gallery-item"><div class="gallery-img">🔥</div><h3>HOTLAND</h3></div>
                <div class="gallery-item"><div class="gallery-img">🏰</div><h3>NEW HOME</h3></div>
                <div class="gallery-item"><div class="gallery-img">⚔️</div><h3>BATTLE</h3></div>
            </div>
        </div>
    </section>

    <!-- Music Section -->
    <section id="music">
        <div class="container">
            <h2>★ SOUNDTRACK ★</h2>
            <div class="music-player">
                <div class="now-playing">
                    <div>NOW PLAYING:</div>
                    <div class="track-name" id="currentTrack"><?php echo !empty($music_tracks) ? htmlspecialchars($music_tracks[0]['title']) : 'No Music'; ?></div>
                </div>
                <div class="player-controls">
                    <button class="control-btn" onclick="previousTrack()">◀</button>
                    <button class="control-btn" id="playBtn" onclick="togglePlay()">▶</button>
                    <button class="control-btn" onclick="nextTrack()">▶▶</button>
                </div>
                <div class="playlist">
                    <?php if (!empty($music_tracks)): ?>
                        <?php foreach ($music_tracks as $index => $track): ?>
                            <div class="track <?php echo $index === 0 ? 'active' : ''; ?>" onclick="selectTrack(<?php echo $index; ?>)">
                                <?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT) . '. ' . htmlspecialchars($track['title']); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="track">No music available</div>
                    <?php endif; ?>




                </div>
            </div>
        </div>
    </section>

    <!-- Character Modal -->
    <div class="modal" id="characterModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal('characterModal')">X</button>
            <div id="modalBody"></div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div class="modal" id="logoutModal">
        <div class="modal-content logout-modal">
            <div class="logout-icon">❤</div>
            <h2>Are you sure?</h2>
            <p>Do you really want to<br>leave?</p>
            <div class="logout-buttons">
                <button class="logout-btn confirm" onclick="confirmLogout()">★ YES ★</button>
                <button class="logout-btn cancel" onclick="closeModal('logoutModal')">★ NO ★</button>
            </div>
        </div>
    </div>

    <!-- Game Over Screen -->
    <div class="game-over" id="gameOver">
        <h1>YOU DIED</h1>
        <button class="menu-btn" onclick="resetBattle()">CONTINUE</button>
    </div>

    <script>
        const dbTracks = <?php echo json_encode(array_map(function($t) {
            return [
                'title' => $t['title'],
                'file' => $t['file_path']
            ];
        }, $music_tracks)); ?>;

        <?php
        // Fetch characters for JS
        $js_chars = [];
        try {
            $c_conn = new mysqli('localhost', 'root', '', 'undertale_game');
            if (!$c_conn->connect_error) {
                $c_conn->set_charset("utf8mb4");
                $c_res = $c_conn->query("SELECT name, role, description, bio FROM characters");
                if ($c_res) {
                    while ($r = $c_res->fetch_assoc()) {
                        $js_chars[strtolower($r['name'])] = [
                            'name' => strtoupper($r['name']),
                            'role' => $r['role'],
                            'description' => $r['description'],
                            'bio' => $r['bio'],
                            // Mapping icons for known ones, default for others
                            'icon' => '👤' 
                        ];
                    }
                }
                $c_conn->close();
            }
        } catch (Exception $e) {}
        ?>
        const dbCharacters = <?php echo json_encode($js_chars); ?>;
    </script>
    <script src="js/script.js?v=<?php echo time(); ?>"></script>
    <script>
        // Toggle dropdown menu
        function toggleDropdown() {
            const menu = document.getElementById('dropdownMenu');
            menu.classList.toggle('active');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.querySelector('.profile-dropdown');
            if (dropdown && !dropdown.contains(e.target)) {
                const menu = document.getElementById('dropdownMenu');
                if (menu) menu.classList.remove('active');
            }
        });

        // Modal functions
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
            }
        }

        function closeModal(modalId) {
            // Handle character modal without ID (for script.js compatibility)
            if (!modalId) {
                const modal = document.getElementById('characterModal');
                if (modal) {
                    modal.classList.remove('active');
                }
                return;
            }
            
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
            }
        }

        // Close modal when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });

        // Load profile data
        function loadProfileData() {
            fetch('api/get_profile.php')
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const profile = data.data;
                        const content = `
                            <div class="profile-info">
                                <div class="profile-info-header">
                                    <h3>★ ${profile.username} ★</h3>
                                </div>
                                <div class="profile-info-row">
                                    <span class="profile-info-label">EMAIL</span>
                                    <span class="profile-info-value">${profile.email}</span>
                                </div>
                                <div class="profile-info-row">
                                    <span class="profile-info-label">STATUS</span>
                                    <span class="profile-info-value">${profile.status || '(no status)'}</span>
                                </div>
                                <div class="profile-info-row">
                                    <span class="profile-info-label">JOINED</span>
                                    <span class="profile-info-value">${new Date(profile.created_at).toLocaleDateString('id-ID')}</span>
                                </div>

                            </div>
                        `;
                        document.getElementById('profileContent').innerHTML = content;
                    }
                })
                .catch(e => console.error('Error:', e));
        }

        // Load edit data
        function loadEditData() {
            fetch('api/get_profile.php')
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('editUsername').value = data.data.username;
                        document.getElementById('editEmail').value = data.data.email;
                    }
                })
                .catch(e => console.error('Error:', e));
        }

        // Edit profile
        document.getElementById('editForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('editEmail').value;
            
            fetch('api/update_profile.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `email=${encodeURIComponent(email)}`
            })
            .then(r => r.json())
            .then(data => {
                const msgEl = document.getElementById('editMessage');
                if (data.success) {
                    msgEl.innerHTML = '<div class="message success">✓ Profile updated successfully!</div>';
                    setTimeout(() => closeModal('editModal'), 1500);
                } else {
                    msgEl.innerHTML = `<div class="message error">✗ ${data.message}</div>`;
                }
            });
        });

        // Change password
        document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const currentPwd = document.getElementById('currentPassword').value;
            const newPwd = document.getElementById('newPassword').value;
            const confirmPwd = document.getElementById('confirmPassword').value;
            
            if (newPwd !== confirmPwd) {
                document.getElementById('passwordMessage').innerHTML = '<div class="message error">✗ Passwords do not match!</div>';
                return;
            }
            
            fetch('api/change_password.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `current_password=${encodeURIComponent(currentPwd)}&new_password=${encodeURIComponent(newPwd)}`
            })
            .then(r => r.json())
            .then(data => {
                const msgEl = document.getElementById('passwordMessage');
                if (data.success) {
                    msgEl.innerHTML = '<div class="message success">✓ Password changed successfully!</div>';
                    setTimeout(() => {
                        closeModal('passwordModal');
                        document.getElementById('passwordForm').reset();
                    }, 1500);
                } else {
                    msgEl.innerHTML = `<div class="message error">✗ ${data.message}</div>`;
                }
            });
        });

        // Add status
        document.getElementById('statusText')?.addEventListener('input', function() {
            document.getElementById('charCount').textContent = this.value.length;
        });

        document.getElementById('statusForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const status = document.getElementById('statusText').value;
            
            fetch('api/add_status.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `status=${encodeURIComponent(status)}`
            })
            .then(r => r.json())
            .then(data => {
                const msgEl = document.getElementById('statusMessage');
                if (data.success) {
                    msgEl.innerHTML = '<div class="message success">✓ Status added successfully!</div>';
                    setTimeout(() => {
                        closeModal('statusModal');
                        document.getElementById('statusForm').reset();
                        document.getElementById('charCount').textContent = '0';
                    }, 1500);
                } else {
                    msgEl.innerHTML = `<div class="message error">✗ ${data.message}</div>`;
                }
            });
        });

        // Logout
        function logout() {
            openModal('logoutModal');
        }

        // Confirm logout
        function confirmLogout() {
            fetch('api/logout.php')
                .then(() => {
                    window.location.href = 'index.php';
                });
        }

        // Diagnostic: log that showCharacterModal exists
        window.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded. Checking showCharacterModal function...');
            console.log('showCharacterModal type:', typeof showCharacterModal);
        });
    </script>
</body>
</html>