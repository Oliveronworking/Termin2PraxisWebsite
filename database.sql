-- Datenbank erstellen
CREATE DATABASE IF NOT EXISTS termin2praxis;
USE termin2praxis;

-- Tabelle für Benutzer (Ärzte und Patienten)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('arzt', 'patient') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabelle für Termine
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    status ENUM('frei', 'angefragt', 'bestätigt', 'abgelehnt', 'storniert') DEFAULT 'frei',
    confirmed_by INT DEFAULT NULL,
    confirmed_at TIMESTAMP NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (confirmed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Beispieldaten: Ein Arzt und ein Patient erstellen
-- Passwort für beide: "password123" (im Klartext, nur für Demo!)
INSERT INTO users (name, email, password, role) VALUES
('Florian Albrecht', 'arzt@termin2praxis.de', 'password123', 'arzt'),
('Max Mustermann', 'patient@termin2praxis.de', 'password123', 'patient');

-- Beispiel freie Termine
INSERT INTO appointments (user_id, date, time, status) VALUES
(NULL, '2026-01-15', '09:00:00', 'frei'),
(NULL, '2026-01-15', '10:00:00', 'frei'),
(NULL, '2026-01-15', '11:00:00', 'frei'),
(NULL, '2026-01-16', '09:00:00', 'frei'),
(NULL, '2026-01-16', '10:00:00', 'frei');
