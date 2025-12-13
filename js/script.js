// Game Variables
let playerHp = 20;
let maxHp = 20;
let gameActive = true;
let bullets = [];
let keys = {};
let battlePhase = 'choose';
let turnCount = 0;
let totalDamage = 0;
let score = 0;
let enemyHp = 50;
let maxEnemyHp = 50;

// Smooth scroll function
function scrollToSection(id) {
    document.getElementById(id).scrollIntoView({ behavior: 'smooth' });
}

// Update HP
function updateHP(newHp) {
    playerHp = Math.max(0, Math.min(maxHp, newHp));
    const percentage = (playerHp / maxHp) * 100;
    document.getElementById('playerHp').style.width = percentage + '%';
    document.getElementById('hpText').textContent = playerHp + ' / ' + maxHp;

    if (playerHp <= 0) {
        gameOver();
    }
}

// Battle Actions
function handleFight() {
    if (battlePhase !== 'choose') return;
    battlePhase = 'dodge';
    turnCount++;
    const damage = Math.floor(Math.random() * 8) + 5;
    totalDamage += damage;
    enemyHp -= damage;
    score += damage * 10;

    updateStats();

    document.getElementById('battleText').innerHTML =
        `* You attacked FROGGIT!<br>* Dealt ${damage} damage!<br>* Enemy HP: ${Math.max(0, enemyHp)}/${maxEnemyHp}<br>* Get ready to dodge!`;

    if (enemyHp <= 0) {
        setTimeout(victory, 1500);
    } else {
        setTimeout(startDodgePhase, 1500);
    }
}

function handleAct() {
    if (battlePhase !== 'choose') return;
    battlePhase = 'choose';
    turnCount++;
    score += 50;

    updateStats();

    const actions = [
        '* You checked FROGGIT.<br>* ATK 4 DEF 1<br>* Life is difficult for this enemy.',
        '* You complimented FROGGIT.<br>* FROGGIT doesn\'t know how to react!',
        '* You threatened FROGGIT.<br>* FROGGIT is scared!',
        '* You told FROGGIT a joke.<br>* FROGGIT is confused.'
    ];

    document.getElementById('battleText').innerHTML = actions[Math.floor(Math.random() * actions.length)];

    setTimeout(() => {
        battlePhase = 'choose';
        document.getElementById('battleText').innerHTML = '* What will you do?';
    }, 2000);
}

function handleItem() {
    if (battlePhase !== 'choose') return;
    turnCount++;
    const healAmount = 10;
    updateHP(playerHp + healAmount);
    score += 30;

    updateStats();

    document.getElementById('battleText').innerHTML =
        `* You ate a Monster Candy.<br>* Recovered ${healAmount} HP!<br>* HP: ${playerHp}/${maxHp}`;

    setTimeout(() => {
        battlePhase = 'choose';
        document.getElementById('battleText').innerHTML = '* What will you do?';
    }, 2000);
}

function handleMercy() {
    if (battlePhase !== 'choose') return;
    turnCount++;
    score += 200;

    updateStats();

    document.getElementById('battleText').innerHTML =
        `* You showed MERCY to FROGGIT.<br>* FROGGIT spared you back.<br>* Battle ended peacefully!`;

    setTimeout(victory, 2000);
}

// Update battle stats
function updateStats() {
    document.getElementById('turnCount').textContent = turnCount;
    document.getElementById('totalDamage').textContent = totalDamage;
    document.getElementById('score').textContent = score;

    // RPG Stats (calculated based on score for demo)
    const currentGold = Math.floor(score * 0.5);
    const currentExp = Math.floor(score * 0.2);

    // Set just the numbers (labels are now in HTML)
    document.getElementById('goldDisplay').textContent = "GOLD " + currentGold;
    document.getElementById('expDisplay').textContent = "EXP " + currentExp;

    // Also update victory screen placeholders if they exist
    const winGold = document.getElementById('winGold');
    const winExp = document.getElementById('winExp');
    if (winGold) winGold.textContent = currentGold;
    if (winExp) winExp.textContent = currentExp;
}

