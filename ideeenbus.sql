-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 27 jun 2025 om 12:20
-- Serverversie: 10.4.32-MariaDB
-- PHP-versie: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ideeenbus`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `actie` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_adres` varchar(45) DEFAULT NULL,
  `datum` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `actie`, `details`, `ip_adres`, `datum`) VALUES
(1, 'Login', 'Admin ingelogd', '::1', '2025-06-27 10:11:17');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ideeen`
--

CREATE TABLE `ideeen` (
  `id` int(11) NOT NULL,
  `naam` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `titel` varchar(200) NOT NULL,
  `bericht` text NOT NULL,
  `datum` timestamp NOT NULL DEFAULT current_timestamp(),
  `upvotes` int(11) DEFAULT 0,
  `downvotes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `ideeen`
--

INSERT INTO `ideeen` (`id`, `naam`, `email`, `titel`, `bericht`, `datum`, `upvotes`, `downvotes`) VALUES
(1, 'Saied', 'Saied@tcr.nl', 'Groenere Schoolplein', 'Wat als we meer [b]bomen en planten[/b] toevoegen aan ons schoolplein? :) Dit zou [color=green]goed zijn voor het milieu[/color] en een [size=16]rustiger gevoel[/size] geven tijdens pauzes!', '2025-06-26 09:46:19', 8, 1),
(2, 'Ahmad', 'Ahmad.@tcr.nl\r\n', 'Digitale Suggestiebox', 'Een [i]digitale versie[/i] van onze fysieke suggestiebox zou [b]veel handiger[/b] zijn! :D Studenten kunnen dan vanuit huis ook ideeën delen. [color=blue]Toegankelijkheid[/color] is belangrijk <3', '2025-06-25 09:46:19', 12, 0),
(3, 'Tariq', 'Tariq@student.tcr.nl', 'Duurzame Kantine', 'Laten we [color=orange]lokale en biologische[/color] producten gebruiken in onze kantine! :) Minder plastic, meer [b]herbruikbare materialen[/b]. Het is tijd voor verandering! :o', '2025-06-24 09:46:19', 15, 2),
(4, 'Rojvan', 'Rojvan@tcr.nl', 'Studieruimtes 24/7', 'Kunnen we de [b]bibliotheek[/b] ook s avonds en weekenden openhouden? :) Vooral tijdens [color=red]tentamenperiodes[/color] zou dit [size=20]super handig[/size] zijn! :D', '2025-06-23 09:46:19', 6, 0),
(5, 'Furkan', 'Furkan@tcr.nl\r\n', 'E-sports Club', 'TCR heeft nog geen [i]officiële e-sports club[/i]! :o Gaming wordt steeds populairder en we zouden [color=blue]toernooien[/color] kunnen organiseren <3', '2025-06-22 09:46:19', 9, 1),
(6, 'Berke', 'Berke.@tcr.nl\r\n', 'Pauze tussen de blokuur lessen', 'Na een lange tijd zitten tijdens de les wordt je moe en is je focus laag in de les, Beste optie is dat we een kleine pauze van ongeveer 10 minuten houden om weer de energie op te pakken. :)', '2025-06-27 10:18:20', 0, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `stemmen`
--

CREATE TABLE `stemmen` (
  `id` int(11) NOT NULL,
  `idee_id` int(11) NOT NULL,
  `ip_adres` varchar(45) NOT NULL,
  `stem_type` enum('up','down') NOT NULL,
  `datum` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `ideeen`
--
ALTER TABLE `ideeen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_datum` (`datum`),
  ADD KEY `idx_upvotes` (`upvotes`);

--
-- Indexen voor tabel `stemmen`
--
ALTER TABLE `stemmen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_stem` (`idee_id`,`ip_adres`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT voor een tabel `ideeen`
--
ALTER TABLE `ideeen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT voor een tabel `stemmen`
--
ALTER TABLE `stemmen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `stemmen`
--
ALTER TABLE `stemmen`
  ADD CONSTRAINT `stemmen_ibfk_1` FOREIGN KEY (`idee_id`) REFERENCES `ideeen` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
