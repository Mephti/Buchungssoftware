-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 02. Feb 2026 um 21:50
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `plauersee`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `boote`
--

CREATE TABLE `boote` (
  `boid` int(11) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `typ` varchar(40) DEFAULT NULL,
  `plaetze` int(11) UNSIGNED NOT NULL DEFAULT 2,
  `status` enum('verfuegbar','gesperrt','wartung','unterwegs') NOT NULL DEFAULT 'verfuegbar',
  `kosten_pt` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `boote`
--

INSERT INTO `boote` (`boid`, `name`, `typ`, `plaetze`, `status`) VALUES
(1, 'Seerose 1', 'Ruderboot', 2, 'verfuegbar'),
(2, 'Seerose 2', 'Tretboot', 4, 'verfuegbar'),
(3, 'Hecht', 'Motorboot', 5, 'gesperrt'),
(4, 'Barsch', 'Ruderboot', 2, 'wartung'),
(5, 'Karpfen', 'Tretboot', 4, 'verfuegbar');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `boot_buchungen`
--

CREATE TABLE `boot_buchungen` (
  `bbid` int(11) UNSIGNED NOT NULL,
  `boid` int(11) UNSIGNED NOT NULL,
  `kid` int(11) UNSIGNED NOT NULL,
  `von` date NOT NULL,
  `bis` date NOT NULL,
  `status` enum('aktiv','storniert') NOT NULL DEFAULT 'aktiv',
  `created_at` datetime DEFAULT NULL,
  `group_token` varchar(36) DEFAULT NULL,
  `kosten` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kunden`
--

CREATE TABLE `kunden` (
  `kid` int(10) UNSIGNED NOT NULL,
  `nachname` varchar(100) NOT NULL,
  `vorname` varchar(100) NOT NULL,
  `geburtsdatum` date NOT NULL,
  `geschlecht` enum('m','w','d') DEFAULT NULL,
  `passwort` varchar(255) NOT NULL,
  `strasse` varchar(150) NOT NULL,
  `hausnr` varchar(10) NOT NULL,
  `plz` varchar(10) NOT NULL,
  `ort` varchar(150) NOT NULL,
  `telefon` varchar(30) DEFAULT NULL,
  `email` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `kunden`
--

INSERT INTO `kunden` (`kid`, `nachname`, `vorname`, `geburtsdatum`, `geschlecht`, `passwort`, `strasse`, `hausnr`, `plz`, `ort`, `telefon`, `email`) VALUES
(12, 'Mustermann', 'Max', '1000-01-01', 'm', '$2y$10$FpTte5Rf4b11270sJXxvzO2uiYPvr7brZT3uAfENdvbUwfW0sC0we', 'Testalle', '00', '11111', 'teststadt', '00000 111111', 'test@test.de');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `liegeplaetze`
--

CREATE TABLE `liegeplaetze` (
  `lid` int(11) UNSIGNED NOT NULL,
  `anleger` varchar(50) NOT NULL,
  `nummer` int(11) UNSIGNED NOT NULL,
  `status` enum('verfuegbar','gesperrt','vermietet','belegt') NOT NULL DEFAULT 'verfuegbar',
  `kosten_pt` int(11) UNSIGNED NOT NULL DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `liegeplaetze`
--

INSERT INTO `liegeplaetze` (`lid`, `anleger`, `nummer`, `status`, `kosten_pt`) VALUES
(1, 'A', 1, 'verfuegbar', 100),
(2, 'A', 2, 'verfuegbar', 100),
(3, 'A', 3, 'verfuegbar', 100),
(4, 'A', 4, 'verfuegbar', 100),
(5, 'A', 5, 'verfuegbar', 100),
(6, 'A', 6, 'verfuegbar', 100),
(7, 'A', 7, 'verfuegbar', 100),
(8, 'A', 8, 'verfuegbar', 100),
(9, 'A', 9, 'verfuegbar', 100),
(10, 'A', 10, 'verfuegbar', 100),
(11, 'A', 11, 'verfuegbar', 100),
(12, 'A', 12, 'verfuegbar', 100),
(13, 'A', 13, 'verfuegbar', 100),
(14, 'A', 14, 'verfuegbar', 100),
(15, 'A', 15, 'verfuegbar', 100),
(16, 'A', 16, 'verfuegbar', 100),
(17, 'A', 17, 'verfuegbar', 100),
(18, 'B', 1, 'verfuegbar', 100),
(19, 'B', 2, 'verfuegbar', 100),
(20, 'B', 3, 'verfuegbar', 100),
(21, 'B', 4, 'verfuegbar', 100),
(22, 'B', 5, 'verfuegbar', 100),
(23, 'B', 6, 'verfuegbar', 100),
(24, 'B', 7, 'verfuegbar', 100),
(25, 'B', 8, 'verfuegbar', 100),
(26, 'B', 9, 'verfuegbar', 100),
(27, 'B', 10, 'verfuegbar', 100),
(28, 'B', 11, 'verfuegbar', 100),
(29, 'B', 12, 'verfuegbar', 100),
(30, 'B', 13, 'verfuegbar', 100),
(31, 'B', 14, 'verfuegbar', 100),
(32, 'B', 15, 'verfuegbar', 100),
(33, 'B', 16, 'verfuegbar', 100),
(34, 'B', 17, 'verfuegbar', 100),
(35, 'B', 18, 'verfuegbar', 100);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `liegeplatz_buchungen`
--

CREATE TABLE `liegeplatz_buchungen` (
  `bid` int(11) UNSIGNED NOT NULL,
  `lid` int(11) UNSIGNED NOT NULL,
  `kid` int(11) UNSIGNED NOT NULL,
  `von` date NOT NULL,
  `bis` date NOT NULL,
  `status` enum('aktiv','storniert') NOT NULL DEFAULT 'aktiv',
  `created_at` datetime DEFAULT NULL,
  `group_token` varchar(36) DEFAULT NULL,
  `kosten` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `liegeplatz_buchungen`
--

INSERT INTO `liegeplatz_buchungen` (`bid`, `lid`, `kid`, `von`, `bis`, `status`, `created_at`, `group_token`) VALUES
(1, 1, 12, '2020-01-01', '2020-01-10', 'aktiv', '2026-02-02 20:01:33', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(6, '2026-01-12-000001', 'App\\Database\\Migrations\\CreateLiegeplaetze', 'default', 'App', 1769516376, 1),
(7, '2026-01-13-000002', 'App\\Database\\Migrations\\CreateLiegeplatzBuchungen', 'default', 'App', 1769516376, 1),
(8, '2026-01-13-000003', 'App\\Database\\Migrations\\CreateBoote', 'default', 'App', 1769516376, 1),
(9, '2026-01-13-000004', 'App\\Database\\Migrations\\CreateBootBuchungen', 'default', 'App', 1769516376, 1),
(10, '2026-01-27-000010', 'App\\Database\\Migrations\\AddGroupTokenToBookings', 'default', 'App', 1769516376, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mitarbeiter`
--

CREATE TABLE `mitarbeiter` (
  `mid` int(10) UNSIGNED NOT NULL,
  `vorname` varchar(100) NOT NULL,
  `nachname` varchar(100) NOT NULL,
  `geschlecht` enum('m','w','d') DEFAULT NULL,
  `passwort` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `mitarbeiter`
--

INSERT INTO `mitarbeiter` (`mid`, `vorname`, `nachname`, `geschlecht`, `passwort`, `email`) VALUES
(1, 'Admin', 'Admin', 'm', '$2y$10$pRp.2wonfm4r73.SLAxxS.dx3FiYKxYMaTvGpc37N32YKZE6i9HL2', 'admin@admin');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `boote`
--
ALTER TABLE `boote`
  ADD PRIMARY KEY (`boid`);

--
-- Indizes für die Tabelle `boot_buchungen`
--
ALTER TABLE `boot_buchungen`
  ADD PRIMARY KEY (`bbid`),
  ADD KEY `boid` (`boid`),
  ADD KEY `kid` (`kid`);

--
-- Indizes für die Tabelle `kunden`
--
ALTER TABLE `kunden`
  ADD PRIMARY KEY (`kid`),
  ADD UNIQUE KEY `uniq_kunden_email` (`email`),
  ADD KEY `passwort` (`passwort`,`email`);

--
-- Indizes für die Tabelle `liegeplaetze`
--
ALTER TABLE `liegeplaetze`
  ADD PRIMARY KEY (`lid`),
  ADD UNIQUE KEY `anleger_nummer` (`anleger`,`nummer`);

--
-- Indizes für die Tabelle `liegeplatz_buchungen`
--
ALTER TABLE `liegeplatz_buchungen`
  ADD PRIMARY KEY (`bid`),
  ADD KEY `lid` (`lid`),
  ADD KEY `kid` (`kid`);

--
-- Indizes für die Tabelle `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `mitarbeiter`
--
ALTER TABLE `mitarbeiter`
  ADD PRIMARY KEY (`mid`),
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `boote`
--
ALTER TABLE `boote`
  MODIFY `boid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `boot_buchungen`
--
ALTER TABLE `boot_buchungen`
  MODIFY `bbid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kunden`
--
ALTER TABLE `kunden`
  MODIFY `kid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `liegeplaetze`
--
ALTER TABLE `liegeplaetze`
  MODIFY `lid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT für Tabelle `liegeplatz_buchungen`
--
ALTER TABLE `liegeplatz_buchungen`
  MODIFY `bid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT für Tabelle `mitarbeiter`
--
ALTER TABLE `mitarbeiter`
  MODIFY `mid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `boot_buchungen`
--
ALTER TABLE `boot_buchungen`
  ADD CONSTRAINT `boot_buchungen_boid_foreign` FOREIGN KEY (`boid`) REFERENCES `boote` (`boid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `boot_buchungen_kid_foreign` FOREIGN KEY (`kid`) REFERENCES `kunden` (`kid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `liegeplatz_buchungen`
--
ALTER TABLE `liegeplatz_buchungen`
  ADD CONSTRAINT `liegeplatz_buchungen_kid_foreign` FOREIGN KEY (`kid`) REFERENCES `kunden` (`kid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `liegeplatz_buchungen_lid_foreign` FOREIGN KEY (`lid`) REFERENCES `liegeplaetze` (`lid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
