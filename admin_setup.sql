-- Admin table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active TINYINT(1) DEFAULT 1,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Story table
CREATE TABLE IF NOT EXISTS stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    description TEXT,
    order_number INT DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_order (order_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Characters table
CREATE TABLE IF NOT EXISTS characters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    role VARCHAR(50),
    image_url VARCHAR(255),
    bio LONGTEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add status column to user_progress if not exists
ALTER TABLE user_progress ADD COLUMN IF NOT EXISTS status VARCHAR(200);

-- Insert default admin user with password admin123
-- Password hash generated with: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO admins (username, password, is_active) VALUES 
('admin', '$2y$10$PeZzIqKYhGqW7.D.MO2HjOy4aHFyCcAC7a0M8HkZ8QHkKo.mzuiFi', 1)
ON DUPLICATE KEY UPDATE is_active=1;

-- Insert sample stories
INSERT INTO stories (title, content, description, order_number, created_by) VALUES 
('Prologue: The Fall', 'You wake up in the ruins. The light from your phone guides you through the darkness. Where am I? How did I get here? As you explore, you notice strange markings on the walls...', 'The beginning of your journey', 1, 1),
('Encounter', 'You meet a small flower. It introduces itself as Flowey. Something seems off about this flower. It greets you with a cheerful demeanor, but there is something sinister behind those eyes...', 'Your first encounter', 2, 1),
('The Ruins', 'You venture deeper into the Ruins. Ancient architecture surrounds you. Toriel appears, a majestic goat-like creature. She seems protective and motherly. She offers you shelter.', 'Exploring the ancient ruins', 3, 1)
ON DUPLICATE KEY UPDATE updated_at=CURRENT_TIMESTAMP;

-- Insert sample characters
INSERT INTO characters (name, description, role, bio, created_by) VALUES 
('Frisk', 'The protagonist', 'Main Character', 'You are Frisk, a human child who has fallen into the Underground. Your choices will determine the fate of both humans and monsters.', 1),
('Flowey', 'A small golden flower', 'Antagonist', 'A golden flower that greets you in the Ruins. Despite appearing friendly, Flowey harbors deep secrets and darker intentions.', 1),
('Toriel', 'A majestic goat-like creature', 'Guardian', 'The caretaker of the Ruins. Toriel is motherly and protective, seeking to keep you safe from the dangers of the Underground.', 1),
('Sans', 'A skeleton wearing a blue hoodie', 'Ally/Mysterious', 'A comedic skeleton who appears throughout your journey. Behind his jokes lies a mysterious past and perhaps more than meets the eye.', 1),
('Papyrus', 'A tall skeleton with a deep voice', 'Ally', 'Sans\' brother, Papyrus is enthusiastic and energetic. He dreams of becoming a member of the Royal Guard.', 1)
ON DUPLICATE KEY UPDATE updated_at=CURRENT_TIMESTAMP;
