-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2026 at 06:07 PM
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
-- Database: `adminpanel`
--

-- --------------------------------------------------------

--
-- Table structure for table `native_users`
--

CREATE TABLE `native_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `full_name` varchar(100) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `year_level` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `native_users`
--

INSERT INTO `native_users` (`id`, `username`, `password`, `created_at`, `full_name`, `course`, `year_level`, `email`) VALUES
(1, 'admin', '$2y$12$97UOZ9aH4dsGuzVjMKrUJupA1kPbjwpjq6lSe2ow0z2rzipqSGX5.', '2026-04-16 16:03:37', NULL, NULL, NULL, NULL),
(2, 'juan', '$2y$12$tQ43ngkhb6Yf6TE9AlFMwu19OGRcx61T5UM6k5ZfalWcgG5PTd6.m', '2026-04-16 16:03:37', NULL, NULL, NULL, NULL),
(3, 'maria', '$2y$12$mx4QarW4nwclAGzTb2u/4OIlV2hrDJV7yl/dH2Dd4.JAa5VG.P2Ii', '2026-04-16 16:03:37', NULL, NULL, NULL, NULL),
(4, 'pedro', '$2y$12$sUj/3nxFPvS.F0.5e1QOdOGJTMOpEQw/ZXuuHVMENLhngAR7lY17m', '2026-04-16 16:03:37', NULL, NULL, NULL, NULL),
(5, 'student', '$2y$12$KsQSSLY4y2PClyIgGCFWp.1SW4ITLiL5OWutTfnAS5kVTJiR1NwlS', '2026-04-16 16:03:37', NULL, NULL, NULL, NULL),
(6, 'sean', '$2y$10$fAVEd4aLcE6D1OrkDYmUaeCHBthh7locrEtaZgHpxDq/UAI5.NVeC', '2026-04-16 16:05:12', 'Sean Lawrence Devera Tapangan', 'Bachelor of Science In Information Technology', '2nd year', '424004001@ntc.edu.ph');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `color` varchar(50) NOT NULL DEFAULT 'bg-secondary'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `username`, `category`, `name`, `color`) VALUES
(1, 'admin', 'Frontend', 'HTML', 'bg-danger'),
(2, 'admin', 'Frontend', 'CSS', 'bg-primary'),
(3, 'admin', 'Frontend', 'JavaScript', 'bg-warning text-dark'),
(4, 'admin', 'Frontend', 'Bootstrap 5', 'bg-info text-dark'),
(5, 'admin', 'Backend', 'PHP', 'bg-secondary'),
(6, 'admin', 'Backend', 'MySQL', 'bg-success'),
(7, 'admin', 'Libraries', 'jQuery', 'bg-dark'),
(8, 'admin', 'Design', 'Responsive Design', 'bg-primary'),
(9, 'sean', 'Design', 'Blender 3D', 'bg-primary'),
(10, 'sean', 'Design', 'Figma', 'bg-info'),
(11, 'sean', 'Coding', 'Java', 'bg-success'),
(12, 'sean', 'Coding', 'PHP/MySQL', 'bg-dark text-white'),
(13, 'sean', 'Multimedia', 'Video Editing', 'bg-warning text-dark');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `native_users`
--
ALTER TABLE `native_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_skills_user` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `native_users`
--
ALTER TABLE `native_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `skills`
--
ALTER TABLE `skills`
  ADD CONSTRAINT `fk_skills_user` FOREIGN KEY (`username`) REFERENCES `native_users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
