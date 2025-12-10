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

function createBulletPattern() {
    const arena = document.querySelector('.battle-arena');
    const patterns = [
        createHorizontalWave,
        createVerticalRain,
        createCirclePattern,
        createRandomPattern
    ];
    
    const pattern = patterns[Math.floor(Math.random() * patterns.length)];
    pattern(arena);
}

function createHorizontalWave(arena) {
    for (let i = 0; i < 8; i++) {
        setTimeout(() => {
            const bullet = document.createElement('div');
            bullet.className = 'bullet';
            bullet.style.width = '15px';
            bullet.style.height = '15px';
            bullet.style.left = '0px';
            bullet.style.top = (50 + i * 30) + 'px';
            arena.appendChild(bullet);
            animateBulletHorizontal(bullet);
        }, i * 300);
    }
}

function createVerticalRain(arena) {
    for (let i = 0; i < 10; i++) {
        setTimeout(() => {
            const bullet = document.createElement('div');
            bullet.className = 'bullet';
            bullet.style.width = '12px';
            bullet.style.height = '12px';
            bullet.style.left = (Math.random() * 90 + 5) + '%';
            bullet.style.top = '0px';
            arena.appendChild(bullet);
            animateBulletVertical(bullet);
        }, i * 400);
    }
}

function createCirclePattern(arena) {
    const centerX = arena.offsetWidth / 2;
    const centerY = arena.offsetHeight / 2;
    const radius = 100;
    
    for (let i = 0; i < 12; i++) {
        setTimeout(() => {
            const angle = (i / 12) * Math.PI * 2;
            const bullet = document.createElement('div');
            bullet.className = 'bullet';
            bullet.style.width = '15px';
            bullet.style.height = '15px';
            bullet.style.left = centerX + 'px';
            bullet.style.top = centerY + 'px';
            arena.appendChild(bullet);
            animateBulletRadial(bullet, angle);
        }, i * 200);
    }
}

function createRandomPattern(arena) {
    for (let i = 0; i < 15; i++) {
        setTimeout(() => {
            const bullet = document.createElement('div');
            bullet.className = 'bullet';
            bullet.style.width = '10px';
            bullet.style.height = '10px';
            bullet.style.left = (Math.random() * 90 + 5) + '%';
            bullet.style.top = (Math.random() * 80 + 10) + '%';
            arena.appendChild(bullet);
        }, i * 200);
    }
}

function animateBulletHorizontal(bullet) {
    let pos = 0;
    const interval = setInterval(() => {
        if (pos >= 800) {
            bullet.remove();
            clearInterval(interval);
        } else {
            pos += 4;
            bullet.style.left = pos + 'px';
            checkCollision(bullet);
        }
    }, 20);
}

function animateBulletVertical(bullet) {
    let pos = 0;
    const interval = setInterval(() => {
        if (pos >= 400) {
            bullet.remove();
            clearInterval(interval);
        } else {
            pos += 3;
            bullet.style.top = pos + 'px';
            checkCollision(bullet);
        }
    }, 20);
}

function animateBulletRadial(bullet, angle) {
    let distance = 0;
    const interval = setInterval(() => {
        if (distance >= 200) {
            bullet.remove();
            clearInterval(interval);
        } else {
            distance += 3;
            const x = parseFloat(bullet.style.left) + Math.cos(angle) * 3;
            const y = parseFloat(bullet.style.top) + Math.sin(angle) * 3;
            bullet.style.left = x + 'px';
            bullet.style.top = y + 'px';
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
}

function victory() {
    clearBullets();
    document.getElementById('finalTurns').textContent = turnCount;
    document.getElementById('finalDamage').textContent = totalDamage;
    document.getElementById('finalScore').textContent = score;
    document.getElementById('victory').classList.add('active');
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
    document.getElementById('victory').classList.remove('active');
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
    }
};

function showCharacterModal(char) {
    const data = characterData[char];
    const modal = document.getElementById('characterModal');
    const body = document.getElementById('modalBody');
    
    body.innerHTML = `
        <div style="text-align: center; font-size: 5rem; margin: 20px 0;">${data.icon}</div>
        <h2 style="text-align: center; margin-bottom: 20px; font-size: 1.5rem;">${data.name}</h2>
        <p style="font-size: 0.8rem; line-height: 2; margin-bottom: 20px;">${data.description}</p>
        <div style="border: 3px solid #fff; padding: 15px; margin: 20px 0;">
            <strong style="font-size: 0.9rem;">ABILITIES:</strong><br>
            <span style="font-size: 0.75rem; color: #aaa;">${data.abilities}</span>
        </div>
        <div style="border: 3px solid #fff; padding: 15px; margin: 20px 0; background: rgba(255,255,255,0.1);">
            <strong style="font-size: 0.9rem;">QUOTE:</strong><br>
            <span style="font-size: 0.75rem; font-style: italic; color: #aaa;">${data.quote}</span>
        </div>
    `;
    
    modal.classList.add('active');
}

function closeModal() {
    document.getElementById('characterModal').classList.remove('active');
}

// Music Player
const tracks = [
    'Once Upon a Time',
    'Your Best Friend',
    'Fallen Down',
    'Megalovania',
    'Death by Glamour'
];

let currentTrackIndex = 0;
let isPlaying = false;

function selectTrack(index) {
    currentTrackIndex = index;
    document.querySelectorAll('.track').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.track')[index].classList.add('active');
    document.getElementById('currentTrack').textContent = tracks[index];
}

function togglePlay() {
    isPlaying = !isPlaying;
    document.getElementById('playBtn').textContent = isPlaying ? '❚❚' : '▶';
}

function previousTrack() {
    currentTrackIndex = (currentTrackIndex - 1 + tracks.length) % tracks.length;
    selectTrack(currentTrackIndex);
}

function nextTrack() {
    currentTrackIndex = (currentTrackIndex + 1) % tracks.length;
    selectTrack(currentTrackIndex);
}

// Initialize
updateHP(20);
updateStats();