-- Update-Script für die Benachrichtigungsfunktion
-- Fügt das is_read Feld zur appointments Tabelle hinzu

USE termin2praxis;

-- Feld hinzufügen, falls es noch nicht existiert
ALTER TABLE appointments 
ADD COLUMN IF NOT EXISTS is_read BOOLEAN DEFAULT FALSE AFTER confirmed_at;

-- Optional: Bestehende Termine als "gelesen" markieren (damit der Zähler bei 0 startet)
-- Kommentieren Sie die nächste Zeile aus, wenn Sie möchten, dass bestehende Termine als ungelesen erscheinen
-- UPDATE appointments SET is_read = TRUE WHERE status IN ('bestätigt', 'abgelehnt', 'storniert');

-- Erfolgreiche Aktualisierung bestätigen
SELECT 'Datenbank erfolgreich aktualisiert!' as Status;
