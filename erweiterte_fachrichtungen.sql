-- Erweiterte Fachrichtungen für Arztpraxen
-- Dieses SQL-Skript fügt mehr Beispiel-Arztpraxen mit allen Fachrichtungen hinzu

USE termin2praxis;

-- Neue Spalten für erweiterte Filterung hinzufügen
ALTER TABLE praxen ADD COLUMN IF NOT EXISTS plz VARCHAR(10);
ALTER TABLE praxen ADD COLUMN IF NOT EXISTS stadt VARCHAR(100);
ALTER TABLE praxen ADD COLUMN IF NOT EXISTS latitude DECIMAL(10, 8) DEFAULT NULL;
ALTER TABLE praxen ADD COLUMN IF NOT EXISTS longitude DECIMAL(11, 8) DEFAULT NULL;
ALTER TABLE praxen ADD COLUMN IF NOT EXISTS kategorie VARCHAR(100);

-- Bestehende Praxen aktualisieren mit PLZ, Stadt und Kategorie
UPDATE praxen SET plz = '12345', stadt = 'Berlin', kategorie = 'Allgemeine & hausärztliche Versorgung' WHERE spezialgebiet = 'Allgemeinmedizin';
UPDATE praxen SET plz = '12345', stadt = 'Berlin', kategorie = 'Chirurgische Fächer' WHERE spezialgebiet = 'Orthopädie';
UPDATE praxen SET plz = '12345', stadt = 'Berlin', kategorie = 'Frauen, Männer & Kinder' WHERE spezialgebiet = 'Pädiatrie';

-- Weitere Beispiel-Arztpraxen mit verschiedenen Fachrichtungen

-- Allgemeine & hausärztliche Versorgung
INSERT INTO praxen (name, beschreibung, adresse, telefon, email, spezialgebiet, kategorie, plz, stadt, latitude, longitude) VALUES
('Praxis Dr. Internist', 'Fachärzte für Innere Medizin mit Schwerpunkt Vorsorgeuntersuchungen und chronische Erkrankungen.', 'Berliner Str. 89, 12345 Berlin', '+49 30 11122233', 'info@internist-berlin.de', 'Innere Medizin', 'Allgemeine & hausärztliche Versorgung', '12345', 'Berlin', 52.5200, 13.4050);

-- Organe & innere Erkrankungen
INSERT INTO praxen (name, beschreibung, adresse, telefon, email, spezialgebiet, kategorie, plz, stadt, latitude, longitude) VALUES
('Kardiologische Praxis Dr. Herz', 'Spezialisiert auf Herzerkrankungen, EKG, Ultraschall und Belastungstests.', 'Herzweg 12, 12345 Berlin', '+49 30 22233344', 'kontakt@kardio-herz.de', 'Kardiologie', 'Organe & innere Erkrankungen', '12345', 'Berlin', 52.5210, 13.4100),
('Gastroenterologie Zentrum', 'Magen-Darm-Erkrankungen, Endoskopie und moderne Diagnostik.', 'Bauchstraße 5, 12345 Berlin', '+49 30 33344455', 'info@gastro-zentrum.de', 'Gastroenterologie', 'Organe & innere Erkrankungen', '12345', 'Berlin', 52.5180, 13.4000),
('Pneumologie Praxis Dr. Lunge', 'Lungenerkrankungen, Allergietests und Lungenfunktionsprüfung.', 'Atemweg 21, 12345 Berlin', '+49 30 44455566', 'praxis@pneumo-lunge.de', 'Pneumologie', 'Organe & innere Erkrankungen', '12345', 'Berlin', 52.5230, 13.4120),
('Nephrologisches Zentrum', 'Nierenerkrankungen, Dialyse und Blutdrucktherapie.', 'Nierenplatz 8, 12345 Berlin', '+49 30 55566677', 'info@nephro-zentrum.de', 'Nephrologie', 'Organe & innere Erkrankungen', '12345', 'Berlin', 52.5190, 13.4080),
('Endokrinologie Dr. Hormone', 'Hormonelle Störungen, Diabetes und Schilddrüsenerkrankungen.', 'Hormonstraße 15, 12345 Berlin', '+49 30 66677788', 'kontakt@endokrino.de', 'Endokrinologie', 'Organe & innere Erkrankungen', '12345', 'Berlin', 52.5220, 13.4030);

