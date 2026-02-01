-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 01, 2026 at 10:18 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `termin2praxis`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'Termindauer in Minuten (optional)',
  `description` varchar(255) DEFAULT NULL COMMENT 'Beschreibung/Art des Termins (optional)',
  `status` enum('frei','angefragt','bestätigt','abgelehnt','storniert') DEFAULT 'frei',
  `confirmed_by` int(11) DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `praxis_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `date`, `time`, `duration`, `description`, `status`, `confirmed_by`, `confirmed_at`, `is_read`, `created_at`, `praxis_id`) VALUES
(11, 8, '2323-03-12', '22:23:00', NULL, NULL, 'storniert', 7, '2026-01-22 10:42:06', 1, '2026-01-21 16:08:23', 1),
(13, 8, '2323-03-12', '12:03:00', NULL, NULL, 'bestätigt', 7, '2026-01-28 16:39:11', 1, '2026-01-22 10:49:55', 1),
(16, NULL, '0000-00-00', '23:23:00', NULL, NULL, 'storniert', NULL, NULL, 0, '2026-01-28 16:39:19', NULL),
(17, NULL, '0000-00-00', '23:22:00', NULL, NULL, 'storniert', NULL, NULL, 0, '2026-01-28 16:39:29', NULL),
(18, NULL, '0000-00-00', '12:23:00', NULL, NULL, 'storniert', NULL, NULL, 0, '2026-01-28 17:31:27', NULL),
(19, NULL, '0000-00-00', '03:34:00', NULL, NULL, 'storniert', NULL, NULL, 0, '2026-01-28 17:34:41', NULL),
(20, NULL, '2232-03-12', '23:23:00', NULL, NULL, 'storniert', NULL, NULL, 0, '2026-01-28 17:34:47', NULL),
(21, NULL, '2323-03-12', '23:23:00', NULL, NULL, 'storniert', NULL, NULL, 0, '2026-01-28 17:34:51', NULL),
(22, NULL, '2323-03-01', '23:23:00', NULL, NULL, 'storniert', NULL, NULL, 0, '2026-01-28 17:34:58', NULL),
(23, NULL, '2323-04-23', '01:03:00', NULL, NULL, 'storniert', NULL, NULL, 0, '2026-01-28 17:37:27', NULL),
(24, NULL, '3222-02-23', '12:23:00', NULL, NULL, 'storniert', NULL, NULL, 0, '2026-01-28 17:37:36', NULL),
(25, NULL, '2222-02-22', '22:22:00', NULL, NULL, 'storniert', NULL, NULL, 0, '2026-01-28 17:37:48', NULL),
(26, NULL, '2222-02-27', '03:22:00', NULL, NULL, 'storniert', NULL, NULL, 0, '2026-01-28 17:37:55', NULL),
(27, NULL, '4333-03-31', '03:04:00', 15, NULL, 'storniert', NULL, NULL, 0, '2026-01-28 18:00:33', NULL),
(28, NULL, '3222-02-12', '11:11:00', 60, 'test', 'storniert', NULL, NULL, 0, '2026-01-28 18:11:09', NULL),
(29, NULL, '3222-02-12', '11:26:00', 60, 'test', 'storniert', NULL, NULL, 0, '2026-01-28 18:11:09', NULL),
(30, NULL, '3222-02-12', '11:41:00', 60, 'test', 'storniert', NULL, NULL, 0, '2026-01-28 18:11:09', NULL),
(31, 8, '2222-12-31', '12:31:00', 15, 'test', 'angefragt', NULL, NULL, 0, '2026-01-31 20:26:48', 36),
(32, NULL, '2222-12-31', '12:46:00', 15, 'test', 'storniert', NULL, NULL, 0, '2026-01-31 20:26:48', 36),
(33, 8, '2222-12-31', '13:01:00', 15, 'test', 'angefragt', NULL, NULL, 0, '2026-01-31 20:26:48', 36),
(34, NULL, '2222-12-31', '13:16:00', 15, 'test', 'storniert', NULL, NULL, 0, '2026-01-31 20:26:48', 36),
(35, 8, '2222-12-31', '13:31:00', 15, 'test', 'angefragt', NULL, NULL, 0, '2026-01-31 20:26:48', 36),
(36, 8, '2222-02-03', '14:22:00', NULL, NULL, 'angefragt', NULL, NULL, 0, '2026-02-01 10:07:52', 36),
(37, NULL, '2222-02-03', '14:37:00', NULL, NULL, 'frei', NULL, NULL, 0, '2026-02-01 10:07:52', 36);

-- --------------------------------------------------------

--
-- Table structure for table `praxen`
--

CREATE TABLE `praxen` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `beschreibung` text DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telefon` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `bild_url` varchar(255) DEFAULT 'https://via.placeholder.com/400x300?text=Arztpraxis',
  `spezialgebiet` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `plz` varchar(10) DEFAULT NULL,
  `stadt` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `kategorie` varchar(100) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `accepting_bookings` tinyint(1) DEFAULT 1 COMMENT 'Ob die Praxis derzeit Buchungen akzeptiert (1=Ja, 0=Nein)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `praxen`
