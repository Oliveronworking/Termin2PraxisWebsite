# 🏥 Termin2Praxis - Erweiterte Fachrichtungen & Filter

## ✨ Neue Features

### 🔍 Intelligente Suchfunktion
- **Live-Suche** mit automatischer Verzögerung (Debouncing)
- Suche nach:
  - Praxisname
  - Fachgebiet
  - Adresse
  - Beschreibung
- Sofortige Ergebnisse ohne Seiten-Reload

### 📂 Kategorie-Filter (9 Hauptkategorien)

#### 1️⃣ Allgemeine & hausärztliche Versorgung
- Allgemeinmedizin (Hausarzt)
- Innere Medizin (Internist)

#### 2️⃣ Organe & innere Erkrankungen
- Kardiologie (Herz)
- Gastroenterologie (Magen, Darm)
- Pneumologie (Lunge)
- Nephrologie (Nieren)
- Endokrinologie (Hormone)

#### 3️⃣ Chirurgische Fächer
- Allgemeinchirurgie
- Unfallchirurgie / Orthopädie
- Neurochirurgie
- Plastische & Ästhetische Chirurgie

#### 4️⃣ Kopf, Sinne & Nerven
- Neurologie (Nervensystem)
- Psychiatrie & Psychotherapie
- Augenheilkunde
- HNO (Hals-Nasen-Ohren)

#### 5️⃣ Frauen, Männer & Kinder
- Gynäkologie & Geburtshilfe
- Urologie
- Pädiatrie (Kinderarzt)
- Kinder- und Jugendpsychiatrie

#### 6️⃣ Haut, Allergien & Immunsystem
- Dermatologie (Hautarzt)
- Allergologie

#### 7️⃣ Krebs & schwere Erkrankungen
- Onkologie
- Palliativmedizin

#### 8️⃣ Diagnostik & Technik
- Radiologie
- Laboratoriumsmedizin

#### 9️⃣ Prävention & spezielle Versorgung
- Arbeitsmedizin
- Sportmedizin
- Schmerzmedizin
- Geriatrie (Altersmedizin)

### 🏥 Fachgebiets-Filter (30+ Spezialgebiete)

Klicken Sie auf ein spezifisches Fachgebiet, um nur diese Ärzte anzuzeigen:
- Alle Fachrichtungen einzeln filterbar
- Ein-Klick-Filterung
- Visuelle Kennzeichnung aktiver Filter

### 📊 Sortieroptionen

- **Alphabetisch (A-Z)** - Standardsortierung nach Praxisname
- **Nach Kategorie** - Gruppierung nach medizinischer Kategorie
- **Nach Fachgebiet** - Sortierung nach Spezialgebiet

### 🎨 Benutzeroberfläche

#### Filter-Sektion
- Moderne Card-basierte Darstellung
- Interaktive Filter-Chips mit Hover-Effekten
- Klare visuelle Trennung zwischen Kategorien und Fachgebieten
- "Filter zurücksetzen" Button

#### Praxiskarten
- Hover-Animation (Lift-Effekt)
- Fachgebiet-Badge
- Platzhalter für Praxisfotos
- Kompakte Darstellung mit allen wichtigen Infos

#### Mobile-Optimierung
- Responsive Design für alle Bildschirmgrößen
- Touch-optimierte Filter-Chips
- Angepasste Schriftgrößen für kleine Displays

### 📱 Responsive Design

```css
✅ Desktop (> 992px)  - 3 Spalten Layout
✅ Tablet (768-991px) - 2 Spalten Layout
✅ Mobile (< 768px)   - 1 Spalte Layout
```

## 🚀 Installation

### Voraussetzungen
- XAMPP (Apache + MySQL + PHP)
- Bestehendes Termin2Praxis System

### Schritte

1. **Datenbank aktualisieren**
   ```bash
   mysql -u root -p termin2praxis < erweiterte_fachrichtungen.sql
   ```
   
   ODER über phpMyAdmin:
   - Öffnen Sie http://localhost/phpmyadmin
   - Wählen Sie die Datenbank "termin2praxis"
   - SQL-Tab öffnen
   - Inhalt von `erweiterte_fachrichtungen.sql` einfügen und ausführen

2. **Dateien wurden bereits aktualisiert**
   - ✅ index.php - Erweitert mit Filter-Logik
   - ✅ css/style.css - Neue Styles hinzugefügt
   - ✅ JavaScript - Filter-Funktionalität implementiert

3. **Testen**
   - Öffnen Sie http://localhost/Termin2Praxis/index.php
   - Probieren Sie die Filter aus
   - Testen Sie die Suchfunktion
   - Ändern Sie die Sortierung

## 📖 Verwendung

### Suche verwenden
1. Geben Sie einen Suchbegriff in das Suchfeld ein
2. Warten Sie 500ms - die Suche startet automatisch
3. Ergebnisse werden sofort angezeigt

### Filter anwenden
1. **Kategorie wählen** - Klicken Sie auf eine Kategorie (z.B. "Chirurgische Fächer")
2. **Fachgebiet wählen** - Verfeinern Sie mit einem Fachgebiet (z.B. "Orthopädie")
3. **Kombinieren** - Suche + Filter können kombiniert werden
4. **Zurücksetzen** - Klicken Sie "Filter zurücksetzen" für Neustart