-- Chirurgische Fächer
INSERT INTO praxen (name, beschreibung, adresse, telefon, email, spezialgebiet, kategorie, plz, stadt, latitude, longitude) VALUES
('Chirurgische Praxis Berlin', 'Allgemeinchirurgie mit ambulanten Operationen und Nachsorge.', 'Operationsweg 33, 12345 Berlin', '+49 30 77788899', 'info@chirurgie-berlin.de', 'Allgemeinchirurgie', 'Chirurgische Fächer', '12345', 'Berlin', 52.5170, 13.3990),
('Unfallchirurgie & Sportmedizin', 'Behandlung von Sportverletzungen, Brüchen und Gelenkerkrankungen.', 'Sportplatz 44, 12345 Berlin', '+49 30 88899900', 'praxis@unfall-sport.de', 'Unfallchirurgie', 'Chirurgische Fächer', '12345', 'Berlin', 52.5240, 13.4140),
('Neurochirurgische Praxis', 'Spezialisiert auf Wirbelsäulen- und Nervenchirurgie.', 'Neuroweg 17, 12345 Berlin', '+49 30 99900011', 'info@neurochirurgie.de', 'Neurochirurgie', 'Chirurgische Fächer', '12345', 'Berlin', 52.5160, 13.3970),
('Plastische Chirurgie Dr. Schön', 'Ästhetische und rekonstruktive Chirurgie, Narbenbehandlung.', 'Schönheitsallee 9, 12345 Berlin', '+49 30 00011122', 'kontakt@plastik-schoen.de', 'Plastische Chirurgie', 'Chirurgische Fächer', '12345', 'Berlin', 52.5250, 13.4160);

-- Kopf, Sinne & Nerven
INSERT INTO praxen (name, beschreibung, adresse, telefon, email, spezialgebiet, kategorie, plz, stadt, latitude, longitude) VALUES
('Neurologische Praxis Dr. Neuro', 'Behandlung von Nervenkrankheiten, Kopfschmerzen und neurologischen Störungen.', 'Nervenstraße 27, 12345 Berlin', '+49 30 11122233', 'info@neuro-praxis.de', 'Neurologie', 'Kopf, Sinne & Nerven', '12345', 'Berlin', 52.5150, 13.3960),
('Psychiatrie & Psychotherapie Zentrum', 'Psychische Erkrankungen, Therapie und Beratung.', 'Seelenweg 13, 12345 Berlin', '+49 30 22233344', 'kontakt@psychiatrie-zentrum.de', 'Psychiatrie', 'Kopf, Sinne & Nerven', '12345', 'Berlin', 52.5260, 13.4180),
('Augenarztpraxis Dr. Sicht', 'Augenuntersuchungen, Sehtest und Behandlung von Augenkrankheiten.', 'Sehstraße 31, 12345 Berlin', '+49 30 33344455', 'praxis@augen-sicht.de', 'Augenheilkunde', 'Kopf, Sinne & Nerven', '12345', 'Berlin', 52.5140, 13.3940),
('HNO-Praxis Dr. Ohr', 'Hals-Nasen-Ohren-Heilkunde, Hörtests und Allergiebehandlung.', 'Hörweg 19, 12345 Berlin', '+49 30 44455566', 'info@hno-ohr.de', 'HNO', 'Kopf, Sinne & Nerven', '12345', 'Berlin', 52.5270, 13.4200);

-- Frauen, Männer & Kinder
INSERT INTO praxen (name, beschreibung, adresse, telefon, email, spezialgebiet, kategorie, plz, stadt, latitude, longitude) VALUES
('Frauenarztpraxis Dr. Gyno', 'Gynäkologie, Schwangerschaftsvorsorge und Krebsfrüherkennung.', 'Frauenplatz 24, 12345 Berlin', '+49 30 55566677', 'kontakt@gyno-praxis.de', 'Gynäkologie', 'Frauen, Männer & Kinder', '12345', 'Berlin', 52.5130, 13.3920),
('Urologische Praxis', 'Männer- und Frauengesundheit, Nieren- und Blasenerkrankungen.', 'Urologiestraße 16, 12345 Berlin', '+49 30 66677788', 'info@uro-praxis.de', 'Urologie', 'Frauen, Männer & Kinder', '12345', 'Berlin', 52.5280, 13.4220),
('Kinder- und Jugendpsychiatrie', 'Psychische Gesundheit für Kinder und Jugendliche, Therapie und Beratung.', 'Jugendweg 11, 12345 Berlin', '+49 30 77788899', 'praxis@kjp-berlin.de', 'Kinder- und Jugendpsychiatrie', 'Frauen, Männer & Kinder', '12345', 'Berlin', 52.5120, 13.3900);

