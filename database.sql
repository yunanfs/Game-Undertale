-- Create database
CREATE DATABASE IF NOT EXISTS undertale_game CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE undertale_game;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active TINYINT(1) DEFAULT 1,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Game scores table
CREATE TABLE IF NOT EXISTS game_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    score INT NOT NULL DEFAULT 0,
    turns_used INT NOT NULL DEFAULT 0,
    damage_dealt INT NOT NULL DEFAULT 0,
    hp_remaining INT NOT NULL DEFAULT 20,
    route_type VARCHAR(20) DEFAULT 'neutral',
    played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_score (score)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User progress table
CREATE TABLE IF NOT EXISTS user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    level INT DEFAULT 1,
    exp INT DEFAULT 0,
    gold INT DEFAULT 0,
    battles_won INT DEFAULT 0,
    battles_lost INT DEFAULT 0,
    pacifist_count INT DEFAULT 0,
    genocide_count INT DEFAULT 0,
    items_collected TEXT,
    achievements TEXT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Characters unlocked table
CREATE TABLE IF NOT EXISTS characters_unlocked (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    character_name VARCHAR(50) NOT NULL,
    unlocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_character (user_id, character_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert demo user (password: undertale123)
INSERT INTO users (username, email, password) VALUES 
('player1', 'player1@undertale.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('frisk', 'frisk@underground.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE username=username;

-- Insert demo progress
INSERT INTO user_progress (user_id, level, exp, gold, battles_won) 
SELECT id, 1, 0, 0, 0 FROM users WHERE username = 'player1'
ON DUPLICATE KEY UPDATE user_id=user_id;

-- Sample game scores
INSERT INTO game_scores (user_id, score, turns_used, damage_dealt, hp_remaining, route_type)
SELECT id, 1000, 5, 50, 15, 'pacifist' FROM users WHERE username = 'player1';

INSERT INTO game_scores (user_id, score, turns_used, damage_dealt, hp_remaining, route_type)
SELECT id, 1500, 8, 80, 12, 'neutral' FROM users WHERE username = 'player1';