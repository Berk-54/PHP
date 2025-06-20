-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 20 jun 2025 om 18:16
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
-- Database: `calculator_db`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `calculations`
--

CREATE TABLE `calculations` (
  `id` int(11) NOT NULL,
  `expression` varchar(255) NOT NULL COMMENT 'De wiskundige expressie (bijv: 2+3)',
  `result` decimal(15,6) NOT NULL COMMENT 'Het resultaat van de berekening',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Wanneer de berekening is gemaakt',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Laatst bijgewerkt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `calculations`
--

INSERT INTO `calculations` (`id`, `expression`, `result`, `created_at`, `updated_at`) VALUES
(1, '2+3', 5.000000, '2025-06-20 12:00:08', '2025-06-20 12:00:08'),
(2, '10-4', 6.000000, '2025-06-20 12:00:08', '2025-06-20 12:00:08'),
(3, '3*7', 21.000000, '2025-06-20 12:00:08', '2025-06-20 12:00:08'),
(4, '15/3', 5.000000, '2025-06-20 12:00:08', '2025-06-20 12:00:08'),
(5, '2^3', 8.000000, '2025-06-20 12:00:08', '2025-06-20 12:00:08'),
(6, '√(16)', 4.000000, '2025-06-20 12:00:08', '2025-06-20 12:00:08'),
(7, '10Mod3', 1.000000, '2025-06-20 12:00:08', '2025-06-20 12:00:08'),
(8, '(5+3)*2', 16.000000, '2025-06-20 12:00:08', '2025-06-20 12:00:08'),
(9, '√(25)+2^3', 13.000000, '2025-06-20 12:00:08', '2025-06-20 12:00:08'),
(10, '(10+5)*2-√(16)', 26.000000, '2025-06-20 12:00:08', '2025-06-20 12:00:08');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `calculation_categories`
--

CREATE TABLE `calculation_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `saved_formulas`
--

CREATE TABLE `saved_formulas` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `formula` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `calculations`
--
ALTER TABLE `calculations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_result` (`result`);

--
-- Indexen voor tabel `calculation_categories`
--
ALTER TABLE `calculation_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `saved_formulas`
--
ALTER TABLE `saved_formulas`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `calculations`
--
ALTER TABLE `calculations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT voor een tabel `calculation_categories`
--
ALTER TABLE `calculation_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `saved_formulas`
--
ALTER TABLE `saved_formulas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
