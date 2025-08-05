-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 05, 2025 at 07:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `matrimonial`
--

-- --------------------------------------------------------

--
-- Table structure for table `biodata`
--

CREATE TABLE `biodata` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `age` int(3) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `hobbies` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `degree1` varchar(255) DEFAULT NULL,
  `institution1` varchar(255) DEFAULT NULL,
  `year1` int(4) DEFAULT NULL,
  `grade1` varchar(50) DEFAULT NULL,
  `degree2` varchar(255) DEFAULT NULL,
  `institution2` varchar(255) DEFAULT NULL,
  `year2` int(4) DEFAULT NULL,
  `grade2` varchar(50) DEFAULT NULL,
  `degree3` varchar(255) DEFAULT NULL,
  `institution3` varchar(255) DEFAULT NULL,
  `year3` int(4) DEFAULT NULL,
  `grade3` varchar(50) DEFAULT NULL,
  `degree4` varchar(255) DEFAULT NULL,
  `institution4` varchar(255) DEFAULT NULL,
  `year4` int(4) DEFAULT NULL,
  `grade4` varchar(50) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `income` varchar(100) DEFAULT NULL,
  `fatherName` varchar(255) DEFAULT NULL,
  `motherName` varchar(255) DEFAULT NULL,
  `siblings` int(2) DEFAULT NULL,
  `familyType` varchar(50) DEFAULT NULL,
  `preferences` varchar(255) DEFAULT NULL,
  `additionalInfo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `biodata`
--

INSERT INTO `biodata` (`id`, `user_id`, `age`, `address`, `hobbies`, `photo`, `degree1`, `institution1`, `year1`, `grade1`, `degree2`, `institution2`, `year2`, `grade2`, `degree3`, `institution3`, `year3`, `grade3`, `degree4`, `institution4`, `year4`, `grade4`, `occupation`, `company`, `income`, `fatherName`, `motherName`, `siblings`, `familyType`, `preferences`, `additionalInfo`) VALUES
(10, 24, 25, 'Konabari, Gazipur, Dhaka', 'music, movies', 'uploads/shahin.jpg', 'SSC ', 'Milestone', 2017, '5.00', 'HSC', 'Milestone', 2019, '5.00', 'CSE', 'DIU ', 2023, '3.5', 'MSC', 'BRAC', 2025, '3.4', 'Player', 'Playboy Club', '0-50 Thousands', 'ABCD', 'EFGH', 1, 'nuclear', 'educated, family-oriented', 'I am md. shahinur Alam Rabbi .looking forward to find a perfect match'),
(11, 25, 25, '60 Feet Barek Molla Road, Mirpur, Dhaka', 'reading, music, cooking, movies', 'uploads/rohan.jpg', 'SSC ', 'BLSC', 2018, '5.00', 'HSC', 'BLCS', 2020, '5.00', 'Bsc in CSE', 'ACC', 2025, '3.50', 'Msc in CSE', 'MIT', 2027, '3.50', 'Student', 'Not Working Yet ', '', 'Abcd', 'Efgh', 2, 'nuclear', 'educated, family-oriented', 'I am Rohan, '),
(12, 26, 65, 'Moktar bari road, Auchpara, Tongi, Gazipur', 'reading, sports', 'uploads/Screenshot 2025-05-25 231351.png', 'SSC', 'Milestone', 1984, '90%', 'HSC', 'ACC', 1985, '85%', 'Hon\'s', 'DIU', 1987, '2.56', 'Not Applicable', 'Not Applicable', 1980, '456', 'Businessman', 'Bekar company ltd', '', 'Hasem Ali', 'Gulbahar Khanom', 0, 'nuclear', 'educated, family-oriented', 'Old but yong');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `maritalStatus` varchar(50) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `fullName`, `phone`, `gender`, `maritalStatus`, `role`) VALUES
(8, 'admin@matrimonial.com', '$2y$10$O8WNTmBRzYIrTL/DCWebQezRafhaxDy6z3UsSlsatXMAE/PplXRBe', 'Admin', '1234567890', 'male', 'single', 'admin'),
(24, 'shahin@gmail.com', '$2y$10$WW0pm5A9ZM4XDZHEt31JQu0NxIzgoMST.7rtyQVJxSJaW7VoffJau', 'Shahinur Alam rabbi', '01328925633', 'male', 'single', 'user'),
(25, 'rohan@gmail.com', '$2y$10$3DBgaojUAtRq0lCO4qKpWuAdSyf.Tyso/CANqNggvuwgvaPhV3eCy', 'Mehedi Hasan Rohan', '01234567896', 'male', 'single', 'user'),
(26, 'abul@gmail.com', '$2y$10$.g62DrKU/TVDFRjNudA4e.cHOKW2XZhIb/GzE1H.9oi2SV2cidfiy', 'Md Abul Mia', '01547889966', 'male', 'single', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `biodata`
--
ALTER TABLE `biodata`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id_2` (`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `biodata`
--
ALTER TABLE `biodata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `biodata`
--
ALTER TABLE `biodata`
  ADD CONSTRAINT `biodata_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