### Sortierung ändern
- Wählen Sie eine Sortieroption aus dem Dropdown
- Die Seite lädt automatisch mit der neuen Sortierung

## 🎯 Technische Details

### Datenbank-Schema

```sql
-- Erweiterte praxen-Tabelle
CREATE TABLE praxen (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200),
    beschreibung TEXT,
    adresse VARCHAR(255),
    telefon VARCHAR(50),
    email VARCHAR(100),
    bild_url VARCHAR(255),
    spezialgebiet VARCHAR(100),    -- Neu: Fachgebiet
    kategorie VARCHAR(100),         -- Neu: Hauptkategorie
    plz VARCHAR(10),                -- Neu: Postleitzahl
    stadt VARCHAR(100),             -- Neu: Stadt
    latitude DECIMAL(10, 8),        -- Neu: GPS-Koordinate
    longitude DECIMAL(11, 8),       -- Neu: GPS-Koordinate
    created_at TIMESTAMP
);
```

### PHP-Filter-Logik

```php
// Filter-Parameter aus URL
$filter_kategorie = $_GET['kategorie'] ?? '';
$filter_spezialgebiet = $_GET['spezialgebiet'] ?? '';
$search_query = $_GET['search'] ?? '';
$sort_by = $_GET['sort'] ?? 'name';

// Dynamische SQL-Query mit Prepared Statements
// Schutz vor SQL-Injection
// Flexible Filter-Kombinationen
```

### JavaScript-Funktionalität

```javascript
// Debounced Search (500ms delay)
// Click-Handler für Filter-Chips
// URL-Parameter-Management
// Smooth Scrolling
// Active-State-Management
```

## 📊 Statistiken

- **30+ Arztpraxen** in der Datenbank
- **9 Hauptkategorien**
- **30+ Fachgebiete**
- **Unbegrenzte Filter-Kombinationen**

## 🔮 Zukünftige Features (Roadmap)

### 📍 Entfernungsfilter
```javascript
// GPS-basierte Umkreissuche
// "In meiner Nähe" Funktion
// Sortierung nach Entfernung
```

### 🗺️ Kartenansicht
```javascript
// Google Maps / OpenStreetMap Integration
// Marker für jede Praxis
// Route zum Arzt anzeigen
```

### ⭐ Bewertungssystem
```javascript
// 5-Sterne-Bewertung
// Kommentare
// Durchschnittsbewertung anzeigen
```

### 🔔 Erweiterte Benachrichtigungen
```javascript
// Push-Benachrichtigungen
// E-Mail-Reminder
// SMS-Bestätigung
```

### 📅 Erweiterte Terminverwaltung
```javascript
// Wiederkehrende Termine
// Warteliste
// Online-Videosprechstunde
```

## 🛠️ Wartung & Support

### Neue Praxis hinzufügen

```sql
INSERT INTO praxen (name, beschreibung, adresse, telefon, email, spezialgebiet, kategorie, plz, stadt) 
VALUES (
    'Neue Praxis',
    'Beschreibung...',
    'Straße 123, Berlin',
    '+49 30 12345678',
    'info@praxis.de',
    'Kardiologie',
    'Organe & innere Erkrankungen',
    '12345',
    'Berlin'
);
```

### Praxis bearbeiten

```sql
UPDATE praxen 
SET name = 'Neuer Name',
    beschreibung = 'Neue Beschreibung'
WHERE id = 1;
```

### Praxis löschen

```sql
DELETE FROM praxen WHERE id = 1;
```

## 🎨 Anpassungen

### Farben ändern
Bearbeiten Sie `css/style.css`:
```css
.filter-chip.active {
    background: #YOUR_COLOR;  /* Standard: #0d6efd */
}
```

### Filter-Layout anpassen
Bearbeiten Sie `index.php` Filter-Sektion

### Zusätzliche Kategorien
1. SQL: Neue Kategorie in `praxen` einfügen
2. PHP: Wird automatisch erkannt und angezeigt
3. Kein Code-Update nötig!

## 📞 Kontakt & Support

Bei Fragen oder Problemen:
- Überprüfen Sie die Browser-Konsole
- Prüfen Sie PHP-Fehlerprotokolle
- Testen Sie mit verschiedenen Browsern

## 📄 Lizenz

Dieses Projekt ist Teil von Termin2Praxis.

---

**Entwickelt mit ❤️ für bessere medizinische Terminverwaltung**

## 🎉 Changelog

### Version 2.0 (Aktuell)
- ✅ 30+ Arztpraxen hinzugefügt
- ✅ 9 Hauptkategorien implementiert
- ✅ 30+ Fachgebiete verfügbar
- ✅ Intelligente Suchfunktion
- ✅ Mehrfache Filter-Optionen
- ✅ 3 Sortier-Varianten
- ✅ Responsive Design
- ✅ Moderne UI mit Animationen

### Version 1.0 (Vorher)
- ✅ Basis-Terminverwaltung
- ✅ 3 Beispiel-Praxen
- ✅ Login-System
- ✅ Arzt/Patient-Dashboard
