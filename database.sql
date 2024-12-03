-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2024 at 11:48 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gis`
--

-- --------------------------------------------------------

--
-- Table structure for table `bencana_sebaran`
--

CREATE TABLE `bencana_sebaran` (
  `id` int(11) NOT NULL,
  `kecamatan` varchar(30) NOT NULL,
  `tanah_longsor` int(11) NOT NULL,
  `angin_kencang` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `bencana_sebaran`
--

INSERT INTO `bencana_sebaran` (`id`, `kecamatan`, `tanah_longsor`, `angin_kencang`) VALUES
(1, 'CARINGIN', 112, 101),
(2, 'CIAWI', 87, 39),
(3, 'CIGOMBONG', 109, 88),
(4, 'CIJERUK', 132, 102),
(5, 'CISARUA', 69, 36),
(6, 'MEGAMENDUNG', 138, 76),
(7, 'TAMANSARI', 19, 70);

-- --------------------------------------------------------

--
-- Table structure for table `data_lokasi`
--

CREATE TABLE `data_lokasi` (
  `id` int(11) NOT NULL,
  `id_kecamatan` varchar(20) DEFAULT NULL,
  `nama_tempat` varchar(50) DEFAULT NULL,
  `latlong` varchar(30) NOT NULL,
  `keterangan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `data_lokasi`
--

INSERT INTO `data_lokasi` (`id`, `id_kecamatan`, `nama_tempat`, `latlong`, `keterangan`) VALUES
(1, '3201071', 'Tamansari', '-6.6399254,106.7588494', ''),
(2, '3201080', 'Cijeruk', '-6.6814912,106.7970599', ''),
(3, '3201081', 'Cigombong', '-6.7311989,106.797352', ''),
(4, '3201090', 'Caringin', '-6.7128814,106.838158', ''),
(5, '3201100', 'Ciawi', '-6.712398,106.8945456', ''),
(6, '3201110', 'Cisarua', '-6.679303,106.9398378', ''),
(7, '3201120', 'Megamendung', '-6.6807469,106.9005964', '');

-- --------------------------------------------------------

--
-- Table structure for table `lokasi`
--

CREATE TABLE `lokasi` (
  `id` int(11) NOT NULL,
  `nama_tempat` varchar(50) NOT NULL,
  `tanah_longsor` varchar(5) NOT NULL,
  `angin_kencang` varchar(5) NOT NULL,
  `cluster` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lokasi`
--

INSERT INTO `lokasi` (`id`, `nama_tempat`, `tanah_longsor`, `angin_kencang`, `cluster`) VALUES
(1, 'CARINGIN', '112', '101', '3'),
(2, 'CIAWI', '87', '39', '1'),
(3, 'CIGOMBONG', '109', '88', '3'),
(4, 'CIJERUK', '132', '102', '3'),
(5, 'CISARUA', '69', '36', '1'),
(6, 'MEGAMENDUNG', '138', '76', '3'),
(7, 'TAMANSARI', '19', '70', '2');

-- --------------------------------------------------------

--
-- Table structure for table `sebaran_bencana`
--

CREATE TABLE `sebaran_bencana` (
  `id` int(11) NOT NULL,
  `kecamatan` varchar(30) NOT NULL,
  `tanah_longsor` int(11) NOT NULL,
  `banjir` int(11) NOT NULL,
  `kebakaran` int(11) NOT NULL,
  `angin_kencang` int(11) NOT NULL,
  `kekeringan` int(11) NOT NULL,
  `pergeseran_tanah` int(11) NOT NULL,
  `gempa_bumi` int(11) NOT NULL,
  `lain_lain` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sebaran_bencana`
--

INSERT INTO `sebaran_bencana` (`id`, `kecamatan`, `tanah_longsor`, `banjir`, `kebakaran`, `angin_kencang`, `kekeringan`, `pergeseran_tanah`, `gempa_bumi`, `lain_lain`) VALUES
(1, 'CARINGIN', 112, 5, 6, 101, 1, 2, 9, 23),
(2, 'CIAWI', 87, 17, 1, 39, 1, 3, 2, 12),
(3, 'CIGOMBONG', 109, 7, 2, 88, 0, 2, 8, 17),
(4, 'CIJERUK', 132, 4, 11, 102, 0, 6, 10, 25),
(5, 'CISARUA', 69, 13, 10, 36, 2, 2, 3, 10),
(6, 'MEGAMENDUNG', 138, 14, 16, 76, 8, 5, 9, 25),
(7, 'TAMANSARI', 19, 4, 5, 70, 10, 2, 0, 10);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bencana_sebaran`
--
ALTER TABLE `bencana_sebaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_lokasi`
--
ALTER TABLE `data_lokasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lokasi`
--
ALTER TABLE `lokasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sebaran_bencana`
--
ALTER TABLE `sebaran_bencana`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bencana_sebaran`
--
ALTER TABLE `bencana_sebaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `data_lokasi`
--
ALTER TABLE `data_lokasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `lokasi`
--
ALTER TABLE `lokasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sebaran_bencana`
--
ALTER TABLE `sebaran_bencana`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