// Dodge Phase
function startDodgePhase() {
    document.getElementById('battleText').innerHTML = '* FROGGIT attacks! Dodge the bullets!';
    bullets = [];
    createBulletPattern();

    setTimeout(() => {
        battlePhase = 'choose';
        clearBullets();
        document.getElementById('battleText').innerHTML = '* What will you do?';
    }, 4000);
}

// New Advanced Attack Logic
function createBulletPattern() {
    const arena = document.querySelector('.battle-arena');
    createSpitAttack(arena);
}

function createSpitAttack(arena) {
    const enemy = document.getElementById('enemySprite');
    const enemyRect = enemy.getBoundingClientRect();
    const arenaRect = arena.getBoundingClientRect();

    // Calculate spawn point relative to arena (Mouth position approx center of sprite)
    const startX = (enemyRect.left - arenaRect.left) + (enemyRect.width / 2);
    const startY = (enemyRect.top - arenaRect.top) + (enemyRect.height / 2) + 20; // +20 to be near "mouth"

    // Number of bullets per wave
    const bulletCount = 20;

    for (let i = 0; i < bulletCount; i++) {
        setTimeout(() => {
            const bullet = document.createElement('div');
            bullet.className = 'bullet';
            // Random size for variety
            const size = Math.random() < 0.3 ? 12 : 8;
            bullet.style.width = size + 'px';
            bullet.style.height = size + 'px';
            bullet.style.borderRadius = '50%'; // Make them round/pellet like
            bullet.style.left = startX + 'px';
            bullet.style.top = startY + 'px';

            // Random direction 360 degrees
            const angle = Math.random() * Math.PI * 2;
            const speed = 3 + Math.random() * 4; // Variable speed

            bullet.dataset.vx = Math.cos(angle) * speed;
            bullet.dataset.vy = Math.sin(angle) * speed; // Can be negative (upwards)

            arena.appendChild(bullet);
            animateBulletVector(bullet);
        }, i * 300); // Staggered release
    }
}

function animateBulletVector(bullet) {
    const arena = document.querySelector('.battle-arena');
    const arenaRect = arena.getBoundingClientRect();

    // Use requestAnimationFrame for smoother movement if possible, but keeping setInterval for compatibility with existing structure
    const interval = setInterval(() => {
        if (!document.body.contains(bullet)) {
            clearInterval(interval);
            return;
        }

        let left = parseFloat(bullet.style.left);
        let top = parseFloat(bullet.style.top);

        // Retrieve vector
        const vx = parseFloat(bullet.dataset.vx);
        const vy = parseFloat(bullet.dataset.vy);

        // Apply movement
        left += vx;
        top += vy;

        // Boundary check
        if (top > arena.offsetHeight || top < -20 || left < -20 || left > arena.offsetWidth) {
            bullet.remove();
            clearInterval(interval);
        } else {
            bullet.style.left = left + 'px';
            bullet.style.top = top + 'px';
            checkCollision(bullet);
        }
    }, 20);
}

function checkCollision(bullet) {
    const soul = document.getElementById('playerSoul');
    if (!soul) return;

    const soulRect = soul.getBoundingClientRect();
    const bulletRect = bullet.getBoundingClientRect();

    if (!(soulRect.right < bulletRect.left ||
        soulRect.left > bulletRect.right ||
        soulRect.bottom < bulletRect.top ||
        soulRect.top > bulletRect.bottom)) {
        updateHP(playerHp - 3);
        bullet.remove();
        flashScreen();
    }
}

function flashScreen() {
    const arena = document.querySelector('.battle-arena');
    arena.style.background = '#ff0000';
    setTimeout(() => {
        arena.style.background = '#000';
    }, 100);
}

function clearBullets() {
    const bullets = document.querySelectorAll('.bullet');
    bullets.forEach(bullet => bullet.remove());
}

