CREATE DATABASE IF NOT EXISTS agungai_db;
USE agungai_db;

CREATE TABLE IF NOT EXISTS chat_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT,
    sender ENUM('user', 'ai') NOT NULL,
    model_used VARCHAR(50),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES chat_sessions(id) ON DELETE CASCADE
);

-- Menyisipkan data sesi default awal
INSERT INTO chat_sessions (id, title) VALUES (1, 'Project Web Alumni')
ON DUPLICATE KEY UPDATE title='Project Web Alumni';
