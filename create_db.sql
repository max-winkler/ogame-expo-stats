-- phpMyAdmin SQL Dump
-- version 4.6.6deb4+deb9u2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 26. Sep 2025 um 09:58
-- Server-Version: 10.1.48-MariaDB-0+deb9u2
-- PHP-Version: 7.0.33-0+deb9u12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `ExpoStats`
--

-- --------------------------------------------------------

--
-- Stellvertreter-Struktur des Views `amount_explorer`
-- (Siehe unten für die tatsächliche Ansicht)
--
CREATE TABLE `amount_explorer` (
`SUM(Amount)` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Expeditions`
--

CREATE TABLE `Expeditions` (
  `Id` int(11) NOT NULL,
  `User` int(11) NOT NULL,
  `Time` datetime DEFAULT NULL,
  `Type` enum('F','R','H','P','A','N','D','V','I') COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `FleetFound`
--

CREATE TABLE `FleetFound` (
  `Expedition` int(11) NOT NULL,
  `Ship` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Highscore`
--

CREATE TABLE `Highscore` (
  `Type` set('metall','crystal','deuterium','darkmatter','fleet') COLLATE utf8mb4_unicode_ci NOT NULL,
  `Rank` int(11) NOT NULL,
  `Expedition` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ResFound`
--

CREATE TABLE `ResFound` (
  `Expedition` int(11) NOT NULL,
  `Type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Resources`
--

CREATE TABLE `Resources` (
  `Type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Ships`
--

CREATE TABLE `Ships` (
  `Id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CostMetall` int(11) NOT NULL,
  `CostCrystal` int(11) NOT NULL,
  `CostDeuterium` int(11) NOT NULL,
  `Speed` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `Ships`
--

INSERT INTO `Ships` (`Id`, `Name`, `CostMetall`, `CostCrystal`, `CostDeuterium`, `Speed`) VALUES
('battleship', 'Schlachtschiff', 45000, 15000, 0, 10000),
('bomber', 'Bomber', 50000, 25000, 15000, 5000),
('colonyShip', 'Kolonieschiff', 10000, 20000, 10000, 2500),
('cruiser', 'Kreuzer', 20000, 7000, 2000, 15000),
('deathstar', 'Todesstern', 5000000, 4000000, 1000000, 100),
('destroyer', 'Zerstörer', 60000, 50000, 15000, 5000),
('espionageProbe', 'Spionagesonde', 0, 1000, 0, 100000000),
('explorer', 'Pathfinder', 8000, 15000, 8000, 10000),
('figherHeavy', 'Schwerer Jäger', 6000, 4000, 0, 10000),
('fighterLight', 'Leichter Jäger', 3000, 1000, 0, 12500),
('interceptor', 'Schlachtkreuzer', 30000, 40000, 15000, 10000),
('reaper', 'Reaper', 85000, 55000, 20000, 7000),
('recycler', 'Recycler', 10000, 6000, 2000, 2000),
('transporterLarge', 'Großer Transporter', 6000, 6000, 0, 12500),
('transporterSmall', 'Kleiner Transporter', 2000, 2000, 0, 10000);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `User`
--

CREATE TABLE `User` (
  `Id` int(10) UNSIGNED NOT NULL,
  `Name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur des Views `amount_explorer`
--
DROP TABLE IF EXISTS `amount_explorer`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `amount_explorer`  AS  select sum(`FleetFound`.`Amount`) AS `SUM(Amount)` from `FleetFound` where (`FleetFound`.`Ship` = 'explorer') ;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `Expeditions`
--
ALTER TABLE `Expeditions`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `User` (`User`,`Time`),
  ADD KEY `idx_expeditions_date` (`Time`),
  ADD KEY `idx_expeditions_user` (`User`),
  ADD KEY `Type` (`Type`);

--
-- Indizes für die Tabelle `FleetFound`
--
ALTER TABLE `FleetFound`
  ADD PRIMARY KEY (`Expedition`,`Ship`) USING BTREE,
  ADD KEY `Ship` (`Ship`);

--
-- Indizes für die Tabelle `Highscore`
--
ALTER TABLE `Highscore`
  ADD PRIMARY KEY (`Rank`,`Type`);

--
-- Indizes für die Tabelle `ResFound`
--
ALTER TABLE `ResFound`
  ADD PRIMARY KEY (`Expedition`) USING BTREE,
  ADD KEY `idx_resfound_type` (`Type`),
  ADD KEY `idx_resfound_expedition_type` (`Expedition`,`Type`);

--
-- Indizes für die Tabelle `Resources`
--
ALTER TABLE `Resources`
  ADD PRIMARY KEY (`Type`) USING BTREE,
  ADD UNIQUE KEY `Type` (`Type`);

--
-- Indizes für die Tabelle `Ships`
--
ALTER TABLE `Ships`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Id` (`Id`);

--
-- Indizes für die Tabelle `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Name` (`Name`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `Expeditions`
--
ALTER TABLE `Expeditions`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2332760;
--
-- AUTO_INCREMENT für Tabelle `User`
--
ALTER TABLE `User`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `FleetFound`
--
ALTER TABLE `FleetFound`
  ADD CONSTRAINT `Expedition_to_Fleet` FOREIGN KEY (`Expedition`) REFERENCES `Expeditions` (`Id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `ResFound`
--
ALTER TABLE `ResFound`
  ADD CONSTRAINT `Expedition_to_Res` FOREIGN KEY (`Expedition`) REFERENCES `Expeditions` (`Id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