// Soul Movement
document.addEventListener('keydown', (e) => {
    keys[e.key] = true;
    moveSoul();
});

document.addEventListener('keyup', (e) => {
    keys[e.key] = false;
});

function moveSoul() {
    const soul = document.getElementById('playerSoul');
    if (!soul) return;

    const speed = 6;
    const rect = soul.getBoundingClientRect();
    const arena = document.querySelector('.battle-arena').getBoundingClientRect();

    if (keys['ArrowLeft'] || keys['a'] || keys['A']) {
        if (rect.left > arena.left + 15) {
            soul.style.left = (soul.offsetLeft - speed) + 'px';
        }
    }
    if (keys['ArrowRight'] || keys['d'] || keys['D']) {
        if (rect.right < arena.right - 15) {
            soul.style.left = (soul.offsetLeft + speed) + 'px';
        }
    }
    if (keys['ArrowUp'] || keys['w'] || keys['W']) {
        if (rect.top > arena.top + 15) {
            soul.style.top = (soul.offsetTop - speed) + 'px';
        }
    }
    if (keys['ArrowDown'] || keys['s'] || keys['S']) {
        if (rect.bottom < arena.bottom - 15) {
            soul.style.top = (soul.offsetTop + speed) + 'px';
        }
    }
}

// Game Over
function gameOver() {
    clearBullets();
    document.getElementById('gameOver').classList.add('active');
    sendBattleResult('loss');
}

function victory() {
    clearBullets();
    document.getElementById('finalTurns').textContent = turnCount;
    document.getElementById('finalDamage').textContent = totalDamage;
    document.getElementById('finalScore').textContent = score;

    // RPG Stats (calculated based on score for demo)
    const currentGold = Math.floor(score * 0.5);
    const currentExp = Math.floor(score * 0.2);

    const winGold = document.getElementById('winGold');
    const winExp = document.getElementById('winExp');
    if (winGold) winGold.textContent = currentGold;
    if (winExp) winExp.textContent = currentExp;

    // Update the list view stats
    const winGoldStat = document.getElementById('winGoldStat');
    const winExpStat = document.getElementById('winExpStat');
    if (winGoldStat) winGoldStat.textContent = currentGold;
    if (winExpStat) winExpStat.textContent = currentExp;

    const vScreen = document.getElementById('victory');
    if (vScreen) vScreen.style.display = 'block'; // Override inline display:none

    sendBattleResult('win');
}

function sendBattleResult(result) {
    // result should be 'win' or 'loss'
    const formData = new FormData();
    formData.append('result', result);

    fetch('api/update_battle_stats.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Battle result recorded:', result);
            } else {
                console.error('Failed to record battle result:', data.message);
            }
        })
        .catch(error => {
            console.error('Error sending battle result:', error);
        });
}

function resetBattle() {
    playerHp = 20;
    enemyHp = 50;
    turnCount = 0;
    totalDamage = 0;
    score = 0;
    battlePhase = 'choose';

    updateHP(20);
    updateStats();
    clearBullets();

    const soul = document.getElementById('playerSoul');
    if (soul) {
        soul.style.left = '50%';
        soul.style.bottom = '50px';
    }

    document.getElementById('battleText').innerHTML =
        '* A wild FROGGIT appeared!<br>* It doesn\'t seem to know why it\'s here.';
    document.getElementById('gameOver').classList.remove('active');
    const vScreen = document.getElementById('victory');
    if (vScreen) {
        vScreen.style.display = 'none';
        vScreen.classList.remove('active');
    }
}

