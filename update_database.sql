-- Update für bestehende Datenbank: Status-Enum erweitern
USE termin2praxis;

-- Status-Spalte um 'abgelehnt' und 'storniert' erweitern
ALTER TABLE appointments 
MODIFY COLUMN status ENUM('frei', 'angefragt', 'bestätigt', 'abgelehnt', 'storniert') DEFAULT 'frei';
