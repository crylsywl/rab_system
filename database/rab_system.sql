-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Okt 2025 pada 13.32
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rab_system`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `isirab`
--

CREATE TABLE `isirab` (
  `id_rab` varchar(50) NOT NULL,
  `id_version` varchar(50) NOT NULL,
  `id_material` varchar(50) NOT NULL,
  `bagian` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `material_name` varchar(255) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` bigint(20) NOT NULL,
  `total_cost` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `isirab`
--

INSERT INTO `isirab` (`id_rab`, `id_version`, `id_material`, `bagian`, `category`, `material_name`, `unit`, `quantity`, `unit_price`, `total_cost`, `created_at`) VALUES
('rab_1760927879269_7146', 'ver_1760927879269_3857', 'mat_1760927879274_6881_3620', 'Budget', 'Pembuatan Pintu', 'Pintu Kayu', 'unit', 155, 950000, 147250000, '2025-10-20 02:37:59'),
('rab_1760927879269_7146', 'ver_1760927879269_3857', 'mat_1760927879285_3567_9892', 'Budget', 'Pembuatan Pintu', 'Paku Beton', 'kg', 144, 30000, 4320000, '2025-10-20 02:37:59'),
('rab_1760927879269_7146', 'ver_1760927879269_3857', 'mat_1760927879294_4057_2710', 'Budget', 'Pembuatan Pintu', 'Triplek 9mm', 'lembar', 155, 75000, 11625000, '2025-10-20 02:37:59'),
('rab_1760927879269_7146', 'ver_1760927879269_3857', 'mat_1760927879300_6022_7803', 'Budget', 'Pembuatan Jendela', 'Paku Beton', 'kg', 665, 30000, 19950000, '2025-10-20 02:37:59'),
('rab_1760927879269_7146', 'ver_1760927879269_3857', 'mat_1760927879304_4092_2696', 'Budget', 'Pembuatan Jendela', 'Cat Nippon Paint', 'kaleng', 166, 145000, 24070000, '2025-10-20 02:37:59'),
('rab_1760927879269_7146', 'ver_1760927879269_3857', 'mat_1760927879310_8977_3776', 'Budget', 'Pembuatan Jendela', 'Pipa PVC 3 inch', 'batang', 166, 45000, 7470000, '2025-10-20 02:37:59'),
('rab_1760927879269_7146', 'ver_1760927879269_3857', 'mat_1760927879317_4385_5483', 'Additional', 'Pembuatan Jalan', 'baja ringan banget', '10 meter', 177, 75000, 204230, '2025-10-20 02:37:59'),
('rab_1760927879269_7146', 'ver_1760927879269_3857', 'mat_1760927879321_4617_6733', 'Additional', 'Pembuatan Jalan', 'Besi Beton', 'batang', 166, 60000, 153230, '2025-10-20 02:37:59'),
('rab_1760927879269_7146', 'ver_1760927879269_3857', 'mat_1760927879326_9491_5341', 'Additional', 'Pembuatan Jalan', 'baja ringan banget', '10 meter', 177, 75000, 204230, '2025-10-20 02:37:59'),
('rab_1760927879269_7146', 'ver_1760927879269_3857', 'mat_1760927879332_7545_5388', 'Additional', 'Pembuatan P', 'Batu Bata Merah', 'buah', 1999, 800, 24603, '2025-10-20 02:37:59'),
('rab_1760927879269_7146', 'ver_1760927879269_3857', 'mat_1760927879339_2997_3785', 'Additional', 'Pembuatan P', 'Batu Bata Merah', 'buah', 176, 800, 2166, '2025-10-20 02:37:59'),
('rab_1760927879269_7146', 'ver_1760927879269_3857', 'mat_1760927879345_8828_4614', 'Additional', 'Pembuatan P', 'Cat Kayu', 'kaleng', 166, 120000, 306461, '2025-10-20 02:37:59'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', 'mat_1760940081271_4636_1430', 'Budget', 'Pembuatan Jendela', 'Kabel NYM', 'roll', 166, 280000, 46480000, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', 'mat_1760940081271_8161_9741', 'Budget', 'Pembuatan Jendela', 'Pipa PVC 3 inch', 'batang', 166, 45000, 7470000, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', 'mat_1760940081271_6038_7954', 'Budget', 'Pembuatan Pintu', 'Paku Beton', 'kg', 144, 30000, 4320000, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', 'mat_1760940081281_3622_6978', 'Budget', 'Pembuatan Pintu', 'Pintu Kayu', 'unit', 155, 950000, 147250000, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', 'mat_1760940081291_7756_1207', 'Budget', 'Pembuatan Pintu', 'Triplek 9mm', 'lembar', 155, 75000, 11625000, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', 'mat_1760940081291_8335_2889', 'Budget', 'Pembuatan Pintu', 'Triplek', 'lembar', 144, 65000, 9360000, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', 'mat_1760940081292_2812_7294', 'Budget', 'Pembuatan Atap', 'Kayu Balok', 'batang', 133, 95000, 12635000, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', 'mat_1760940081292_8773_9571', 'Budget', 'Pembuatan Atap', 'Cat Tembok', 'kaleng', 122, 150000, 18300000, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', 'mat_1760940081292_7906_6613', 'Budget', 'Pembuatan Atap', 'Batu Kali', 'truk', 122, 850000, 103700000, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', 'mat_1760940081292_3911_3824', 'Additional', 'Pembuatan Jalan', 'baja ringan banget', '10 meter', 177, 75000, 204230, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', 'mat_1760940081293_7492_2814', 'Additional', 'Pembuatan Jalan', 'baja ringan banget', '10 meter', 177, 75000, 204230, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', 'mat_1760940081293_4863_5402', 'Additional', 'Pembuatan Jalan', 'Besi Beton', 'batang', 166, 60000, 153230, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', 'mat_1760940081293_2125_7116', 'Additional', 'Pembuatan Pagar Utama', 'Batu Bata Merah', 'buah', 1999, 800, 24603, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', 'mat_1760940081294_4654_5218', 'Additional', 'Pembuatan Pagar Utama', 'Batu Bata Merah', 'buah', 176, 800, 2166, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', 'mat_1760940081294_6340_2321', 'Additional', 'Pembuatan Pagar Utama', 'Cat Kayu', 'kaleng', 166, 120000, 306461, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', 'mat_1760940081294_3227_6712', 'Additional', 'Pembuatan Aliran Air', 'Kran Taman', 'buah', 155, 75000, 178846, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922426_3194_7046', 'Budget', 'Pembuatan Atap', 'Batu Kali', 'truk', 122, 850000, 103700000, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922438_4611_4274', 'Budget', 'Pembuatan Atap', 'Cat Tembok', 'kaleng', 122, 150000, 18300000, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922438_5347_7398', 'Budget', 'Pembuatan Atap', 'Kayu Balok', 'batang', 133, 95000, 12635000, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922439_5758_9464', 'Budget', 'Pembuatan Atap 2', 'Besi Beton', 'batang', 966, 60000, 57960000, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922439_3346_9308', 'Budget', 'Pembuatan Atap 2', 'Cat Tembok', 'kaleng', 15, 150000, 2250000, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922439_7295_9471', 'Budget', 'Pembuatan Atap 2', 'Triplek 9mm', 'lembar', 188, 75000, 14100000, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922440_6391_1668', 'Budget', 'Pembuatan Jendela', 'Kabel NYM', 'roll', 166, 280000, 46480000, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922440_7637_1560', 'Budget', 'Pembuatan Jendela', 'Pipa PVC 3 inch', 'batang', 166, 45000, 7470000, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922452_4100_1532', 'Budget', 'Pembuatan Pintu', 'Paku Beton', 'kg', 144, 30000, 4320000, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922453_7407_1235', 'Budget', 'Pembuatan Pintu', 'Pintu Kayu', 'unit', 155, 950000, 147250000, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922453_7951_5640', 'Budget', 'Pembuatan Pintu', 'Triplek', 'lembar', 144, 65000, 9360000, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922453_6574_5584', 'Budget', 'Pembuatan Pintu', 'Triplek 9mm', 'lembar', 155, 75000, 11625000, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922454_9961_1452', 'Additional', 'Pembuatan Aliran Air', 'Kran Taman', 'buah', 155, 75000, 178846, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922454_8013_2230', 'Additional', 'Pembuatan Aliran Air 2', 'Cat Tembok', 'kaleng', 155, 150000, 357692, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922454_9826_1128', 'Additional', 'Pembuatan Jalan', 'baja ringan banget', '10 meter', 177, 75000, 204230, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922454_9126_6667', 'Additional', 'Pembuatan Jalan', 'baja ringan banget', '10 meter', 177, 75000, 204230, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922455_2361_9224', 'Additional', 'Pembuatan Jalan', 'Besi Beton', 'batang', 166, 60000, 153230, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922455_8092_9994', 'Additional', 'Pembuatan Pagar Utama', 'Batu Bata Merah', 'buah', 1999, 800, 24603, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922455_5487_9848', 'Additional', 'Pembuatan Pagar Utama', 'Batu Bata Merah', 'buah', 176, 800, 2166, '2025-10-20 06:48:42'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', 'mat_1760942922456_6660_1279', 'Additional', 'Pembuatan Pagar Utama', 'Cat Kayu', 'kaleng', 166, 120000, 306461, '2025-10-20 06:48:42'),
('rab_1760946854912_2077', 'ver_1760946854912_4553', 'mat_1760946854914_5317_9074', 'Budget', 'Pembuatan Pintu', 'Cat Tembok', 'kaleng', 122, 150000, 18300000, '2025-10-20 07:54:14'),
('rab_1760946854912_2077', 'ver_1760946854912_4553', 'mat_1760946854915_5609_1663', 'Budget', 'Pembuatan Pintu', 'Batu Kali', 'truk', 112, 850000, 95200000, '2025-10-20 07:54:14'),
('rab_1760946854912_2077', 'ver_1760946854912_4553', 'mat_1760946854916_9275_3752', 'Budget', 'Pembuatan Jendela', 'Pintu Kayu', 'unit', 112, 950000, 106400000, '2025-10-20 07:54:14'),
('rab_1760946854912_2077', 'ver_1760946854912_4553', 'mat_1760946854917_1220_2651', 'Budget', 'Pembuatan Jendela', 'Kabel NYM', 'roll', 166, 280000, 46480000, '2025-10-20 07:54:14'),
('rab_1760946854912_2077', 'ver_1760946854912_4553', 'mat_1760946854917_3717_2881', 'Budget', 'Pembuatan Jendela', 'Besi Siku', 'batang', 134, 120000, 16080000, '2025-10-20 07:54:14'),
('rab_1760946854912_2077', 'ver_1760946854912_4553', 'mat_1760946854918_5210_3524', 'Budget', 'Pembuatan Atap', 'Cat Eksterior', 'kaleng', 177, 180000, 31860000, '2025-10-20 07:54:14'),
('rab_1760946854912_2077', 'ver_1760946854912_4553', 'mat_1760946854919_1864_7649', 'Budget', 'Pembuatan Atap', 'Lem Fox', 'kaleng', 189, 35000, 6615000, '2025-10-20 07:54:14'),
('rab_1760946854912_2077', 'ver_1760946854912_4553', 'mat_1760946854919_4750_7197', 'Budget', 'Pembuatan Atap', 'Keramik Dinding', 'dus', 188, 85000, 15980000, '2025-10-20 07:54:14'),
('rab_1760946854912_2077', 'ver_1760946854912_4553', 'mat_1760946854930_1530_4402', 'Additional', 'Pembuatan Jalan', 'Plat Besi', 'lembar', 199, 260000, 923928, '2025-10-20 07:54:14'),
('rab_1760946854912_2077', 'ver_1760946854912_4553', 'mat_1760946854931_5165_1751', 'Additional', 'Pembuatan Jalan', 'Pintu Kayu', 'unit', 766, 950000, 12994642, '2025-10-20 07:54:14'),
('rab_1760946854912_2077', 'ver_1760946854912_4553', 'mat_1760946854932_2786_8661', 'Additional', 'Pembuatan Jalan', 'Pipa PVC 3 inch', 'batang', 176, 45000, 141428, '2025-10-20 07:54:14'),
('rab_1760946854912_2077', 'ver_1760946854912_4553', 'mat_1760946854933_4073_7118', 'Additional', 'Pembuatan Aliran Air', 'Pipa PVC 3 inch', 'batang', 899, 45000, 722410, '2025-10-20 07:54:14'),
('rab_1760946854912_2077', 'ver_1760946854912_4553', 'mat_1760946854933_4385_7980', 'Additional', 'Pembuatan Aliran Air', 'Granit Tile', 'dus', 155, 145000, 401339, '2025-10-20 07:54:14'),
('rab_1760946854912_2077', 'ver_1760946893438_8405', 'mat_1760946893451_3862_2868', 'Budget', 'Pembuatan Atap', 'Cat Eksterior', 'kaleng', 177, 180000, 31860000, '2025-10-20 07:54:53'),
('rab_1760946854912_2077', 'ver_1760946893438_8405', 'mat_1760946893451_6425_9546', 'Budget', 'Pembuatan Atap', 'Keramik Dinding', 'dus', 188, 85000, 15980000, '2025-10-20 07:54:53'),
('rab_1760946854912_2077', 'ver_1760946893438_8405', 'mat_1760946893452_8260_7227', 'Budget', 'Pembuatan Atap', 'Lem Fox', 'kaleng', 189, 35000, 6615000, '2025-10-20 07:54:53'),
('rab_1760946854912_2077', 'ver_1760946893438_8405', 'mat_1760946893461_7950_4294', 'Budget', 'Pembuatan Jendela', 'Besi Siku', 'batang', 134, 120000, 16080000, '2025-10-20 07:54:53'),
('rab_1760946854912_2077', 'ver_1760946893438_8405', 'mat_1760946893461_7492_2578', 'Budget', 'Pembuatan Jendela', 'Kabel NYM', 'roll', 166, 280000, 46480000, '2025-10-20 07:54:53'),
('rab_1760946854912_2077', 'ver_1760946893438_8405', 'mat_1760946893461_3689_4155', 'Budget', 'Pembuatan Jendela', 'Pintu Kayu', 'unit', 112, 950000, 106400000, '2025-10-20 07:54:53'),
('rab_1760946854912_2077', 'ver_1760946893438_8405', 'mat_1760946893461_2452_2512', 'Budget', 'Pembuatan Jendela', 'Besi Hollow', 'batang', 199, 78000, 15522000, '2025-10-20 07:54:53'),
('rab_1760946854912_2077', 'ver_1760946893438_8405', 'mat_1760946893462_3162_6789', 'Budget', 'Pembuatan Pintu', 'Batu Kali', 'truk', 112, 850000, 95200000, '2025-10-20 07:54:53'),
('rab_1760946854912_2077', 'ver_1760946893438_8405', 'mat_1760946893462_3852_3587', 'Budget', 'Pembuatan Pintu', 'Cat Tembok', 'kaleng', 122, 150000, 18300000, '2025-10-20 07:54:53'),
('rab_1760946854912_2077', 'ver_1760946893438_8405', 'mat_1760946893462_7996_2641', 'Additional', 'Pembuatan Aliran Air', 'Granit Tile', 'dus', 155, 145000, 401339, '2025-10-20 07:54:53'),
('rab_1760946854912_2077', 'ver_1760946893438_8405', 'mat_1760946893462_8514_9586', 'Additional', 'Pembuatan Aliran Air', 'Pipa PVC 3 inch', 'batang', 899, 45000, 722410, '2025-10-20 07:54:53'),
('rab_1760946854912_2077', 'ver_1760946893438_8405', 'mat_1760946893462_1263_4157', 'Additional', 'Pembuatan Jalan', 'Pintu Kayu', 'unit', 766, 950000, 12994642, '2025-10-20 07:54:53'),
('rab_1760946854912_2077', 'ver_1760946893438_8405', 'mat_1760946893463_6003_6197', 'Additional', 'Pembuatan Jalan', 'Pipa PVC 3 inch', 'batang', 176, 45000, 141428, '2025-10-20 07:54:53'),
('rab_1760946854912_2077', 'ver_1760946893438_8405', 'mat_1760946893463_4100_6481', 'Additional', 'Pembuatan Jalan', 'Plat Besi', 'lembar', 199, 260000, 923928, '2025-10-20 07:54:53'),
('rab_1760946854912_2077', 'ver_1760947179332_4770', 'mat_1760947179342_5612_3955', 'Budget', 'Pembuatan Atap', 'Cat Eksterior', 'kaleng', 177, 180000, 31860000, '2025-10-20 07:59:39'),
('rab_1760946854912_2077', 'ver_1760947179332_4770', 'mat_1760947179343_1384_3262', 'Budget', 'Pembuatan Atap', 'Keramik Dinding', 'dus', 188, 85000, 15980000, '2025-10-20 07:59:39'),
('rab_1760946854912_2077', 'ver_1760947179332_4770', 'mat_1760947179343_4334_7370', 'Budget', 'Pembuatan Atap', 'Lem Fox', 'kaleng', 189, 35000, 6615000, '2025-10-20 07:59:39'),
('rab_1760946854912_2077', 'ver_1760947179332_4770', 'mat_1760947179343_9120_3664', 'Budget', 'Pembuatan Jendela', 'Besi Siku', 'batang', 134, 120000, 16080000, '2025-10-20 07:59:39'),
('rab_1760946854912_2077', 'ver_1760947179332_4770', 'mat_1760947179343_8787_3347', 'Budget', 'Pembuatan Jendela', 'Kabel NYM', 'roll', 166, 280000, 46480000, '2025-10-20 07:59:39'),
('rab_1760946854912_2077', 'ver_1760947179332_4770', 'mat_1760947179344_3624_9587', 'Budget', 'Pembuatan Jendela', 'Pintu Kayu', 'unit', 112, 950000, 106400000, '2025-10-20 07:59:39'),
('rab_1760946854912_2077', 'ver_1760947179332_4770', 'mat_1760947179344_8199_2024', 'Budget', 'Pembuatan Pintu', 'Batu Kali', 'truk', 112, 850000, 95200000, '2025-10-20 07:59:39'),
('rab_1760946854912_2077', 'ver_1760947179332_4770', 'mat_1760947179344_8163_4535', 'Budget', 'Pembuatan Pintu', 'Cat Tembok', 'kaleng', 122, 150000, 18300000, '2025-10-20 07:59:39'),
('rab_1760946854912_2077', 'ver_1760947179332_4770', 'mat_1760947179344_8482_8747', 'Budget', 'Pembuatan Pintu', 'Kayu Balok', 'batang', 189, 95000, 17955000, '2025-10-20 07:59:39'),
('rab_1760946854912_2077', 'ver_1760947179332_4770', 'mat_1760947179344_5716_8834', 'Additional', 'Pembuatan Aliran Air', 'Granit Tile', 'dus', 155, 145000, 401339, '2025-10-20 07:59:39'),
('rab_1760946854912_2077', 'ver_1760947179332_4770', 'mat_1760947179345_7025_6873', 'Additional', 'Pembuatan Aliran Air', 'Pipa PVC 3 inch', 'batang', 899, 45000, 722410, '2025-10-20 07:59:39'),
('rab_1760946854912_2077', 'ver_1760947179332_4770', 'mat_1760947179345_9413_7209', 'Additional', 'Pembuatan Jalan', 'Pintu Kayu', 'unit', 766, 950000, 12994642, '2025-10-20 07:59:39'),
('rab_1760946854912_2077', 'ver_1760947179332_4770', 'mat_1760947179354_2964_9976', 'Additional', 'Pembuatan Jalan', 'Pipa PVC 3 inch', 'batang', 176, 45000, 141428, '2025-10-20 07:59:39'),
('rab_1760946854912_2077', 'ver_1760947179332_4770', 'mat_1760947179354_2452_5807', 'Additional', 'Pembuatan Jalan', 'Plat Besi', 'lembar', 199, 260000, 923928, '2025-10-20 07:59:39'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860863_7877_1533', 'Budget', 'Pembuatan Pintu', 'Cat Kayu', 'kaleng', 199, 120000, 23880000, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860873_8572_5615', 'Budget', 'Pembuatan Pintu', 'Kawat Bendrat', 'kg', 199, 18000, 3582000, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860874_1706_8011', 'Budget', 'Pembuatan Pintu', 'Kayu Balok', 'batang', 199, 95000, 18905000, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860879_2839_7369', 'Budget', 'Pembuatan Jendela', 'Cat Tembok', 'kaleng', 189, 150000, 28350000, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860880_3743_4582', 'Budget', 'Pembuatan Jendela', 'Batu Kali', 'truk', 196, 850000, 166600000, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860881_3492_2251', 'Budget', 'Pembuatan Jendela', 'Besi Hollow', 'batang', 177, 78000, 13806000, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860881_9662_8184', 'Budget', 'Pembuatan Atap', 'Pintu Kayu', 'unit', 177, 950000, 168150000, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860882_7765_4303', 'Budget', 'Pembuatan Atap', 'Pintu Kayu', 'unit', 166, 950000, 157700000, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860883_4581_2271', 'Budget', 'Pembuatan Atap', 'Besi Hollow', 'batang', 188, 78000, 14664000, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860893_9682_2746', 'Additional', 'Pembuatan Jalan', 'baja ringan banget', '10 meter', 199, 75000, 266517, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860894_6636_9263', 'Additional', 'Pembuatan Jalan', 'Cat Tembok', 'kaleng', 199, 150000, 533035, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860895_3060_4574', 'Additional', 'Pembuatan Jalan', 'Genteng Beton', 'buah', 199, 7500, 26651, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860895_1199_7134', 'Additional', 'Pembuatan Aliran Air', 'Cat Kayu', 'kaleng', 199, 120000, 426428, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860896_7808_5079', 'Additional', 'Pembuatan Aliran Air', 'Batu Kali', 'truk', 199, 850000, 3020535, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860897_3258_1524', 'Additional', 'Pembuatan Aliran Air', 'Cat Nippon Paint', 'kaleng', 199, 145000, 515267, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860897_9670_3177', 'Additional', 'Pembuatan Gerbang Utama', 'Besi Hollow', 'batang', 188, 78000, 261857, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860898_3419_1132', 'Additional', 'Pembuatan Gerbang Utama', 'Triplek 9mm', 'lembar', 199, 75000, 266517, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', 'mat_1760947860899_7975_2758', 'Additional', 'Pembuatan Gerbang Utama', 'Engsel Pintu', 'buah', 199, 10000, 35535, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219921_2997_2628', 'Budget', 'Pembuatan Atap', 'Besi Hollow', 'batang', 188, 78000, 14664000, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219930_1519_9592', 'Budget', 'Pembuatan Atap', 'Pintu Kayu', 'unit', 177, 950000, 168150000, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219931_3165_8724', 'Budget', 'Pembuatan Atap', 'Pintu Kayu', 'unit', 166, 950000, 157700000, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219941_1899_1784', 'Budget', 'Pembuatan Jendela', 'Batu Kali', 'truk', 196, 850000, 166600000, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219941_1142_7618', 'Budget', 'Pembuatan Jendela', 'Besi Hollow', 'batang', 177, 78000, 13806000, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219942_9216_7578', 'Budget', 'Pembuatan Jendela', 'Cat Tembok', 'kaleng', 189, 150000, 28350000, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219942_1435_7246', 'Budget', 'Pembuatan Jendela', 'Besi Beton', 'batang', 199, 60000, 11940000, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219942_5289_5732', 'Budget', 'Pembuatan Pintu', 'Cat Kayu', 'kaleng', 199, 120000, 23880000, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219942_1634_1272', 'Budget', 'Pembuatan Pintu', 'Kawat Bendrat', 'kg', 199, 18000, 3582000, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219942_1282_1994', 'Budget', 'Pembuatan Pintu', 'Kayu Balok', 'batang', 199, 95000, 18905000, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219942_7794_9000', 'Additional', 'Pembuatan Aliran Air', 'Batu Kali', 'truk', 199, 850000, 3020535, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219943_9901_3753', 'Additional', 'Pembuatan Aliran Air', 'Cat Kayu', 'kaleng', 199, 120000, 426428, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219943_9864_6218', 'Additional', 'Pembuatan Aliran Air', 'Cat Nippon Paint', 'kaleng', 199, 145000, 515267, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219943_9832_7448', 'Additional', 'Pembuatan Gerbang Utama', 'Besi Hollow', 'batang', 188, 78000, 261857, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219943_7500_4253', 'Additional', 'Pembuatan Gerbang Utama', 'Engsel Pintu', 'buah', 199, 10000, 35535, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219943_8521_2821', 'Additional', 'Pembuatan Gerbang Utama', 'Triplek 9mm', 'lembar', 199, 75000, 266517, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219943_3434_7582', 'Additional', 'Pembuatan Jalan', 'baja ringan banget', '10 meter', 199, 75000, 266517, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219943_3820_2252', 'Additional', 'Pembuatan Jalan', 'Cat Tembok', 'kaleng', 199, 150000, 533035, '2025-10-20 08:16:59'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', 'mat_1760948219944_6842_7936', 'Additional', 'Pembuatan Jalan', 'Genteng Beton', 'buah', 199, 7500, 26651, '2025-10-20 08:16:59'),
('rab_1760956328671_9193', 'ver_1760956328671_2927', 'mat_1760956328674_8364_2922', 'Budget', 'Pembuatan Pintu', 'Kran Taman', 'buah', 123, 145000, 17835000, '2025-10-20 10:32:08'),
('rab_1760956328671_9193', 'ver_1760956328671_2927', '2', 'Budget', 'Pembuatan Pintu', 'pasir', '5 sak', 1, 100000, 100000, '2025-10-20 10:32:08'),
('rab_1760956328671_9193', 'ver_1760956454174_4411', 'mat_1760956328674_8364_2922', 'Budget', 'Pembuatan Pintu', 'Kran Taman', 'buah', 123, 145000, 17835000, '2025-10-20 10:34:14'),
('rab_1760956328671_9193', 'ver_1760956454174_4411', '2', 'Budget', 'Pembuatan Pintu', 'pasir', '5 sak', 1, 100000, 100000, '2025-10-20 10:34:14'),
('rab_1760956328671_9193', 'ver_1760956454174_4411', '1', 'Additional', 'Pembuatan Aliran Air', 'baja ringan banget', '10 meter', 100, 75000, 7500000, '2025-10-20 10:34:14'),
('rab_1760956328671_9193', 'ver_1760956454174_4411', 'mat_1760956454193_3505_2434', 'Additional', 'Pembuatan Aliran Air', 'Cat Tembok', 'kaleng', 12, 340000, 4080000, '2025-10-20 10:34:14'),
('rab_1760956328671_9193', 'ver_1760956522076_2711', 'mat_1760956328674_8364_2922', 'Budget', 'Pembuatan Pintu', 'Kran Taman', 'buah', 123, 145000, 17835000, '2025-10-20 10:35:22'),
('rab_1760956328671_9193', 'ver_1760956522076_2711', '2', 'Budget', 'Pembuatan Pintu', 'pasir', '5 sak', 1, 100000, 100000, '2025-10-20 10:35:22'),
('rab_1760956328671_9193', 'ver_1760956522076_2711', '1', 'Additional', 'Pembuatan Aliran Air', 'baja ringan banget', '10 meter', 100, 75000, 7500000, '2025-10-20 10:35:22'),
('rab_1760956328671_9193', 'ver_1760956522076_2711', 'mat_1760956454193_3505_2434', 'Additional', 'Pembuatan Aliran Air', 'Cat Tembok', 'kaleng', 12, 340000, 4080000, '2025-10-20 10:35:22'),
('rab_1760956328671_9193', 'ver_1760956522076_2711', 'mat_1760956522105_9682_8741', 'Additional', 'Pembuatan Aliran Air', 'Besi Beton', 'buah', 88, 90000, 7920000, '2025-10-20 10:35:22'),
('rab_1760959292183_5320', 'ver_1760959292183_9951', '2', 'Budget', 'Pembuatan Pintu', 'pasir', '5 sak', 1, 100000, 100000, '2025-10-20 11:21:32'),
('rab_1760959292183_5320', 'ver_1760959292183_9951', 'mat_1760959292238_4676_4675', 'Budget', 'Pembuatan Pintu', 'Kran Taman', 'buah', 44, 75000, 3300000, '2025-10-20 11:21:32'),
('rab_1760959292183_5320', 'ver_1760959317083_8248', 'mat_1760959292238_4676_4675', 'Budget', 'Pembuatan Pintu', 'Kran Taman', 'buah', 44, 75000, 3300000, '2025-10-20 11:21:57'),
('rab_1760959292183_5320', 'ver_1760959317083_8248', '2', 'Budget', 'Pembuatan Pintu', 'pasir', '5 sak', 1, 100000, 100000, '2025-10-20 11:21:57'),
('rab_1760959292183_5320', 'ver_1760959317083_8248', '1', 'Budget', 'Pembuatan Aliran Air', 'baja ringan banget', '10 meter', 199, 75000, 14925000, '2025-10-20 11:21:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `material`
--

CREATE TABLE `material` (
  `id_material` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `specification` text DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `material`
--

INSERT INTO `material` (`id_material`, `id_user`, `name`, `specification`, `unit`, `quantity`, `price`) VALUES
(1, 1, 'baja ringan banget', '8 mili', '10 meter', 100, 75000),
(2, 3, 'pasir', 'pasir pantai', '5 sak', 5, 100000),
(3, 3, 'Genteng', 'buat latihan silat', 'pcs', 16, 90000),
(4, 6, 'semen', 'kaki roda', 'sak', 9, 80000),
(85, 1, 'Besi Beton', 'Diameter 10mm', 'batang', 200, 60000),
(86, 1, 'Paku', 'Ukuran 5cm galvanis', 'kg', 50, 25000),
(87, 2, 'Cat Tembok', 'Dulux warna putih 5L', 'kaleng', 30, 150000),
(88, 2, 'Kawat Bendrat', 'Kawat baja 1mm', 'kg', 100, 18000),
(89, 3, 'Batu Bata Merah', 'Ukuran standar 5x10x20 cm', 'buah', 10000, 800),
(90, 3, 'Keramik Lantai', '40x40 Motif Marmer', 'dus', 150, 95000),
(91, 5, 'Kayu Balok', 'Ukuran 5x10x400 cm', 'batang', 70, 95000),
(92, 5, 'Triplek', 'Tebal 12mm', 'lembar', 80, 65000),
(93, 6, 'Cat Kayu', 'Warna coklat glossy 2.5L', 'kaleng', 25, 120000),
(94, 6, 'Genteng Beton', 'Warna abu-abu', 'buah', 600, 7500),
(95, 1, 'Batu Kali', 'Batu pondasi ukuran besar', 'truk', 5, 850000),
(96, 1, 'Besi Hollow', 'Hollow galvanis 4x4cm', 'batang', 120, 78000),
(97, 1, 'Semen Tiga Roda', 'Semen portland 40kg', 'sak', 100, 79000),
(98, 1, 'Cat Nippon Paint', 'Warna putih interior 5L', 'kaleng', 20, 145000),
(99, 2, 'Paku Beton', 'Ukuran 7 cm galvanis', 'kg', 80, 30000),
(100, 2, 'Triplek 9mm', 'Triplek meranti tebal 9mm', 'lembar', 60, 75000),
(101, 2, 'Pintu Kayu', 'Kayu jati ukuran 90x210 cm', 'unit', 10, 950000),
(102, 2, 'Engsel Pintu', 'Engsel stainless ukuran 4 inch', 'buah', 200, 10000),
(103, 3, 'Kabel NYM', 'Kabel listrik 3x1.5mm 50 meter', 'roll', 25, 280000),
(104, 3, 'Stop Kontak', 'Stop kontak tanam Panasonic', 'buah', 150, 18000),
(105, 3, 'Pipa PVC 3 inch', 'Rucika tipe AW', 'batang', 80, 45000),
(106, 3, 'Kran Taman', 'Kran kuningan ¾ inch', 'buah', 50, 75000),
(107, 5, 'Keramik Dinding', '25x40 Motif Batu Alam', 'dus', 100, 85000),
(108, 5, 'Granit Tile', '60x60 Motif Marmer', 'dus', 60, 145000),
(109, 5, 'Cat Eksterior', 'Nippon Weatherguard 5L', 'kaleng', 15, 180000),
(110, 5, 'Lem Fox', 'Lem kuning kaleng 1L', 'kaleng', 40, 35000),
(111, 6, 'Besi Siku', 'Ukuran 4x4x4mm panjang 6 meter', 'batang', 70, 120000),
(112, 6, 'Plat Besi', 'Plat baja tebal 3mm', 'lembar', 50, 260000),
(113, 6, 'Las Listrik', 'Elektroda las 3.2mm 20kg', 'kotak', 30, 340000),
(114, 6, 'Cat Anti Karat', 'Warna abu-abu 5L', 'kaleng', 25, 160000),
(115, 7, 'Batu bata', '2x2', 'pcs', 120, 3000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `rab`
--

CREATE TABLE `rab` (
  `id_rab` varchar(50) NOT NULL,
  `id_version` varchar(50) NOT NULL,
  `id_user` varchar(50) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `unit` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `location` text NOT NULL,
  `jumlah_total_budget` bigint(20) NOT NULL DEFAULT 0,
  `pembulatan_budget` bigint(20) NOT NULL DEFAULT 0,
  `permeterpersegi_budget` bigint(20) NOT NULL DEFAULT 0,
  `jumlah_total_additional` bigint(20) NOT NULL DEFAULT 0,
  `pembulatan_additional` bigint(20) NOT NULL DEFAULT 0,
  `permeterpersegi_additional` bigint(20) NOT NULL DEFAULT 0,
  `version_description` varchar(255) DEFAULT 'Initial Version',
  `is_latest` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rab`
--

INSERT INTO `rab` (`id_rab`, `id_version`, `id_user`, `project_name`, `unit`, `type`, `location`, `jumlah_total_budget`, `pembulatan_budget`, `permeterpersegi_budget`, `jumlah_total_additional`, `pembulatan_additional`, `permeterpersegi_additional`, `version_description`, `is_latest`, `created_at`) VALUES
('rab_1760927879269_7146', 'ver_1760927879269_3857', '1', 'Perumahan Kemang', 65, 45, 'Depok, Jawa Barat, Indonesia', 214685000, 215000000, 4777777, 215579920, 216000000, 4800000, 'Initial Version', 0, '2025-10-20 02:37:59'),
('rab_1760927879269_7146', 'ver_1760940081250_3487', '1', 'Perumahan Kemang', 65, 45, 'Depok, Jawa Barat, Indonesia', 361140000, 370000000, 8222222, 362213766, 371000000, 8244444, 'Edited from version ver_176092...', 0, '2025-10-20 06:01:21'),
('rab_1760927879269_7146', 'ver_1760942922423_1708', '1', 'Perumahan Kemang', 65, 45, 'Depok, Jawa Barat, Indonesia', 435450000, 570000000, 12666666, 436881458, 571000000, 12688888, 'Edited from version 20 Oct 2025 13:46', 1, '2025-10-20 06:48:42'),
('rab_1760946854912_2077', 'ver_1760946854912_4553', '1', 'Perumahan Griya Arjuna', 56, 44, 'Depok, Jawa Barat, Indonesia', 336915000, 340000000, 7727272, 352098747, 355000000, 8068181, 'Initial Version', 0, '2025-10-20 07:54:14'),
('rab_1760946854912_2077', 'ver_1760946893438_8405', '1', 'Perumahan Griya Arjuna', 56, 44, 'Depok, Jawa Barat, Indonesia', 352437000, 360000000, 8181818, 367620747, 365000000, 8295454, 'Edited from version 20 Oct 2025 14:54', 0, '2025-10-20 07:54:53'),
('rab_1760946854912_2077', 'ver_1760947179332_4770', '1', 'Perumahan Griya Arjuna', 56, 44, 'Depok, Jawa Barat, Indonesia', 354870000, 360000000, 8181818, 370053747, 375000000, 8522727, 'Edited from version 20 Oct 2025 14:54', 1, '2025-10-20 07:59:39'),
('rab_1760947860862_8185', 'ver_1760947860862_4186', '1', 'Perumahan Kalibaru permai', 56, 75, 'Depok, Jawa Barat, Indonesia', 595637000, 595700000, 7942666, 600989342, 601000000, 8013333, 'Initial Version', 0, '2025-10-20 08:11:00'),
('rab_1760947860862_8185', 'ver_1760948219919_4520', '1', 'Perumahan Kalibaru permai', 56, 75, 'Depok, Jawa Barat, Indonesia', 607577000, 610700000, 8142666, 612929342, 615000000, 8200000, 'Edited from version 20 Oct 2025 15:11:00', 1, '2025-10-20 08:16:59'),
('rab_1760956328671_9193', 'ver_1760956328671_2927', '1', 'Perumahan Agung Ganda Prasaja', 1, 35, 'Depok, Jawa Barat, Indonesia', 17935000, 18000000, 514285, 17935000, 18000000, 514285, 'Initial Version', 0, '2025-10-20 10:32:08'),
('rab_1760956328671_9193', 'ver_1760956454174_4411', '1', 'Perumahan Agung Ganda Prasaja', 1, 35, 'Depok, Jawa Barat, Indonesia', 17935000, 18000000, 514285, 29515000, 30000000, 857142, 'Edited from version 20 Oct 2025 17:32:08', 0, '2025-10-20 10:34:14'),
('rab_1760956328671_9193', 'ver_1760956522076_2711', '1', 'Perumahan Agung Ganda Prasaja', 1, 35, 'Depok, Jawa Barat, Indonesia', 17935000, 18000000, 514285, 37435000, 40000000, 1142857, 'Edited from version 20 Oct 2025 17:34:14', 1, '2025-10-20 10:35:22'),
('rab_1760959292183_5320', 'ver_1760959292183_9951', '1', 'Perumahan PGRI', 4, 43, 'Depok, Jawa Barat, Indonesia', 3400000, 3600000, 83720, 3400000, 3600000, 83720, 'Initial Version', 0, '2025-10-20 11:21:32'),
('rab_1760959292183_5320', 'ver_1760959317083_8248', '1', 'Perumahan PGRI', 4, 43, 'Depok, Jawa Barat, Indonesia', 18325000, 30600000, 711627, 18325000, 30600000, 711627, 'Edited from version 20 Oct 2025 18:21:32', 1, '2025-10-20 11:21:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `supplier`
--

CREATE TABLE `supplier` (
  `id_user` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `no_telp` int(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `supplier`
--

INSERT INTO `supplier` (`id_user`, `email`, `no_telp`, `address`) VALUES
('7', 'jayabaru@gmail.com', 98123476, 'jl. jaya baru no.67');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user_rab','supplier') DEFAULT 'user_rab'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama`, `username`, `password`, `role`) VALUES
(1, 'Administrator', 'admin', '$2y$10$4UXRm6igUL6NsghE130kRu/B88GMpk..OYxB4qbV32dCkq2Wm7rAy', 'admin'),
(2, 'RBA User', 'userrba', '$2y$10$4UXRm6igUL6NsghE130kRu/B88GMpk..OYxB4qbV32dCkq2Wm7rAy', 'user_rab'),
(3, 'Supplier A', 'supplier', '$2y$10$9cezUmwRu0ilQKXTQVc.kuMpJDL/UWKwYLuHuPxdFQOh.yir7/uee', 'supplier'),
(5, 'abiyusofyan', 'abiyuoke', '$2y$10$BLzg6Bovd3IHh3pJBKqf1eFZfs./stbyFLGfOmDLg72KNIntD7vNO', 'user_rab'),
(6, 'supplier 2', 'supplier2a', '$2y$10$e89pvPSyDOVKO4.ArsM43O/4LHZvpRypAIjwe8lYxyuAIK1wEGWx2', 'supplier'),
(7, 'toko material jaya baru', 'jayabaru2025', '$2y$10$aoTrbq0sJHibxkDJBHQRoOc08Il5NZhAZlJNKad3pa3VHF60O.A96', 'supplier'),
(8, 'cryl', 'cryl2025', '$2y$10$Ta3A2d9AX6xiXnXgqAiAOONKeIKol3yuR/CR6W65zTSbMfEizz6wO', 'user_rab'),
(9, 'user', 'user', '$2y$10$U0DgPB6E0pJz8t7heEhaDeqgJ5o.N.0h78SnNXXpRS8plvPn1MYDG', 'user_rab');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `isirab`
--
ALTER TABLE `isirab`
  ADD KEY `idx_isirab_material` (`id_rab`,`id_version`);

--
-- Indeks untuk tabel `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`id_material`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `rab`
--
ALTER TABLE `rab`
  ADD PRIMARY KEY (`id_rab`,`id_version`),
  ADD KEY `idx_rab_user` (`id_user`),
  ADD KEY `idx_rab_latest` (`id_rab`,`is_latest`),
  ADD KEY `idx_rab_created` (`created_at`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `material`
--
ALTER TABLE `material`
  MODIFY `id_material` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `isirab`
--
ALTER TABLE `isirab`
  ADD CONSTRAINT `isirab_ibfk_1` FOREIGN KEY (`id_rab`,`id_version`) REFERENCES `rab` (`id_rab`, `id_version`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `material`
--
ALTER TABLE `material`
  ADD CONSTRAINT `material_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
