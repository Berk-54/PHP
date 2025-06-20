-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 20 jun 2025 om 10:15
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
-- Database: `statistiekensysteem`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bezoekers`
--

CREATE TABLE `bezoekers` (
  `id` int(11) NOT NULL,
  `land` varchar(100) NOT NULL,
  `ip_adres` varchar(45) NOT NULL,
  `provider` varchar(100) NOT NULL,
  `browser` varchar(100) NOT NULL,
  `datum_tijd` datetime NOT NULL,
  `referer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `bezoekers`
--

INSERT INTO `bezoekers` (`id`, `land`, `ip_adres`, `provider`, `browser`, `datum_tijd`, `referer`) VALUES
(1, 'Nederland', '192.168.1.100', 'KPN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-15 10:30:00', 'https://google.com'),
(2, 'Belgie', '192.168.1.101', 'Proximus', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', '2024-01-15 14:20:00', 'https://facebook.com'),
(3, 'Duitsland', '192.168.1.102', 'Deutsche Telekom', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101', '2024-01-16 09:15:00', 'https://twitter.com'),
(4, 'Frankrijk', '192.168.1.103', 'Orange', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15', '2024-01-16 16:45:00', 'https://instagram.com'),
(5, 'Spanje', '192.168.1.104', 'Movistar', 'Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0', '2024-01-17 11:30:00', 'https://youtube.com'),
(6, 'Italie', '192.168.1.105', 'TIM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-17 13:20:00', 'https://linkedin.com'),
(7, 'Nederland', '192.168.1.106', 'Ziggo', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15', '2024-01-18 08:10:00', 'https://reddit.com'),
(8, 'Belgie', '192.168.1.107', 'Telenet', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101', '2024-01-18 19:30:00', 'https://google.com'),
(9, 'Duitsland', '192.168.1.108', 'Vodafone', 'Mozilla/5.0 (iPad; CPU OS 15_0 like Mac OS X) AppleWebKit/605.1.15', '2024-01-19 12:15:00', 'https://bing.com'),
(10, 'Frankrijk', '192.168.1.109', 'Free', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36', '2024-01-19 15:45:00', 'https://duckduckgo.com'),
(11, 'Nederland', '192.168.1.110', 'T-Mobile', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-02-01 10:00:00', 'https://google.com'),
(12, 'Belgie', '192.168.1.111', 'BASE', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', '2024-02-01 14:30:00', 'https://facebook.com'),
(13, 'Duitsland', '192.168.1.112', 'O2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101', '2024-02-02 09:20:00', 'https://twitter.com'),
(14, 'Frankrijk', '192.168.1.113', 'SFR', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15', '2024-02-02 16:40:00', 'https://instagram.com'),
(15, 'Spanje', '192.168.1.114', 'Vodafone', 'Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0', '2024-02-03 11:25:00', 'https://youtube.com'),
(16, 'Italie', '192.168.1.115', 'Vodafone', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-02-03 13:10:00', 'https://linkedin.com'),
(17, 'Nederland', '192.168.1.116', 'KPN', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15', '2024-02-04 08:05:00', 'https://reddit.com'),
(18, 'Belgie', '192.168.1.117', 'Proximus', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101', '2024-02-04 19:20:00', 'https://google.com'),
(19, 'Duitsland', '192.168.1.118', 'Deutsche Telekom', 'Mozilla/5.0 (iPad; CPU OS 15_0 like Mac OS X) AppleWebKit/605.1.15', '2024-02-05 12:30:00', 'https://bing.com'),
(20, 'Frankrijk', '192.168.1.119', 'Orange', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36', '2024-02-05 15:50:00', 'https://duckduckgo.com'),
(21, 'Nederland', '192.168.1.120', 'Ziggo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-03-01 10:15:00', 'https://google.com'),
(22, 'Belgie', '192.168.1.121', 'Telenet', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', '2024-03-01 14:25:00', 'https://facebook.com'),
(23, 'Duitsland', '192.168.1.122', 'Vodafone', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101', '2024-03-02 09:35:00', 'https://twitter.com'),
(24, 'Frankrijk', '192.168.1.123', 'Free', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15', '2024-03-02 16:55:00', 'https://instagram.com'),
(25, 'Spanje', '192.168.1.124', 'Movistar', 'Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0', '2024-03-03 11:40:00', 'https://youtube.com'),
(26, 'Italie', '192.168.1.125', 'TIM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-03-03 13:15:00', 'https://linkedin.com'),
(27, 'Nederland', '192.168.1.126', 'T-Mobile', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15', '2024-03-04 08:20:00', 'https://reddit.com'),
(28, 'Belgie', '192.168.1.127', 'BASE', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101', '2024-03-04 19:35:00', 'https://google.com'),
(29, 'Duitsland', '192.168.1.128', 'O2', 'Mozilla/5.0 (iPad; CPU OS 15_0 like Mac OS X) AppleWebKit/605.1.15', '2024-03-05 12:45:00', 'https://bing.com'),
(30, 'Frankrijk', '192.168.1.129', 'SFR', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36', '2024-03-05 15:25:00', 'https://duckduckgo.com'),
(31, 'Nederland', '192.168.1.130', 'KPN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-04-10 11:00:00', 'https://google.com'),
(32, 'Belgie', '192.168.1.131', 'Proximus', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', '2024-04-10 15:30:00', 'https://facebook.com'),
(33, 'Duitsland', '192.168.1.132', 'Deutsche Telekom', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101', '2024-04-11 10:20:00', 'https://twitter.com'),
(34, 'Frankrijk', '192.168.1.133', 'Orange', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15', '2024-04-11 17:40:00', 'https://instagram.com'),
(35, 'Spanje', '192.168.1.134', 'Vodafone', 'Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0', '2024-04-12 12:25:00', 'https://youtube.com'),
(36, 'Italie', '192.168.1.135', 'Vodafone', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-04-12 14:10:00', 'https://linkedin.com'),
(37, 'Nederland', '192.168.1.136', 'Ziggo', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15', '2024-04-13 09:05:00', 'https://reddit.com'),
(38, 'Belgie', '192.168.1.137', 'Telenet', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101', '2024-04-13 20:20:00', 'https://google.com'),
(39, 'Duitsland', '192.168.1.138', 'Vodafone', 'Mozilla/5.0 (iPad; CPU OS 15_0 like Mac OS X) AppleWebKit/605.1.15', '2024-04-14 13:30:00', 'https://bing.com'),
(40, 'Frankrijk', '192.168.1.139', 'Free', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36', '2024-04-14 16:50:00', 'https://duckduckgo.com'),
(41, 'Nederland', '192.168.1.140', 'T-Mobile', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-05-15 12:00:00', 'https://google.com'),
(42, 'Belgie', '192.168.1.141', 'BASE', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', '2024-05-15 16:30:00', 'https://facebook.com'),
(43, 'Duitsland', '192.168.1.142', 'O2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101', '2024-05-16 11:20:00', 'https://twitter.com'),
(44, 'Frankrijk', '192.168.1.143', 'SFR', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15', '2024-05-16 18:40:00', 'https://instagram.com'),
(45, 'Spanje', '192.168.1.144', 'Movistar', 'Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0', '2024-05-17 13:25:00', 'https://youtube.com'),
(46, 'Italie', '192.168.1.145', 'TIM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-05-17 15:10:00', 'https://linkedin.com'),
(47, 'Nederland', '192.168.1.146', 'KPN', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15', '2024-05-18 10:05:00', 'https://reddit.com'),
(48, 'Belgie', '192.168.1.147', 'Proximus', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101', '2024-05-18 21:20:00', 'https://google.com'),
(49, 'Duitsland', '192.168.1.148', 'Deutsche Telekom', 'Mozilla/5.0 (iPad; CPU OS 15_0 like Mac OS X) AppleWebKit/605.1.15', '2024-05-19 14:30:00', 'https://bing.com'),
(50, 'Frankrijk', '192.168.1.149', 'Orange', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36', '2024-05-19 17:50:00', 'https://duckduckgo.com'),
(51, 'Nederland', '192.168.1.150', 'Ziggo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-06-01 09:30:00', 'https://google.com'),
(52, 'Belgie', '192.168.1.151', 'Telenet', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', '2024-06-01 13:45:00', 'https://facebook.com'),
(53, 'Duitsland', '192.168.1.152', 'Vodafone', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101', '2024-06-02 10:15:00', 'https://twitter.com'),
(54, 'Frankrijk', '192.168.1.153', 'Free', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15', '2024-06-02 17:30:00', 'https://instagram.com'),
(55, 'Spanje', '192.168.1.154', 'Vodafone', 'Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0', '2024-06-03 12:20:00', 'https://youtube.com'),
(56, 'Italie', '192.168.1.155', 'Vodafone', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-06-03 14:45:00', 'https://linkedin.com'),
(57, 'Nederland', '192.168.1.156', 'T-Mobile', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15', '2024-06-04 08:50:00', 'https://reddit.com'),
(58, 'Belgie', '192.168.1.157', 'BASE', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101', '2024-06-04 19:15:00', 'https://google.com'),
(59, 'Duitsland', '192.168.1.158', 'O2', 'Mozilla/5.0 (iPad; CPU OS 15_0 like Mac OS X) AppleWebKit/605.1.15', '2024-06-05 13:25:00', 'https://bing.com'),
(60, 'Frankrijk', '192.168.1.159', 'SFR', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36', '2024-06-05 16:40:00', 'https://duckduckgo.com'),
(61, 'Noorwegen', '192.168.1.160', 'Telenor', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-20 10:30:00', 'https://google.com'),
(62, 'Zweden', '192.168.1.161', 'Telia', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', '2024-01-20 14:20:00', 'https://facebook.com'),
(63, 'Denemarken', '192.168.1.162', 'TDC', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101', '2024-01-21 09:15:00', 'https://twitter.com'),
(64, 'Finland', '192.168.1.163', 'Elisa', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15', '2024-01-21 16:45:00', 'https://instagram.com'),
(65, 'Oostenrijk', '192.168.1.164', 'A1 Telekom', 'Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0', '2024-01-22 11:30:00', 'https://youtube.com'),
(66, 'Zwitserland', '192.168.1.165', 'Swisscom', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2024-01-22 13:20:00', 'https://linkedin.com'),
(67, 'Polen', '192.168.1.166', 'Orange Polska', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15', '2024-01-23 08:10:00', 'https://reddit.com'),
(68, 'Tsjechie', '192.168.1.167', 'O2 Czech Republic', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101', '2024-01-23 19:30:00', 'https://google.com'),
(69, 'Hongarije', '192.168.1.168', 'Magyar Telekom', 'Mozilla/5.0 (iPad; CPU OS 15_0 like Mac OS X) AppleWebKit/605.1.15', '2024-01-24 12:15:00', 'https://bing.com'),
(70, 'Slovakije', '192.168.1.169', 'Slovak Telekom', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36', '2024-01-24 15:45:00', 'https://duckduckgo.com'),
(71, 'Nederland', '192.168.1.170', 'KPN', 'Chrome/91.0.4472.124', '2024-02-10 10:30:00', 'https://google.com'),
(72, 'Belgie', '192.168.1.171', 'Proximus', 'Safari/537.36', '2024-02-10 14:20:00', 'https://facebook.com'),
(73, 'Duitsland', '192.168.1.172', 'Deutsche Telekom', 'Firefox/89.0', '2024-02-11 09:15:00', 'https://twitter.com'),
(74, 'Frankrijk', '192.168.1.173', 'Orange', 'Chrome Mobile/91.0.4472.124', '2024-02-11 16:45:00', 'https://instagram.com'),
(75, 'Spanje', '192.168.1.174', 'Movistar', 'Samsung Browser/14.2', '2024-02-12 11:30:00', 'https://youtube.com'),
(76, 'Italie', '192.168.1.175', 'TIM', 'Edge/91.0.864.59', '2024-02-12 13:20:00', 'https://linkedin.com'),
(77, 'Portugal', '192.168.1.176', 'MEO', 'Opera/77.0.4054.277', '2024-02-13 08:10:00', 'https://reddit.com'),
(78, 'Griekenland', '192.168.1.177', 'Cosmote', 'Chrome/91.0.4472.124', '2024-02-13 19:30:00', 'https://google.com'),
(79, 'Bulgarije', '192.168.1.178', 'Vivacom', 'Firefox/89.0', '2024-02-14 12:15:00', 'https://bing.com'),
(80, 'Roemenie', '192.168.1.179', 'Orange Romania', 'Safari/537.36', '2024-02-14 15:45:00', 'https://duckduckgo.com'),
(81, 'Kroatie', '192.168.1.180', 'A1 Hrvatska', 'Chrome/91.0.4472.124', '2024-03-15 10:30:00', 'https://google.com'),
(82, 'Slovenie', '192.168.1.181', 'A1 Slovenija', 'Safari/537.36', '2024-03-15 14:20:00', 'https://facebook.com'),
(83, 'Servie', '192.168.1.182', 'Telekom Srbija', 'Firefox/89.0', '2024-03-16 09:15:00', 'https://twitter.com'),
(84, 'Bosnie', '192.168.1.183', 'BH Telecom', 'Chrome Mobile/91.0.4472.124', '2024-03-16 16:45:00', 'https://instagram.com'),
(85, 'Letland', '192.168.1.184', 'Lattelecom', 'Samsung Browser/14.2', '2024-03-17 11:30:00', 'https://youtube.com'),
(86, 'Litouwen', '192.168.1.185', 'Tele2', 'Edge/91.0.864.59', '2024-03-17 13:20:00', 'https://linkedin.com'),
(87, 'Estland', '192.168.1.186', 'Elisa Eesti', 'Opera/77.0.4054.277', '2024-03-18 08:10:00', 'https://reddit.com'),
(88, 'Cyprus', '192.168.1.187', 'Cytamobile-Vodafone', 'Chrome/91.0.4472.124', '2024-03-18 19:30:00', 'https://google.com'),
(89, 'Malta', '192.168.1.188', 'GO Mobile', 'Firefox/89.0', '2024-03-19 12:15:00', 'https://bing.com'),
(90, 'Luxembourg', '192.168.1.189', 'POST Luxembourg', 'Safari/537.36', '2024-03-19 15:45:00', 'https://duckduckgo.com'),
(91, 'Ierland', '192.168.1.190', 'Eir', 'Chrome/91.0.4472.124', '2024-04-20 10:30:00', 'https://google.com'),
(92, 'IJsland', '192.168.1.191', 'Siminn', 'Safari/537.36', '2024-04-20 14:20:00', 'https://facebook.com'),
(93, 'Liechtenstein', '192.168.1.192', 'Telecom Liechtenstein', 'Firefox/89.0', '2024-04-21 09:15:00', 'https://twitter.com'),
(94, 'Monaco', '192.168.1.193', 'Monaco Telecom', 'Chrome Mobile/91.0.4472.124', '2024-04-21 16:45:00', 'https://instagram.com'),
(95, 'Andorra', '192.168.1.194', 'Andorra Telecom', 'Samsung Browser/14.2', '2024-04-22 11:30:00', 'https://youtube.com'),
(96, 'San Marino', '192.168.1.195', 'San Marino Telecom', 'Edge/91.0.864.59', '2024-04-22 13:20:00', 'https://linkedin.com'),
(97, 'Vaticaanstad', '192.168.1.196', 'Vatican Telecom', 'Opera/77.0.4054.277', '2024-04-23 08:10:00', 'https://reddit.com'),
(98, 'Albanie', '192.168.1.197', 'Vodafone Albania', 'Chrome/91.0.4472.124', '2024-04-23 19:30:00', 'https://google.com'),
(99, 'Noord-Macedonie', '192.168.1.198', 'A1 Macedonia', 'Firefox/89.0', '2024-04-24 12:15:00', 'https://bing.com'),
(100, 'Montenegro', '192.168.1.199', 'Crnogorski Telekom', 'Safari/537.36', '2024-04-24 15:45:00', 'https://duckduckgo.com'),
(101, 'Rusland', '192.168.1.200', 'MTS', 'Chrome/91.0.4472.124', '2024-05-25 10:30:00', 'https://yandex.com'),
(102, 'Oekraine', '192.168.1.201', 'Kyivstar', 'Safari/537.36', '2024-05-25 14:20:00', 'https://facebook.com'),
(103, 'Wit-Rusland', '192.168.1.202', 'A1 Belarus', 'Firefox/89.0', '2024-05-26 09:15:00', 'https://twitter.com'),
(104, 'Moldavie', '192.168.1.203', 'Orange Moldova', 'Chrome Mobile/91.0.4472.124', '2024-05-26 16:45:00', 'https://instagram.com'),
(105, 'Georgie', '192.168.1.204', 'Magti', 'Samsung Browser/14.2', '2024-05-27 11:30:00', 'https://youtube.com'),
(106, 'Armenie', '192.168.1.205', 'Beeline Armenia', 'Edge/91.0.864.59', '2024-05-27 13:20:00', 'https://linkedin.com'),
(107, 'Azerbeijan', '192.168.1.206', 'Azercell', 'Opera/77.0.4054.277', '2024-05-28 08:10:00', 'https://reddit.com'),
(108, 'Kazachstan', '192.168.1.207', 'Kazakhtelecom', 'Chrome/91.0.4472.124', '2024-05-28 19:30:00', 'https://google.com'),
(109, 'Turkije', '192.168.1.208', 'Turkcell', 'Firefox/89.0', '2024-05-29 12:15:00', 'https://bing.com'),
(110, 'Cyprus', '192.168.1.209', 'Cyta', 'Safari/537.36', '2024-05-29 15:45:00', 'https://duckduckgo.com'),
(111, 'Noorwegen', '192.168.178.46', 'Vodafone', 'Google Chrome', '2025-06-18 08:36:54', 'http://localhost/'),
(112, 'Noorwegen', '192.168.178.46', 'Vodafone', 'Google Chrome', '2025-06-18 08:37:31', 'http://localhost/'),
(113, 'Noorwegen', '192.168.178.46', 'Vodafone', 'Google Chrome', '2025-06-18 08:37:44', 'http://localhost/Bezoekersgrafiek/'),
(114, 'Noorwegen', '192.168.178.46', 'Vodafone', 'Google Chrome', '2025-06-18 08:37:50', 'http://localhost/Bezoekersgrafiek/'),
(115, 'Noorwegen', '192.168.178.46', 'Vodafone', 'Google Chrome', '2025-06-18 08:37:50', 'http://localhost/Bezoekersgrafiek/'),
(116, 'Noorwegen', '192.168.178.46', 'Vodafone', 'Google Chrome', '2025-06-18 15:35:18', 'http://localhost/'),
(117, 'Italië', '192.168.178.46', 'T-Mobile', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Sa', '2025-06-19 10:32:50', 'http://localhost/Bezoekersgrafiek/index.php'),
(118, 'Nederland', '192.168.178.46', 'KPN', '', '2025-06-20 10:11:16', 'Direct');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `bezoekers`
--
ALTER TABLE `bezoekers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `bezoekers`
--
ALTER TABLE `bezoekers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
