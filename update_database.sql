-- Update für bestehende Datenbank: Status-Enum erweitern
USE termin2praxis;

-- Status-Spalte um 'abgelehnt' und 'storniert' erweitern
ALTER TABLE appointments 
MODIFY COLUMN status ENUM('frei', 'angefragt', 'bestätigt', 'abgelehnt', 'storniert') DEFAULT 'frei';

-- Spalten für Bestätigung durch Arzt hinzufügen
ALTER TABLE appointments 
ADD COLUMN confirmed_by INT DEFAULT NULL AFTER status,
ADD COLUMN confirmed_at TIMESTAMP NULL AFTER confirmed_by,
ADD FOREIGN KEY (confirmed_by) REFERENCES users(id) ON DELETE SET NULL;

-- Arzt-Namen aktualisieren
UPDATE users SET name = 'Florian Albrecht' WHERE email = 'arzt@termin2praxis.de' AND role = 'arzt';
