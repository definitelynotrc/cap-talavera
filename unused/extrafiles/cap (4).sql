-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 10, 2024 at 10:19 AM
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
-- Database: `cap`
--

-- --------------------------------------------------------

--
-- Table structure for table `acad_year`
--

CREATE TABLE `acad_year` (
  `ay_id` int(11) NOT NULL,
  `year_start` int(11) NOT NULL,
  `isActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `acad_year`
--

INSERT INTO `acad_year` (`ay_id`, `year_start`, `isActive`) VALUES
(1, 2024, 1);

-- --------------------------------------------------------

--
-- Table structure for table `advisory_class`
--

CREATE TABLE `advisory_class` (
  `advisory_class_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `ay_id` int(11) NOT NULL,
  `sem_id` int(11) NOT NULL,
  `isActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advisory_class`
--

INSERT INTO `advisory_class` (`advisory_class_id`, `class_id`, `ay_id`, `sem_id`, `isActive`) VALUES
(14, 7, 1, 1, 1),
(15, 9, 1, 1, 1),
(16, 9, 1, 2, 1),
(17, 7, 1, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `class_id` int(11) NOT NULL,
  `year_level` int(11) DEFAULT NULL,
  `section_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`class_id`, `year_level`, `section_id`) VALUES
(7, 4, 10),
(8, 4, 11),
(9, 1, 12);

-- --------------------------------------------------------

--
-- Table structure for table `class_teacher`
--

CREATE TABLE `class_teacher` (
  `class_teacher_id` int(11) NOT NULL,
  `advisory_class_id` int(11) NOT NULL,
  `teacher_type` varchar(50) NOT NULL,
  `sub_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_teacher`
--

INSERT INTO `class_teacher` (`class_teacher_id`, `advisory_class_id`, `teacher_type`, `sub_id`, `user_id`) VALUES
(45, 14, 'Primary Teacher', 1, 17),
(46, 14, 'Primary Teacher', 2, 18),
(47, 14, 'Primary Teacher', 3, 19),
(48, 14, 'Primary Teacher', 4, 20),
(49, 14, 'Primary Teacher', 5, 67),
(50, 14, 'Primary Teacher', 6, 63);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dep_id` int(11) NOT NULL,
  `department` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','archived') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dep_id`, `department`, `description`, `status`) VALUES
(1, 'BSIT', 'Bachelor of Science in Information Technology', 'active'),
(2, 'BSBA', 'Bachelor of Science in Business Administration', 'active'),
(3, 'BEED', 'Bachelor of Science in Education', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `dep_sub`
--

CREATE TABLE `dep_sub` (
  `dep_sub_id` int(11) NOT NULL,
  `dep_id` int(11) DEFAULT NULL,
  `sub_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dep_sub`
--

INSERT INTO `dep_sub` (`dep_sub_id`, `dep_id`, `sub_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `evaluation`
--

CREATE TABLE `evaluation` (
  `eval_id` int(11) NOT NULL,
  `remarks` text NOT NULL,
  `rate_result` decimal(10,2) NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_teacher_id` int(11) NOT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `transaction_code` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation`
--

INSERT INTO `evaluation` (`eval_id`, `remarks`, `rate_result`, `user_id`, `class_teacher_id`, `date_created`, `transaction_code`) VALUES
(32, 'asdasdsad', 4.90, 69, 47, '2024-12-10 17:08:29', '0AF3E815-1733821709'),
(33, 'asdasdsadsa', 5.00, 73, 45, '2024-12-10 17:09:10', 'E0781A04-1733821750');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `ques_id` int(11) NOT NULL,
  `questions` text NOT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `status` enum('active','archived') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`ques_id`, `questions`, `date_created`, `status`) VALUES
(1, 'Organizes teacher/student learning activities.', '2024-11-28 03:17:16', 'active'),
(2, 'Provides appropriate worksheets/exercises/handouts to students.', '2024-11-28 03:20:25', 'active'),
(3, 'Developed instructional materials in consultation/cooperation with peers and supervisor.', '2024-11-28 03:21:27', 'active'),
(4, 'Has thorough knowledge of his subject.', '2024-11-28 03:22:20', 'active'),
(5, 'Has comprehensive knowledge of his subject.', '2024-11-28 03:37:58', 'active'),
(6, 'Utilize instructional materials to make learning more  meaningful.', '2024-11-28 03:38:45', 'active'),
(7, 'Prescribes reasonable course requirements.', '2024-11-28 03:40:54', 'active'),
(8, 'Shows openness to questions/suggestions/reactions criticism.', '2024-11-28 03:41:20', 'active'),
(9, 'Communicates ideas effectively.', '2024-11-28 03:42:15', 'active'),
(10, 'Submit reports/grades in time.', '2024-11-28 03:42:49', 'active'),
(11, 'Observes punctuality in class and school activities.', '2024-11-28 03:43:38', 'active'),
(12, 'Observes good grooming and respectable was of dressing.', '2024-11-28 03:44:14', 'active'),
(13, 'Shows sincerity and maturity in dealing with superior, peers  and students.', '2024-11-28 03:44:59', 'active'),
(14, 'Maintains self-control at all times.', '2024-11-28 03:45:15', 'active'),
(15, 'Practices good moral and intellectual behavior.', '2024-11-28 03:46:46', 'active'),
(16, 'Has the ability to cope with difficult situations.', '2024-11-28 03:47:07', 'active'),
(17, 'Cooperates willingly with others in the achievements of  common goals. ', '2024-11-28 03:48:10', 'active'),
(18, 'Participates actively in officials, social and cultural activities.', '2024-11-28 03:48:48', 'active'),
(19, 'Shares expertise willingly and enthusiastically.', '2024-11-28 03:49:06', 'active'),
(21, 'Shows evidence of professional growth in terms of work output. a(l.e. Instructional materials, consultancy, conduct of seminars)', '2024-11-28 03:50:41', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `rate`
--

CREATE TABLE `rate` (
  `rate_id` int(11) NOT NULL,
  `rate_name` varchar(100) NOT NULL,
  `rates` int(11) NOT NULL,
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rate`
--

INSERT INTO `rate` (`rate_id`, `rate_name`, `rates`, `date_created`) VALUES
(1, 'Outstanding', 5, '2024-11-28 13:16:42'),
(2, 'Very Satisfactory', 4, '2024-11-28 13:17:55'),
(3, 'Satisfactory', 3, '2024-11-28 13:18:09'),
(4, 'Poor', 2, '2024-11-28 13:18:24'),
(5, 'Very Poor', 1, '2024-11-28 13:18:35');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `ratings_id` int(11) NOT NULL,
  `eval_id` int(11) NOT NULL,
  `ques_id` int(11) NOT NULL,
  `rate_id` int(11) NOT NULL,
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`ratings_id`, `eval_id`, `ques_id`, `rate_id`, `date_created`) VALUES
(541, 32, 1, 5, '2024-12-10 17:08:29'),
(542, 32, 2, 4, '2024-12-10 17:08:29'),
(543, 32, 3, 5, '2024-12-10 17:08:29'),
(544, 32, 4, 4, '2024-12-10 17:08:29'),
(545, 32, 5, 5, '2024-12-10 17:08:29'),
(546, 32, 6, 5, '2024-12-10 17:08:29'),
(547, 32, 7, 5, '2024-12-10 17:08:29'),
(548, 32, 8, 5, '2024-12-10 17:08:29'),
(549, 32, 9, 5, '2024-12-10 17:08:29'),
(550, 32, 10, 5, '2024-12-10 17:08:29'),
(551, 32, 11, 5, '2024-12-10 17:08:29'),
(552, 32, 12, 5, '2024-12-10 17:08:29'),
(553, 32, 13, 5, '2024-12-10 17:08:29'),
(554, 32, 14, 5, '2024-12-10 17:08:29'),
(555, 32, 15, 5, '2024-12-10 17:08:29'),
(556, 32, 16, 5, '2024-12-10 17:08:29'),
(557, 32, 17, 5, '2024-12-10 17:08:29'),
(558, 32, 18, 5, '2024-12-10 17:08:29'),
(559, 32, 19, 5, '2024-12-10 17:08:29'),
(560, 32, 21, 5, '2024-12-10 17:08:29'),
(561, 33, 1, 5, '2024-12-10 17:09:10'),
(562, 33, 2, 5, '2024-12-10 17:09:10'),
(563, 33, 3, 5, '2024-12-10 17:09:10'),
(564, 33, 4, 5, '2024-12-10 17:09:10'),
(565, 33, 5, 5, '2024-12-10 17:09:10'),
(566, 33, 6, 5, '2024-12-10 17:09:10'),
(567, 33, 7, 5, '2024-12-10 17:09:10'),
(568, 33, 8, 5, '2024-12-10 17:09:10'),
(569, 33, 9, 5, '2024-12-10 17:09:10'),
(570, 33, 10, 5, '2024-12-10 17:09:10'),
(571, 33, 11, 5, '2024-12-10 17:09:10'),
(572, 33, 12, 5, '2024-12-10 17:09:10'),
(573, 33, 13, 5, '2024-12-10 17:09:10'),
(574, 33, 14, 5, '2024-12-10 17:09:10'),
(575, 33, 15, 5, '2024-12-10 17:09:10'),
(576, 33, 16, 5, '2024-12-10 17:09:10'),
(577, 33, 17, 5, '2024-12-10 17:09:10'),
(578, 33, 18, 5, '2024-12-10 17:09:10'),
(579, 33, 19, 5, '2024-12-10 17:09:10'),
(580, 33, 21, 5, '2024-12-10 17:09:10');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `section_id` int(11) NOT NULL,
  `sections` varchar(11) NOT NULL,
  `status` enum('Active','Archived') DEFAULT 'Active',
  `dep_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`section_id`, `sections`, `status`, `dep_id`) VALUES
(2, 'B', 'Active', 1),
(5, 'G', 'Active', 1),
(6, 'A', 'Active', 2),
(7, 'A', 'Active', 1),
(8, 'C', 'Active', 1),
(9, 'C', 'Active', 1),
(10, 'D', 'Active', 1),
(11, 'A', 'Active', 1),
(12, 'B', 'Active', 1);

-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

CREATE TABLE `semester` (
  `sem_id` int(11) NOT NULL,
  `semesters` varchar(70) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`sem_id`, `semesters`, `date_created`) VALUES
(1, '2nd Semester', '2024-11-30 06:26:33'),
(2, '1st Semester', '2024-11-24 05:14:47');

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `sub_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `subjects` varchar(100) NOT NULL,
  `lec` int(11) NOT NULL,
  `lab` int(11) DEFAULT 0,
  `credit` int(11) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`sub_id`, `code`, `subjects`, `lec`, `lab`, `credit`, `description`) VALUES
(1, 'IT-SIA01', 'Systems Integration and Architecture', 2, 1, 3, 'Systems Integration and Architecture'),
(2, 'IT-SP01', 'Social and Professional Issues', 3, 0, 3, NULL),
(3, 'IT-CAP02', 'Capstone Project and Research 2', 3, 0, 3, NULL),
(4, 'IT-SW01', 'Seminars and Workshops', 0, 1, 2, NULL),
(5, 'IT-WS06', 'Web Digital Media', 3, 0, 3, NULL),
(6, 'IT-WS07', 'Mobile Application Technology', 2, 1, 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `mname` varchar(100) DEFAULT NULL,
  `lname` varchar(100) NOT NULL,
  `suffixname` varchar(20) DEFAULT NULL,
  `contact_no` varchar(20) NOT NULL,
  `houseno` int(20) NOT NULL,
  `street` varchar(100) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `postalcode` varchar(25) NOT NULL,
  `birthdate` date NOT NULL DEFAULT curdate(),
  `gender` enum('Female','Male') NOT NULL,
  `role` enum('Admin','Student','Instructor') NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) DEFAULT NULL,
  `is_archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fname`, `mname`, `lname`, `suffixname`, `contact_no`, `houseno`, `street`, `barangay`, `city`, `province`, `postalcode`, `birthdate`, `gender`, `role`, `email`, `password`, `is_archived`) VALUES
(1, '', NULL, '', NULL, '0', 0, '', '', '', '', '', '2024-11-16', 'Female', 'Admin', 'admin', '$2y$10$NIAuJkJQqB31vAyk.lxgvuGDinzOVRgiBvpHcKmw8.KZq6abuYMWe', 1),
(11, 'Rodolfo', '', 'Dela Cruz', '', '9555726637', 14, 'Zone 4', 'Licaong', 'Science City of Munoz', 'Nueva Ecija', '3119', '2003-03-21', 'Male', 'Instructor', 'delacruzrodolfo1999@gmail.com', NULL, 0),
(13, 'Enrique', '', 'Santos', 'Jr.', '9755896058', 13, 'Villa Pinili', 'Bantug', 'Science City of Munoz', 'Nueva Ecija', '3119', '2001-04-22', 'Male', 'Student', 'esantosjr45@gmail.com', NULL, 0),
(15, 'Jenalyn', '', 'Sabado', '', '9555726637', 12, 'Zone 4', 'Licaong', 'Science City of Munoz', 'Nueva Ecija', '3119', '2003-03-21', 'Male', 'Student', 'jenalynsabado29@gmail.com', NULL, 0),
(16, 'Chriszy Mae', '', 'Salamanca', '', '9363640508', 313, 'Zone1', 'San Fabian', 'Sto Domingo', 'Nueva Ecija', '3133', '2002-04-08', 'Female', 'Student', 'chriszymaesalamancalaureta@gmail.com', NULL, 0),
(17, 'Eduard', '', 'Manalili', 'Jr.', '9876234567', 12, 'Zone 2', 'Pinagpanaan', 'Talavera', 'Nueva Ecija', '3112', '1999-03-09', 'Male', 'Instructor', 'endo@gmail.com', NULL, 0),
(18, 'Rey John', '', 'Aguillar', '', '9675452345', 1, 'Zone 3', 'Sumacab', 'cabanatuan', 'Nueva Ecija', '3', '1989-06-08', 'Male', 'Instructor', 'rey@gmail.com', NULL, 0),
(19, 'Johnica', '', 'Alejandro', '', '9374651723', 4, 'Z', 'Jasj', 'cga', 'Nueva Ecija', '2', '1990-11-02', 'Female', 'Instructor', 'jn@gmail.com', NULL, 0),
(20, 'Miguel', '', 'Santos', '', '9283818273', 5, 'a', 's', 'ii', 'Nueva Ecija', '6', '1981-06-01', 'Male', 'Instructor', 'miguel@gmail.com', NULL, 0),
(21, 'Gayle', '', 'De Jesus', '', '9232451234', 7, 'Highway 1', 'Pag asa', 'Talavera', 'Nueva Ecija', '8', '1984-05-05', 'Male', 'Instructor', 'gayle@gmail.com', NULL, 0),
(22, 'Camillo', '', 'Villaviza', '', '9764562534', 1, 'j', 'Jasj', 'cabanatuan', 'Nueva Ecija', '9', '1993-07-03', 'Male', 'Instructor', 'camillo@gmail.com', NULL, 0),
(47, 'Nicka Joy', 'Bernal', 'Dayag', '', '9655883839', 2, 'Purok 2', ' Brgy Villa Santos', 'Science City of Muñoz', 'Nueva Ecija', '3119', '2003-11-05', 'Female', 'Student', 'dayagnickajoy05@gmail.com', NULL, 0),
(48, 'Ricah ', 'Pable', 'Paraguison', '', '9636029948', 2, 'Purok 2', ' Brgy Villa Santos', 'Science City of Muñoz', 'Nueva Ecija', '3119', '2003-10-31', 'Female', 'Student', 'ricahparaguison@gmail.com ', NULL, 0),
(61, 'Johnicaa', NULL, 'Alejandroa', NULL, '9374651723', 4, 'Z', 'Jasj', 'cga', 'Nueva Ecija', '2', '1990-11-02', 'Female', 'Instructor', 'jn@gmail.com', '$2y$10$Ei9TAcRxDImB8xIyfQ8d3.aXcdWRMLPrbxGA6TObdmRf4Cr8pK.5u', 0),
(62, 'Miguela', NULL, 'Santosa', NULL, '9283818273', 5, 'a', 's', 'ii', 'Nueva Ecija', '6', '1981-06-01', 'Male', 'Instructor', 'miguel@gmail.com', '$2y$10$qOt6IMSJAv.cD.UKkYHOEOnxiyNQhmOtCl8QU8xns.E/K4uockYTm', 0),
(63, 'Gaylea', NULL, 'De Jesusa', NULL, '9232451234', 7, 'Highway 1', 'Pag asa', 'Talavera', 'Nueva Ecija', '8', '1984-05-05', 'Male', 'Instructor', 'gayle@gmail.com', '$2y$10$VIcWlDxV0p6RuF2tqFr2ReqnfbbH3snoTgEILcVuVmchNp4Kn1Onu', 0),
(64, 'Camilloa', NULL, 'Villavizaa', NULL, '9764562534', 1, 'j', 'Jasj', 'cabanatuan', 'Nueva Ecija', '9', '1993-07-03', 'Male', 'Instructor', 'camillo@gmail.com', '$2y$10$jOjpiU65lHZA3JikFE0IgOTHVcojhUz/EyG/h2HJrB4NsmK8CPq6u', 0),
(67, 'CHIE', NULL, 'chay', NULL, '9876234567', 12, 'Zone 2', 'Pinagpanaan', 'Talavera', 'Nueva Ecija', '3112', '1999-03-09', 'Male', 'Instructor', 'chiechay111@gmail.com', '$2y$10$o9YhtpIZDjo.gNUYbOiEJuxmFakwiyZq0H9JzD2I/eYWtjs9QOpRi', 0),
(69, 'Clint', NULL, 'Casil', NULL, '9282877890', 1, 'w', 'Sta Rita', 'Sto Domingo', 'Nueva Ecija', '3133', '2003-07-09', 'Male', 'Student', 'rcanlas012003@gmail.com ', '$2y$10$gPSiw4Csme5Vg45DVMiEPuEv9Zhxa5XoQD12SSoXaD9A.Qo9U3anu', 0),
(70, 'Bain', '', 'Hansly', '', '9282877890', 1, 'w', 'Sta Rita', 'Sto Domingo', 'Nueva Ecija', '3133', '2003-07-09', 'Male', 'Student', 'villacillomarchie@gmail.com', '$2y$10$Ckb9cTxtR8s8PFhQ8WgMbudpTg9rJr7oL.C9mSO6OTiVZT7nj.hIi', 0),
(72, 'Clarence', 'Omana', 'Villacillo', 'Jr', '09719989313', 13822, 'NE AirPorT wAy', 'santor', 'portland', 'OR', '3128', '2024-12-06', 'Male', 'Student', 'clarencesk8@gmail.com', '$2y$10$PWkJ6NsiIazRwr6T324V8.Xhs5LWDwUGtb5g.jLBoVa8L7SjHY8RK', 0),
(73, 'Bitch Nigga', 'Omana', 'Canlas', 'Jr', '09511018949', 13822, 'NE AirPorT wAy', '', 'Bongabon', 'Nueva Ecija', '3128', '2024-12-06', 'Male', 'Instructor', 'ilovedonkeys483@gmail.com', '$2y$10$ToXTvoZq/gaPuNRWrQ1reeFhYiIO.GJ6VI3Q1BelSRxMdF2q/k6KO', 0),
(76, 'Ralph Ashley', 'Omana', 'O. Canlas', '', '09511018949', 13822, 'NE AirPorT wAy', '', 'Bongabon', 'Nueva Ecija', '3128', '2024-12-08', 'Male', 'Admin', 'floresjohnalber19@gmail.com', '$2y$10$bUMQsdcXNTH0p0VMuYLNd.NCmikD8pLckh3rQ/YRgOhw5fjlNGNNq', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_class`
--

CREATE TABLE `user_class` (
  `user_class_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `advisory_class_id` int(11) NOT NULL,
  `isActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_class`
--

INSERT INTO `user_class` (`user_class_id`, `user_id`, `advisory_class_id`, `isActive`) VALUES
(2, 13, 14, 1),
(3, 13, 14, 1),
(4, 13, 14, 1),
(5, 13, 14, 1),
(6, 13, 14, 1),
(7, 13, 14, 1),
(8, 69, 14, 1),
(9, 69, 14, 1),
(10, 69, 14, 1),
(11, 69, 14, 1),
(12, 69, 14, 1),
(13, 69, 14, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_dep`
--

CREATE TABLE `user_dep` (
  `user_dep_id` int(11) NOT NULL,
  `dep_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `isActive` tinyint(1) DEFAULT 1,
  `date_assigned` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_dep`
--

INSERT INTO `user_dep` (`user_dep_id`, `dep_id`, `user_id`, `isActive`, `date_assigned`) VALUES
(1, 1, 17, 1, '2024-11-30 00:00:00'),
(2, 1, 11, 1, '2024-12-03 00:00:00'),
(4, 1, 18, 1, '2024-12-03 00:00:00'),
(5, 1, 19, 1, '2024-12-03 00:00:00'),
(6, 1, 20, 1, '2024-12-03 00:00:00'),
(7, 2, 21, 1, '2024-12-03 00:00:00'),
(8, 1, 22, 1, '2024-12-03 00:00:00'),
(9, 2, 61, 1, '2024-12-03 00:00:00'),
(11, 1, 67, 1, '2024-12-03 00:00:00'),
(12, 1, 72, 1, '2024-12-07 00:00:00'),
(13, 1, 13, 1, '2024-12-07 00:00:00'),
(14, 1, 62, 1, '2024-12-07 00:00:00'),
(15, 1, 63, 1, '2024-12-07 00:00:00'),
(16, 1, 64, 1, '2024-12-07 00:00:00'),
(17, 1, 73, 1, '2024-12-07 00:00:00'),
(18, 1, 15, 1, '2024-12-07 00:00:00'),
(19, 1, 16, 1, '2024-12-07 00:00:00'),
(20, 2, 47, 1, '2024-12-07 00:00:00'),
(21, 2, 48, 1, '2024-12-07 00:00:00'),
(22, 1, 69, 1, '2024-12-07 00:00:00'),
(23, 1, 70, 1, '2024-12-07 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acad_year`
--
ALTER TABLE `acad_year`
  ADD PRIMARY KEY (`ay_id`);

--
-- Indexes for table `advisory_class`
--
ALTER TABLE `advisory_class`
  ADD PRIMARY KEY (`advisory_class_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `ay_id` (`ay_id`),
  ADD KEY `sem_id` (`sem_id`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `class_teacher`
--
ALTER TABLE `class_teacher`
  ADD PRIMARY KEY (`class_teacher_id`),
  ADD KEY `advisory_class_id` (`advisory_class_id`),
  ADD KEY `sub_id` (`sub_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dep_id`);

--
-- Indexes for table `dep_sub`
--
ALTER TABLE `dep_sub`
  ADD PRIMARY KEY (`dep_sub_id`),
  ADD KEY `dep_id` (`dep_id`),
  ADD KEY `sub_id` (`sub_id`);

--
-- Indexes for table `evaluation`
--
ALTER TABLE `evaluation`
  ADD PRIMARY KEY (`eval_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `class_teacher_id` (`class_teacher_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`ques_id`);

--
-- Indexes for table `rate`
--
ALTER TABLE `rate`
  ADD PRIMARY KEY (`rate_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`ratings_id`),
  ADD KEY `eval_id` (`eval_id`),
  ADD KEY `ques_id` (`ques_id`),
  ADD KEY `rate_id` (`rate_id`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`section_id`);

--
-- Indexes for table `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`sem_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`sub_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_class`
--
ALTER TABLE `user_class`
  ADD PRIMARY KEY (`user_class_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `advisory_class_id` (`advisory_class_id`);

--
-- Indexes for table `user_dep`
--
ALTER TABLE `user_dep`
  ADD PRIMARY KEY (`user_dep_id`),
  ADD KEY `dep_id` (`dep_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acad_year`
--
ALTER TABLE `acad_year`
  MODIFY `ay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `advisory_class`
--
ALTER TABLE `advisory_class`
  MODIFY `advisory_class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `class_teacher`
--
ALTER TABLE `class_teacher`
  MODIFY `class_teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `dep_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dep_sub`
--
ALTER TABLE `dep_sub`
  MODIFY `dep_sub_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `eval_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `ques_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `rate`
--
ALTER TABLE `rate`
  MODIFY `rate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `ratings_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=581;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `semester`
--
ALTER TABLE `semester`
  MODIFY `sem_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `sub_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `user_class`
--
ALTER TABLE `user_class`
  MODIFY `user_class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_dep`
--
ALTER TABLE `user_dep`
  MODIFY `user_dep_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `advisory_class`
--
ALTER TABLE `advisory_class`
  ADD CONSTRAINT `advisory_class_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `advisory_class_ibfk_2` FOREIGN KEY (`ay_id`) REFERENCES `acad_year` (`ay_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `advisory_class_ibfk_3` FOREIGN KEY (`sem_id`) REFERENCES `semester` (`sem_id`) ON DELETE CASCADE;

--
-- Constraints for table `class_teacher`
--
ALTER TABLE `class_teacher`
  ADD CONSTRAINT `class_teacher_ibfk_1` FOREIGN KEY (`advisory_class_id`) REFERENCES `advisory_class` (`advisory_class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `class_teacher_ibfk_2` FOREIGN KEY (`sub_id`) REFERENCES `subject` (`sub_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `class_teacher_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dep_sub`
--
ALTER TABLE `dep_sub`
  ADD CONSTRAINT `dep_sub_ibfk_1` FOREIGN KEY (`dep_id`) REFERENCES `department` (`dep_id`),
  ADD CONSTRAINT `dep_sub_ibfk_2` FOREIGN KEY (`sub_id`) REFERENCES `subject` (`sub_id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`eval_id`) REFERENCES `evaluation` (`eval_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`ques_id`) REFERENCES `question` (`ques_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_3` FOREIGN KEY (`rate_id`) REFERENCES `rate` (`rate_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_class`
--
ALTER TABLE `user_class`
  ADD CONSTRAINT `user_class_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_class_ibfk_2` FOREIGN KEY (`advisory_class_id`) REFERENCES `advisory_class` (`advisory_class_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_dep`
--
ALTER TABLE `user_dep`
  ADD CONSTRAINT `user_dep_ibfk_1` FOREIGN KEY (`dep_id`) REFERENCES `department` (`dep_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_dep_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