// Character Modal
const characterData = {
    frisk: {
        name: 'FRISK',
        icon: '👤',
        description: 'The eighth human to fall into the Underground. The protagonist controlled by the player.',
        quote: '"..."',
        abilities: 'Can ACT, FIGHT, use ITEMS, and show MERCY'
    },
    sans: {
        name: 'SANS',
        icon: '💀',
        description: 'A lazy skeleton who loves puns and ketchup. Don\'t let his demeanor fool you.',
        quote: '"it\'s a beautiful day outside..."',
        abilities: 'Teleportation, Gaster Blasters, bone attacks'
    },
    papyrus: {
        name: 'PAPYRUS',
        icon: '🦴',
        description: 'Sans\'s enthusiastic brother who dreams of joining the Royal Guard.',
        quote: '"NYEH HEH HEH!"',
        abilities: 'Blue attack mastery, puzzle creation'
    },
    toriel: {
        name: 'TORIEL',
        icon: '👑',
        description: 'The caretaker of the Ruins and former Queen of the Underground.',
        quote: '"My child... I will protect you."',
        abilities: 'Fire magic, cooking, protection spells'
    },
    undyne: {
        name: 'UNDYNE',
        icon: '🐟',
        description: 'The head of the Royal Guard. Passionate about justice and anime!',
        quote: '"NGAHHH!!!"',
        abilities: 'Spear summoning, Determination'
    },
    flowey: {
        name: 'FLOWEY',
        icon: '🌻',
        description: 'A sentient flower with sinister motives.',
        quote: '"In this world, it\'s kill or BE killed!"',
        abilities: 'SAVE and LOAD, friendliness pellets'
    },
    'flower pot': {
        name: 'FLOWER POT',
        icon: '🪴',
        description: 'Just a normal flower pot. Or is it?',
        quote: '"..."',
        abilities: 'Photosynthesis, sitting still'
    },
    fahri: {
        name: 'FAHRI',
        icon: '🧑',
        description: 'A mysterious character added to the game.',
        quote: '"Hello world!"',
        abilities: 'Coding, debugging'
    },
    agoyy: {
        name: 'AGOYY',
        icon: '😎',
        description: 'orang ganteng ft unsoed blater purbalingga',
        quote: '"Always stay cool"',
        abilities: 'Charm, Technology, Leadership'
    }
};

