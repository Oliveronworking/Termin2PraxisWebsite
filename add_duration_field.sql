-- Dauer-Feld und Beschreibungs-Feld zur appointments Tabelle hinzufügen
-- Beide Felder sind optional

USE termin2praxis;

-- Dauer-Feld hinzufügen (Termindauer in Minuten)
ALTER TABLE appointments 
ADD COLUMN duration INT NULL COMMENT 'Termindauer in Minuten (optional)' 
AFTER time;

-- Beschreibungs-Feld hinzufügen (Art des Termins)
ALTER TABLE appointments 
ADD COLUMN description VARCHAR(255) NULL COMMENT 'Beschreibung/Art des Termins (optional)' 
AFTER duration;

-- Beispielwerte setzen (optional)
-- UPDATE appointments SET duration = 30 WHERE duration IS NULL;
-- UPDATE appointments SET description = 'Kontrolltermin' WHERE description IS NULL;