-- Haut, Allergien & Immunsystem
INSERT INTO praxen (name, beschreibung, adresse, telefon, email, spezialgebiet, kategorie, plz, stadt, latitude, longitude) VALUES
('Dermatologie Praxis Dr. Haut', 'Hauterkrankungen, Akne, Allergien und ästhetische Dermatologie.', 'Hautweg 28, 12345 Berlin', '+49 30 88899900', 'info@derma-haut.de', 'Dermatologie', 'Haut, Allergien & Immunsystem', '12345', 'Berlin', 52.5290, 13.4240),
('Allergologie Zentrum', 'Allergietests, Desensibilisierung und Behandlung allergischer Erkrankungen.', 'Allergieplatz 7, 12345 Berlin', '+49 30 99900011', 'kontakt@allergo-zentrum.de', 'Allergologie', 'Haut, Allergien & Immunsystem', '12345', 'Berlin', 52.5110, 13.3880);

-- Krebs & schwere Erkrankungen
INSERT INTO praxen (name, beschreibung, adresse, telefon, email, spezialgebiet, kategorie, plz, stadt, latitude, longitude) VALUES
('Onkologische Praxis', 'Krebsbehandlung, Chemotherapie und onkologische Nachsorge.', 'Hoffnungsstraße 42, 12345 Berlin', '+49 30 00011122', 'info@onko-praxis.de', 'Onkologie', 'Krebs & schwere Erkrankungen', '12345', 'Berlin', 52.5300, 13.4260),
('Palliativmedizin Dr. Care', 'Lindernde Behandlung bei schweren Erkrankungen, Schmerztherapie.', 'Pflegeweg 14, 12345 Berlin', '+49 30 11122233', 'kontakt@palliativ-care.de', 'Palliativmedizin', 'Krebs & schwere Erkrankungen', '12345', 'Berlin', 52.5100, 13.3860);

-- Diagnostik & Technik
INSERT INTO praxen (name, beschreibung, adresse, telefon, email, spezialgebiet, kategorie, plz, stadt, latitude, longitude) VALUES
('Radiologisches Institut', 'Röntgen, CT, MRT und moderne Bildgebung für präzise Diagnosen.', 'Röntgenstraße 50, 12345 Berlin', '+49 30 22233344', 'info@radio-institut.de', 'Radiologie', 'Diagnostik & Technik', '12345', 'Berlin', 52.5310, 13.4280),
('Labor Dr. Test', 'Umfassende Labordiagnostik, Blutuntersuchungen und Analysen.', 'Laborweg 6, 12345 Berlin', '+49 30 33344455', 'labor@dr-test.de', 'Laboratoriumsmedizin', 'Diagnostik & Technik', '12345', 'Berlin', 52.5090, 13.3840);

-- Prävention & spezielle Versorgung
INSERT INTO praxen (name, beschreibung, adresse, telefon, email, spezialgebiet, kategorie, plz, stadt, latitude, longitude) VALUES
('Arbeitsmedizin Zentrum', 'Betriebsärztliche Versorgung, Vorsorgeuntersuchungen für Unternehmen.', 'Arbeitsplatz 38, 12345 Berlin', '+49 30 44455566', 'info@arbeitsmedizin.de', 'Arbeitsmedizin', 'Prävention & spezielle Versorgung', '12345', 'Berlin', 52.5320, 13.4300),
('Sportmedizin & Fitness', 'Sportärztliche Untersuchungen, Leistungsdiagnostik und Ernährungsberatung.', 'Sportweg 25, 12345 Berlin', '+49 30 55566677', 'kontakt@sportmedizin.de', 'Sportmedizin', 'Prävention & spezielle Versorgung', '12345', 'Berlin', 52.5080, 13.3820),
('Schmerztherapie Praxis', 'Chronische Schmerzen, multimodale Schmerztherapie und Akupunktur.', 'Schmerzfrei 9, 12345 Berlin', '+49 30 66677788', 'praxis@schmerztherapie.de', 'Schmerzmedizin', 'Prävention & spezielle Versorgung', '12345', 'Berlin', 52.5330, 13.4320),
('Geriatrie Dr. Senior', 'Altersmedizin, Gedächtnissprechstunde und ganzheitliche Betreuung älterer Patienten.', 'Seniorenweg 18, 12345 Berlin', '+49 30 77788899', 'info@geriatrie-senior.de', 'Geriatrie', 'Prävention & spezielle Versorgung', '12345', 'Berlin', 52.5070, 13.3800);