function showCharacterModal(charId) {
    const modal = document.getElementById('characterModal');
    if (!modal) return;

    // charId is the name (e.g., 'frisk') passed from index.php
    const normalizedId = String(charId).toLowerCase().trim();

    // Default structure
    let char = null;

    // 1. Check dynamic DB data injected from PHP
    if (typeof dbCharacters !== 'undefined' && dbCharacters[normalizedId]) {
        const dbChar = dbCharacters[normalizedId];
        char = {
            name: dbChar.name,
            icon: '👤', // Default icon
            description: dbChar.description,
            bio: dbChar.bio,
            abilities: dbChar.role
        };
    }

    // 2. Check hardcoded data (for icons, specific overrides)
    if (characterData[normalizedId]) {
        const localChar = characterData[normalizedId];
        if (char) {
            // Merge: prefer local icon
            char.icon = localChar.icon;
            // If bio is missing in DB but quote exists in local, maybe we could use quote as bio? 
            // But user said "no quote in admin", so let's stick to admin fields aka Bio.
            // We won't map local 'quote' to 'bio' to respect the "match admin" request.
            if (!char.abilities) char.abilities = localChar.abilities;
        } else {
            // Use local data entirely if not in DB
            char = localChar;
            // Map local 'quote' to bio for consistent display if utilizing legacy data
            if (!char.bio && char.quote) {
                char.bio = char.quote;
            }
        }
    }

    if (char) {
        const body = document.getElementById('modalBody');
        // Build the bio section only if it exists
        const bioHtml = char.bio ? `
            <div style="border: 3px solid #fff; padding: 15px; margin: 20px 0;">
                <strong style="font-size: 0.9rem;">BIOGRAPHY:</strong><br>
                <span style="font-size: 0.75rem; color: #aaa; font-style: italic; line-height: 1.6; display: block; margin-top: 10px;">${char.bio}</span>
            </div>` : '';

        body.innerHTML = `
            <div style="text-align: center; font-size: 5rem; margin: 20px 0; min-height: 120px; display: flex; align-items: center; justify-content: center;">${char.icon}</div>
            <h2 style="text-align: center; margin-bottom: 20px; font-size: 1.5rem;">${char.name}</h2>
            <p style="font-size: 0.8rem; line-height: 2; margin-bottom: 20px; text-align: center;">${char.description}</p>
            ${bioHtml}
            <div style="border: 3px solid #fff; padding: 15px; margin: 20px 0; background: rgba(255,255,255,0.1);">
                <strong style="font-size: 0.9rem;">ABILITIES/ROLE:</strong><br>
                <span style="font-size: 0.75rem; color: #aaa;">${char.abilities}</span>
            </div>
        `;
        modal.classList.add('active');
    } else {
        console.log('Character data not found for:', charId);
        const body = document.getElementById('modalBody');
        body.innerHTML = `<p style="text-align: center; color: #ff0000;">Character data missing for: ${charId}</p>`;
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId || 'characterModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

// Music Player
// Music Player
let tracks = [];
let trackFiles = [];
let audioPlayer = new Audio();
let currentTrackIndex = 0;
let isPlaying = false;

if (typeof dbTracks !== 'undefined' && dbTracks.length > 0) {
    tracks = dbTracks.map(t => t.title);
    trackFiles = dbTracks.map(t => t.file);
} else {
    // Fallback if no DB tracks
    tracks = ['No Music Available'];
    trackFiles = [];
}

function selectTrack(index) {
    currentTrackIndex = index;
    document.querySelectorAll('.track').forEach(t => t.classList.remove('active'));

    const trackElements = document.querySelectorAll('.track');
    if (trackElements[index]) {
        trackElements[index].classList.add('active');
    }

    if (tracks[index]) {
        document.getElementById('currentTrack').textContent = tracks[index];
    }

    if (trackFiles[index]) {
        console.log("Loading  track:", tracks[index], "from", trackFiles[index]);
        audioPlayer.src = trackFiles[index];

        // Add error listener
        audioPlayer.onerror = (e) => {
            console.error("Error loading audio source:", audioPlayer.error);
            alert("Error loading audio file. Check console for details.");
        };

        // Auto-play
        const playPromise = audioPlayer.play();
        if (playPromise !== undefined) {
            playPromise.then(() => {
                console.log("Playback started successfully.");
                isPlaying = true;
                document.getElementById('playBtn').textContent = '❚❚';
            }).catch(error => {
                console.error("Playback failed (autoplay policy or other):", error);
                isPlaying = false;
                document.getElementById('playBtn').textContent = '▶';
            });
        }
    } else {
        console.warn("No file defined for track index:", index);
    }
}

function togglePlay() {
    if (isPlaying) {
        audioPlayer.pause();
        isPlaying = false;
        document.getElementById('playBtn').textContent = '▶';
    } else {
        if (!audioPlayer.src && trackFiles[currentTrackIndex]) {
            selectTrack(currentTrackIndex); // Load and play
        } else if (audioPlayer.src) {
            const playPromise = audioPlayer.play();
            if (playPromise !== undefined) {
                playPromise.then(() => {
                    isPlaying = true;
                    document.getElementById('playBtn').textContent = '❚❚';
                }).catch(error => {
                    console.error("Playback failed:", error);
                    isPlaying = false;
                    document.getElementById('playBtn').textContent = '▶';
                });
            }
        }
    }
}

function previousTrack() {
    if (tracks.length === 0) return;
    currentTrackIndex = (currentTrackIndex - 1 + tracks.length) % tracks.length;
    selectTrack(currentTrackIndex);
}

function nextTrack() {
    if (tracks.length === 0) return;
    currentTrackIndex = (currentTrackIndex + 1) % tracks.length;
    selectTrack(currentTrackIndex);
}

// Initialize
updateHP(20);
updateStats();

// ==========================================
// RESTORED GALLERY LOGIC
// ==========================================

// Gallery Modal
function openGalleryModal(title, desc, img) {
    const modal = document.getElementById('galleryModal');
    if (!modal) return;

    document.getElementById('galleryModalTitle').textContent = title;
    document.getElementById('galleryModalDesc').textContent = desc;

    const imgContainer = document.getElementById('galleryModalImage');

    // Check if it's an image path or emoji
    if (img.includes('/') || img.length > 10) {
        imgContainer.innerHTML = `<img src="${img}" style="max-width: 100%; max-height: 300px; border: 4px solid #fff;">`;
    } else {
        imgContainer.innerHTML = img; // Emoji
    }

    modal.classList.add('active');
}

// Gallery Carousel Logic
let galleryIndex = 0;
let galleryInterval;
let itemsPerView = 3;
let isTransitioning = false;

// Infinite Loop Setup
function setupGallery() {
    const track = document.getElementById('galleryTrack');
    if (!track) return;

    // Clear any existing clones if re-running
    const existingClones = track.querySelectorAll('.gallery-clone');
    existingClones.forEach(el => el.remove());

    const items = Array.from(track.children);
    if (items.length === 0) return;

    // Clone count based on max view (3)
    const cloneCount = 3;

    // Create Clones
    const firstClones = items.slice(0, cloneCount).map(item => {
        const clone = item.cloneNode(true);
        clone.classList.add('gallery-clone');
        // Fix click handlers on clones by recreating the onclick if needed
        // Since we use inline onclick HTML attributes, they are cloned automatically.
        return clone;
    });

    const lastClones = items.slice(-cloneCount).map(item => {
        const clone = item.cloneNode(true);
        clone.classList.add('gallery-clone');
        return clone;
    });

    // Prepend and Append
    lastClones.forEach(clone => track.insertBefore(clone, track.firstChild));
    firstClones.forEach(clone => track.appendChild(clone));

    // Set Initial Position (offset by cloneCount)
    galleryIndex = cloneCount;
    updateGalleryPosition(false); // No transition initially

    // Transition End Listener for seamless loop
    track.addEventListener('transitionend', () => {
        const currentItems = track.children;
        const totalRealItems = currentItems.length - (cloneCount * 2);

        isTransitioning = false;

        // Correct jumps
        if (galleryIndex >= totalRealItems + cloneCount) {
            // Jump to start
            galleryIndex = cloneCount;
            updateGalleryPosition(false);
        } else if (galleryIndex < cloneCount) {
            // Jump to end
            galleryIndex = totalRealItems + cloneCount - 1;
            updateGalleryPosition(false);
        }
    });
}

function updateItemsPerView() {
    if (window.innerWidth <= 768) {
        itemsPerView = 1;
    } else if (window.innerWidth <= 1024) {
        itemsPerView = 2;
    } else {
        itemsPerView = 3;
    }
    // Re-adjust position simply by ensuring we are aligned
    updateGalleryPosition(false);
}
window.addEventListener('resize', updateItemsPerView);

function moveGallery(step) {
    if (isTransitioning) return;
    isTransitioning = true;
    galleryIndex += step;
    updateGalleryPosition(true);
}

function updateGalleryPosition(animate) {
    const track = document.getElementById('galleryTrack');
    if (!track) return;

    if (animate) {
        track.style.transition = 'transform 0.5s ease-in-out';
    } else {
        track.style.transition = 'none';
    }

    const shift = -(galleryIndex * (100 / itemsPerView));
    track.style.transform = `translateX(${shift}%)`;
}

function startGalleryAutoSlide() {
    if (galleryInterval) clearInterval(galleryInterval);
    galleryInterval = setInterval(() => {
        moveGallery(1); // Always move forward
    }, 3000);
}

function pauseGallery() {
    clearInterval(galleryInterval);
}

function resumeGallery() {
    startGalleryAutoSlide();
}

// Start on load
document.addEventListener('DOMContentLoaded', () => {
    setupGallery(); // Setup clones first
    updateItemsPerView();
    startGalleryAutoSlide();
});