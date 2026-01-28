-- Tabelle für Arztpraxen erstellen
CREATE TABLE IF NOT EXISTS praxen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    beschreibung TEXT,
    adresse VARCHAR(255),
    telefon VARCHAR(50),
    email VARCHAR(100),
    bild_url VARCHAR(255) DEFAULT 'https://via.placeholder.com/400x300?text=Arztpraxis',
    spezialgebiet VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Beziehung zwischen Ärzten und Praxen
ALTER TABLE users ADD COLUMN praxis_id INT DEFAULT NULL;
ALTER TABLE users ADD FOREIGN KEY (praxis_id) REFERENCES praxen(id) ON DELETE SET NULL;

-- Beziehung zwischen Terminen und Praxen
ALTER TABLE appointments ADD COLUMN praxis_id INT DEFAULT NULL;
ALTER TABLE appointments ADD FOREIGN KEY (praxis_id) REFERENCES praxen(id) ON DELETE CASCADE;

-- Beispiel Arztpraxen
INSERT INTO praxen (name, beschreibung, adresse, telefon, email, spezialgebiet) VALUES
('Praxis Dr. Müller', 'Hausarztpraxis mit langjähriger Erfahrung. Wir bieten umfassende medizinische Betreuung für die ganze Familie.', 'Hauptstraße 123, 12345 Berlin', '+49 30 12345678', 'info@praxis-mueller.de', 'Allgemeinmedizin'),
('Orthopädie Zentrum Schmidt', 'Spezialisiert auf Erkrankungen des Bewegungsapparats, Sportmedizin und Rehabilitation.', 'Bahnhofstraße 45, 12345 Berlin', '+49 30 98765432', 'kontakt@ortho-schmidt.de', 'Orthopädie'),
('Kinderarztpraxis Wagner', 'Einfühlsame Kinderbetreuung von der Geburt bis zum Jugendalter. Modern ausgestattete Praxis.', 'Parkweg 78, 12345 Berlin', '+49 30 55566677', 'praxis@kinderarzt-wagner.de', 'Pädiatrie');

-- Bestehendem Arzt eine Praxis zuweisen
UPDATE users SET praxis_id = 1 WHERE email = 'arzt@termin2praxis.de';

-- Allen bestehenden Terminen eine Praxis zuweisen
UPDATE appointments SET praxis_id = 1 WHERE praxis_id IS NULL;