--

INSERT INTO `praxen` (`id`, `name`, `beschreibung`, `adresse`, `telefon`, `email`, `bild_url`, `spezialgebiet`, `created_at`, `plz`, `stadt`, `latitude`, `longitude`, `kategorie`, `owner_id`, `accepting_bookings`) VALUES
(1, 'Praxis Dr. Rhomberg', 'Hausarztpraxis mit langjähriger Erfahrung. Wir bieten umfassende medizinische Betreuung für die ganze Familie.', 'Hauptstraße 123, 12345 Berlin', '+49 30 12345678', 'info@praxis-mueller.de', 'uploads/praxen/praxis_1_1769888010.png', 'Allgemeinmedizin', '2026-01-28 13:17:28', '12345', 'Berlin', NULL, NULL, 'Allgemeinmedizin', 7, 1),
(2, 'Orthopädie Zentrum Berfin', 'Spezialisiert auf Erkrankungen des Bewegungsapparats, Sportmedizin und Rehabilitation.', 'Peterstraße 45', '+49 30 98765432', 'kontakt@ortho-schmidt.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Orthopädie', '2026-01-28 13:17:28', '12345', 'Dornbirn', NULL, NULL, '', 9, 1),
(3, 'Kinderarztpraxis Wagner', 'Einfühlsame Kinderbetreuung von der Geburt bis zum Jugendalter. Modern ausgestattete Praxis.', 'Parkweg 78, 12345 Berlin', '+49 30 55566677', 'praxis@kinderarzt-wagner.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Pädiatrie', '2026-01-28 13:17:28', '12345', 'Berlin', NULL, NULL, 'Frauen, Männer & Kinder', 10, 1),
(4, 'Praxis Dr. Internist', 'Fachärzte für Innere Medizin mit Schwerpunkt Vorsorgeuntersuchungen und chronische Erkrankungen.', 'Berliner Str. 89, 12345 Berlin', '+49 30 11122233', 'info@internist-berlin.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Innere Medizin', '2026-01-28 16:37:36', '12345', 'Berlin', 52.52000000, 13.40500000, 'Allgemeine & hausärztliche Versorgung', NULL, 1),
(5, 'Kardiologische Praxis Dr. Herz', 'Spezialisiert auf Herzerkrankungen, EKG, Ultraschall und Belastungstests.', 'Herzweg 12, 12345 Berlin', '+49 30 22233344', 'kontakt@kardio-herz.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Kardiologie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.52100000, 13.41000000, 'Organe & innere Erkrankungen', NULL, 1),
(6, 'Gastroenterologie Zentrum', 'Magen-Darm-Erkrankungen, Endoskopie und moderne Diagnostik.', 'Bauchstraße 5, 12345 Berlin', '+49 30 33344455', 'info@gastro-zentrum.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Gastroenterologie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.51800000, 13.40000000, 'Organe & innere Erkrankungen', NULL, 1),
(7, 'Pneumologie Praxis Dr. Lunge', 'Lungenerkrankungen, Allergietests und Lungenfunktionsprüfung.', 'Atemweg 21, 12345 Berlin', '+49 30 44455566', 'praxis@pneumo-lunge.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Pneumologie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.52300000, 13.41200000, 'Organe & innere Erkrankungen', NULL, 1),
(8, 'Nephrologisches Zentrum', 'Nierenerkrankungen, Dialyse und Blutdrucktherapie.', 'Nierenplatz 8, 12345 Berlin', '+49 30 55566677', 'info@nephro-zentrum.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Nephrologie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.51900000, 13.40800000, 'Organe & innere Erkrankungen', NULL, 1),
(9, 'Endokrinologie Dr. Hormone', 'Hormonelle Störungen, Diabetes und Schilddrüsenerkrankungen.', 'Hormonstraße 15, 12345 Berlin', '+49 30 66677788', 'kontakt@endokrino.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Endokrinologie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.52200000, 13.40300000, 'Organe & innere Erkrankungen', NULL, 1),
(10, 'Chirurgische Praxis Berlin', 'Allgemeinchirurgie mit ambulanten Operationen und Nachsorge.', 'Operationsweg 33, 12345 Berlin', '+49 30 77788899', 'info@chirurgie-berlin.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Allgemeinchirurgie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.51700000, 13.39900000, 'Chirurgische Fächer', NULL, 1),
(11, 'Unfallchirurgie & Sportmedizin', 'Behandlung von Sportverletzungen, Brüchen und Gelenkerkrankungen.', 'Sportplatz 44, 12345 Berlin', '+49 30 88899900', 'praxis@unfall-sport.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Unfallchirurgie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.52400000, 13.41400000, 'Chirurgische Fächer', NULL, 1),
(12, 'Neurochirurgische Praxis', 'Spezialisiert auf Wirbelsäulen- und Nervenchirurgie.', 'Neuroweg 17, 12345 Berlin', '+49 30 99900011', 'info@neurochirurgie.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Neurochirurgie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.51600000, 13.39700000, 'Chirurgische Fächer', NULL, 1),
(13, 'Plastische Chirurgie Dr. Schön', 'Ästhetische und rekonstruktive Chirurgie, Narbenbehandlung.', 'Schönheitsallee 9, 12345 Berlin', '+49 30 00011122', 'kontakt@plastik-schoen.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Plastische Chirurgie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.52500000, 13.41600000, 'Chirurgische Fächer', NULL, 1),
(14, 'Neurologische Praxis Dr. Neuro', 'Behandlung von Nervenkrankheiten, Kopfschmerzen und neurologischen Störungen.', 'Nervenstraße 27, 12345 Berlin', '+49 30 11122233', 'info@neuro-praxis.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Neurologie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.51500000, 13.39600000, 'Kopf, Sinne & Nerven', NULL, 1),
(15, 'Psychiatrie & Psychotherapie Zentrum', 'Psychische Erkrankungen, Therapie und Beratung.', 'Seelenweg 13, 12345 Berlin', '+49 30 22233344', 'kontakt@psychiatrie-zentrum.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Psychiatrie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.52600000, 13.41800000, 'Kopf, Sinne & Nerven', NULL, 1),
(16, 'Augenarztpraxis Dr. Sicht', 'Augenuntersuchungen, Sehtest und Behandlung von Augenkrankheiten.', 'Sehstraße 31, 12345 Berlin', '+49 30 33344455', 'praxis@augen-sicht.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Augenheilkunde', '2026-01-28 16:37:36', '12345', 'Berlin', 52.51400000, 13.39400000, 'Kopf, Sinne & Nerven', NULL, 1),
(17, 'HNO-Praxis Dr. Ohr', 'Hals-Nasen-Ohren-Heilkunde, Hörtests und Allergiebehandlung.', 'Hörweg 19, 12345 Berlin', '+49 30 44455566', 'info@hno-ohr.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'HNO', '2026-01-28 16:37:36', '12345', 'Berlin', 52.52700000, 13.42000000, 'Kopf, Sinne & Nerven', NULL, 1),
(18, 'Frauenarztpraxis Dr. Gyno', 'Gynäkologie, Schwangerschaftsvorsorge und Krebsfrüherkennung.', 'Frauenplatz 24, 12345 Berlin', '+49 30 55566677', 'kontakt@gyno-praxis.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Gynäkologie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.51300000, 13.39200000, 'Frauen, Männer & Kinder', NULL, 1),
(19, 'Urologische Praxis', 'Männer- und Frauengesundheit, Nieren- und Blasenerkrankungen.', 'Urologiestraße 16, 12345 Berlin', '+49 30 66677788', 'info@uro-praxis.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Urologie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.52800000, 13.42200000, 'Frauen, Männer & Kinder', NULL, 1),
(20, 'Kinder- und Jugendpsychiatrie', 'Psychische Gesundheit für Kinder und Jugendliche, Therapie und Beratung.', 'Jugendweg 11, 12345 Berlin', '+49 30 77788899', 'praxis@kjp-berlin.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Kinder- und Jugendpsychiatrie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.51200000, 13.39000000, 'Frauen, Männer & Kinder', NULL, 1),
(21, 'Dermatologie Praxis Dr. Haut', 'Hauterkrankungen, Akne, Allergien und ästhetische Dermatologie.', 'Hautweg 28, 12345 Berlin', '+49 30 88899900', 'info@derma-haut.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Dermatologie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.52900000, 13.42400000, 'Haut, Allergien & Immunsystem', NULL, 1),
(22, 'Allergologie Zentrum', 'Allergietests, Desensibilisierung und Behandlung allergischer Erkrankungen.', 'Allergieplatz 7, 12345 Berlin', '+49 30 99900011', 'kontakt@allergo-zentrum.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Allergologie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.51100000, 13.38800000, 'Haut, Allergien & Immunsystem', NULL, 1),
(23, 'Onkologische Praxis', 'Krebsbehandlung, Chemotherapie und onkologische Nachsorge.', 'Hoffnungsstraße 42, 12345 Berlin', '+49 30 00011122', 'info@onko-praxis.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Onkologie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.53000000, 13.42600000, 'Krebs & schwere Erkrankungen', NULL, 1),
(24, 'Palliativmedizin Dr. Care', 'Lindernde Behandlung bei schweren Erkrankungen, Schmerztherapie.', 'Pflegeweg 14, 12345 Berlin', '+49 30 11122233', 'kontakt@palliativ-care.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Palliativmedizin', '2026-01-28 16:37:36', '12345', 'Berlin', 52.51000000, 13.38600000, 'Krebs & schwere Erkrankungen', NULL, 1),
(25, 'Radiologisches Institut', 'Röntgen, CT, MRT und moderne Bildgebung für präzise Diagnosen.', 'Röntgenstraße 50, 12345 Berlin', '+49 30 22233344', 'info@radio-institut.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Radiologie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.53100000, 13.42800000, 'Diagnostik & Technik', NULL, 1),
(26, 'Labor Dr. Test', 'Umfassende Labordiagnostik, Blutuntersuchungen und Analysen.', 'Laborweg 6, 12345 Berlin', '+49 30 33344455', 'labor@dr-test.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Laboratoriumsmedizin', '2026-01-28 16:37:36', '12345', 'Berlin', 52.50900000, 13.38400000, 'Diagnostik & Technik', NULL, 1),
(27, 'Arbeitsmedizin Zentrum', 'Betriebsärztliche Versorgung, Vorsorgeuntersuchungen für Unternehmen.', 'Arbeitsplatz 38, 12345 Berlin', '+49 30 44455566', 'info@arbeitsmedizin.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Arbeitsmedizin', '2026-01-28 16:37:36', '12345', 'Berlin', 52.53200000, 13.43000000, 'Prävention & spezielle Versorgung', NULL, 1),
(28, 'Sportmedizin & Fitness', 'Sportärztliche Untersuchungen, Leistungsdiagnostik und Ernährungsberatung.', 'Sportweg 25, 12345 Berlin', '+49 30 55566677', 'kontakt@sportmedizin.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Sportmedizin', '2026-01-28 16:37:36', '12345', 'Berlin', 52.50800000, 13.38200000, 'Prävention & spezielle Versorgung', NULL, 1),
(29, 'Schmerztherapie Praxis', 'Chronische Schmerzen, multimodale Schmerztherapie und Akupunktur.', 'Schmerzfrei 9, 12345 Berlin', '+49 30 66677788', 'praxis@schmerztherapie.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Schmerzmedizin', '2026-01-28 16:37:36', '12345', 'Berlin', 52.53300000, 13.43200000, 'Prävention & spezielle Versorgung', NULL, 1),
(30, 'Geriatrie Dr. Senior', 'Altersmedizin, Gedächtnissprechstunde und ganzheitliche Betreuung älterer Patienten.', 'Seniorenweg 18, 12345 Berlin', '+49 30 77788899', 'info@geriatrie-senior.de', 'https://via.placeholder.com/400x300?text=Arztpraxis', 'Geriatrie', '2026-01-28 16:37:36', '12345', 'Berlin', 52.50700000, 13.38000000, 'Prävention & spezielle Versorgung', NULL, 1),
(31, 'Hey', '', '', '', '', '', '', '2026-01-31 19:37:14', '', '', NULL, NULL, '', 7, 1),
(34, 'test', '', 'asdasd', '', 'asdasdasd@gmail.com', '', 'Innere Medizin', '2026-01-31 19:47:28', '342', 'ber', NULL, NULL, '', 7, 1),
(36, 'Oliver Rhomberg', 'hey', 'Am Eisweiher 2B', '+4369981981525', 'oliver.rhomberg@gmail.com', 'uploads/praxen/praxis_7_1769889700_697e5fa46544e.png', 'Orthopädie', '2026-01-31 20:01:40', '6850', 'Dornbirn', NULL, NULL, '', 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('arzt','patient') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `praxis_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `praxis_id`) VALUES
(7, 'Dr Albrecht', 'arzt@termin2praxis.de', 'password123', 'arzt', '2026-01-08 05:33:55', 1),
(8, 'Max Mustermann', 'patient@termin2praxis.de', 'password123', 'patient', '2026-01-08 05:33:55', NULL),
(9, 'Dr. Michael Schmidt', 'dr.schmidt@ortho.de', 'password123', 'arzt', '2026-01-29 19:47:18', NULL),
(10, 'Dr. Anna Wagner', 'dr.wagner@kinder.de', 'password123', 'arzt', '2026-01-29 19:47:18', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `confirmed_by` (`confirmed_by`),
  ADD KEY `praxis_id` (`praxis_id`);

--
-- Indexes for table `praxen`
--
ALTER TABLE `praxen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `praxis_id` (`praxis_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `praxen`
--
ALTER TABLE `praxen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`praxis_id`) REFERENCES `praxen` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `praxen`
--
ALTER TABLE `praxen`
  ADD CONSTRAINT `praxen_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`praxis_id`) REFERENCES `praxen` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
