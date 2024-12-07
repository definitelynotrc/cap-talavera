-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 06, 2024 at 05:27 AM
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
(1, 4, 1, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `class_id` int(11) NOT NULL,
  `year_level` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`class_id`, `year_level`, `section_id`) VALUES
(1, 1, NULL),
(2, 2, NULL),
(3, 3, NULL),
(4, 4, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `class_student`
--

CREATE TABLE `class_student` (
  `class_student_id` int(11) NOT NULL,
  `advisory_class_id` int(11) DEFAULT NULL,
  `dep_id` int(11) DEFAULT NULL,
  `sub_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_student`
--

INSERT INTO `class_student` (`class_student_id`, `advisory_class_id`, `dep_id`, `sub_id`, `user_id`) VALUES
(1, 1, 1, 1, 15);

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
(2, 1, 'Primary Teacher', 5, 18),
(3, 1, 'Secondary Teacher', 2, 17),
(4, 1, 'Primary Teacher', 3, 19),
(5, 1, 'Primary Teacher', 1, 67),
(10, 1, 'Main', 4, 11),
(11, 1, 'student', 1, 13),
(12, 1, 'student', 2, 13),
(13, 1, 'student', 3, 13),
(14, 1, 'student', 4, 13),
(15, 1, 'student', 5, 13),
(16, 1, 'student', 6, 13),
(17, 1, 'student', 1, 69),
(18, 1, 'student', 2, 69),
(19, 1, 'student', 3, 69),
(20, 1, 'student', 4, 69),
(21, 1, 'student', 5, 69),
(22, 1, 'student', 6, 69),
(23, 1, 'student', 1, 15),
(24, 1, 'student', 1, 70),
(25, 1, 'student', 2, 70),
(26, 1, 'student', 3, 70),
(27, 1, 'student', 4, 70),
(28, 1, 'student', 5, 70),
(29, 1, 'student', 6, 70);

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
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation`
--

INSERT INTO `evaluation` (`eval_id`, `remarks`, `rate_result`, `user_id`, `class_teacher_id`, `date_created`) VALUES
(13, 'Skibi toilet', 5.00, 65, 3, '2024-12-03 23:24:11'),
(14, 'nyiknyiknyik', 2.00, 65, 2, '2024-12-03 23:25:08'),
(15, 'asdasdsa', 5.00, 67, 3, '2024-12-03 23:29:40'),
(16, 'asdasdasda', 5.00, 67, 6, '2024-12-03 23:30:00'),
(17, 'asdasdasdasdasd', 4.00, 69, 5, '2024-12-04 18:46:46'),
(18, 'asdasdasdas', 4.00, 70, 5, '2024-12-04 23:45:46'),
(19, 'asdsadasas', 4.00, 69, 3, '2024-12-05 01:03:04');

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
(161, 13, 1, 5, '2024-12-03 23:24:11'),
(162, 13, 2, 5, '2024-12-03 23:24:11'),
(163, 13, 3, 5, '2024-12-03 23:24:11'),
(164, 13, 4, 5, '2024-12-03 23:24:11'),
(165, 13, 5, 5, '2024-12-03 23:24:11'),
(166, 13, 6, 5, '2024-12-03 23:24:11'),
(167, 13, 7, 5, '2024-12-03 23:24:11'),
(168, 13, 8, 5, '2024-12-03 23:24:11'),
(169, 13, 9, 5, '2024-12-03 23:24:11'),
(170, 13, 10, 5, '2024-12-03 23:24:11'),
(171, 13, 11, 5, '2024-12-03 23:24:11'),
(172, 13, 12, 5, '2024-12-03 23:24:11'),
(173, 13, 13, 5, '2024-12-03 23:24:11'),
(174, 13, 14, 5, '2024-12-03 23:24:11'),
(175, 13, 15, 5, '2024-12-03 23:24:11'),
(176, 13, 16, 5, '2024-12-03 23:24:11'),
(177, 13, 17, 5, '2024-12-03 23:24:11'),
(178, 13, 18, 5, '2024-12-03 23:24:11'),
(179, 13, 19, 5, '2024-12-03 23:24:11'),
(180, 13, 21, 5, '2024-12-03 23:24:11'),
(181, 14, 1, 1, '2024-12-03 23:25:08'),
(182, 14, 2, 1, '2024-12-03 23:25:08'),
(183, 14, 3, 1, '2024-12-03 23:25:08'),
(184, 14, 4, 1, '2024-12-03 23:25:08'),
(185, 14, 5, 2, '2024-12-03 23:25:08'),
(186, 14, 6, 3, '2024-12-03 23:25:08'),
(187, 14, 7, 4, '2024-12-03 23:25:08'),
(188, 14, 8, 5, '2024-12-03 23:25:08'),
(189, 14, 9, 4, '2024-12-03 23:25:08'),
(190, 14, 10, 3, '2024-12-03 23:25:08'),
(191, 14, 11, 2, '2024-12-03 23:25:08'),
(192, 14, 12, 1, '2024-12-03 23:25:08'),
(193, 14, 13, 1, '2024-12-03 23:25:08'),
(194, 14, 14, 2, '2024-12-03 23:25:08'),
(195, 14, 15, 3, '2024-12-03 23:25:08'),
(196, 14, 16, 4, '2024-12-03 23:25:08'),
(197, 14, 17, 5, '2024-12-03 23:25:08'),
(198, 14, 18, 4, '2024-12-03 23:25:08'),
(199, 14, 19, 3, '2024-12-03 23:25:08'),
(200, 14, 21, 2, '2024-12-03 23:25:08'),
(201, 15, 1, 5, '2024-12-03 23:29:40'),
(202, 15, 2, 5, '2024-12-03 23:29:40'),
(203, 15, 3, 5, '2024-12-03 23:29:40'),
(204, 15, 4, 5, '2024-12-03 23:29:40'),
(205, 15, 5, 5, '2024-12-03 23:29:40'),
(206, 15, 6, 5, '2024-12-03 23:29:40'),
(207, 15, 7, 5, '2024-12-03 23:29:40'),
(208, 15, 8, 5, '2024-12-03 23:29:40'),
(209, 15, 9, 5, '2024-12-03 23:29:40'),
(210, 15, 10, 5, '2024-12-03 23:29:40'),
(211, 15, 11, 5, '2024-12-03 23:29:40'),
(212, 15, 12, 5, '2024-12-03 23:29:40'),
(213, 15, 13, 5, '2024-12-03 23:29:40'),
(214, 15, 14, 5, '2024-12-03 23:29:40'),
(215, 15, 15, 5, '2024-12-03 23:29:40'),
(216, 15, 16, 5, '2024-12-03 23:29:40'),
(217, 15, 17, 5, '2024-12-03 23:29:40'),
(218, 15, 18, 5, '2024-12-03 23:29:40'),
(219, 15, 19, 5, '2024-12-03 23:29:40'),
(220, 15, 21, 5, '2024-12-03 23:29:40'),
(221, 16, 1, 5, '2024-12-03 23:30:00'),
(222, 16, 2, 5, '2024-12-03 23:30:00'),
(223, 16, 3, 5, '2024-12-03 23:30:00'),
(224, 16, 4, 5, '2024-12-03 23:30:00'),
(225, 16, 5, 5, '2024-12-03 23:30:00'),
(226, 16, 6, 5, '2024-12-03 23:30:00'),
(227, 16, 7, 5, '2024-12-03 23:30:00'),
(228, 16, 8, 5, '2024-12-03 23:30:00'),
(229, 16, 9, 5, '2024-12-03 23:30:00'),
(230, 16, 10, 5, '2024-12-03 23:30:00'),
(231, 16, 11, 5, '2024-12-03 23:30:00'),
(232, 16, 12, 5, '2024-12-03 23:30:00'),
(233, 16, 13, 5, '2024-12-03 23:30:00'),
(234, 16, 14, 5, '2024-12-03 23:30:00'),
(235, 16, 15, 5, '2024-12-03 23:30:00'),
(236, 16, 16, 5, '2024-12-03 23:30:00'),
(237, 16, 17, 5, '2024-12-03 23:30:00'),
(238, 16, 18, 5, '2024-12-03 23:30:00'),
(239, 16, 19, 5, '2024-12-03 23:30:00'),
(240, 16, 21, 5, '2024-12-03 23:30:00'),
(241, 17, 1, 5, '2024-12-04 18:46:46'),
(242, 17, 2, 4, '2024-12-04 18:46:46'),
(243, 17, 3, 5, '2024-12-04 18:46:46'),
(244, 17, 4, 4, '2024-12-04 18:46:46'),
(245, 17, 5, 5, '2024-12-04 18:46:46'),
(246, 17, 6, 4, '2024-12-04 18:46:46'),
(247, 17, 7, 5, '2024-12-04 18:46:46'),
(248, 17, 8, 5, '2024-12-04 18:46:46'),
(249, 17, 9, 4, '2024-12-04 18:46:46'),
(250, 17, 10, 5, '2024-12-04 18:46:46'),
(251, 17, 11, 4, '2024-12-04 18:46:46'),
(252, 17, 12, 5, '2024-12-04 18:46:46'),
(253, 17, 13, 4, '2024-12-04 18:46:46'),
(254, 17, 14, 5, '2024-12-04 18:46:46'),
(255, 17, 15, 4, '2024-12-04 18:46:46'),
(256, 17, 16, 5, '2024-12-04 18:46:46'),
(257, 17, 17, 4, '2024-12-04 18:46:46'),
(258, 17, 18, 5, '2024-12-04 18:46:46'),
(259, 17, 19, 4, '2024-12-04 18:46:46'),
(260, 17, 21, 5, '2024-12-04 18:46:46'),
(261, 18, 1, 5, '2024-12-04 23:45:46'),
(262, 18, 2, 5, '2024-12-04 23:45:46'),
(263, 18, 3, 3, '2024-12-04 23:45:46'),
(264, 18, 4, 4, '2024-12-04 23:45:46'),
(265, 18, 5, 5, '2024-12-04 23:45:46'),
(266, 18, 6, 4, '2024-12-04 23:45:46'),
(267, 18, 7, 4, '2024-12-04 23:45:46'),
(268, 18, 8, 3, '2024-12-04 23:45:46'),
(269, 18, 9, 4, '2024-12-04 23:45:46'),
(270, 18, 10, 5, '2024-12-04 23:45:46'),
(271, 18, 11, 4, '2024-12-04 23:45:46'),
(272, 18, 12, 3, '2024-12-04 23:45:46'),
(273, 18, 13, 4, '2024-12-04 23:45:46'),
(274, 18, 14, 4, '2024-12-04 23:45:46'),
(275, 18, 15, 3, '2024-12-04 23:45:46'),
(276, 18, 16, 4, '2024-12-04 23:45:46'),
(277, 18, 17, 4, '2024-12-04 23:45:46'),
(278, 18, 18, 4, '2024-12-04 23:45:46'),
(279, 18, 19, 5, '2024-12-04 23:45:46'),
(280, 18, 21, 5, '2024-12-04 23:45:46'),
(281, 19, 1, 5, '2024-12-05 01:03:04'),
(282, 19, 2, 4, '2024-12-05 01:03:04'),
(283, 19, 3, 5, '2024-12-05 01:03:04'),
(284, 19, 4, 4, '2024-12-05 01:03:04'),
(285, 19, 5, 5, '2024-12-05 01:03:04'),
(286, 19, 6, 4, '2024-12-05 01:03:04'),
(287, 19, 7, 5, '2024-12-05 01:03:04'),
(288, 19, 8, 4, '2024-12-05 01:03:04'),
(289, 19, 9, 5, '2024-12-05 01:03:04'),
(290, 19, 10, 4, '2024-12-05 01:03:04'),
(291, 19, 11, 5, '2024-12-05 01:03:04'),
(292, 19, 12, 4, '2024-12-05 01:03:04'),
(293, 19, 13, 5, '2024-12-05 01:03:04'),
(294, 19, 14, 4, '2024-12-05 01:03:04'),
(295, 19, 15, 5, '2024-12-05 01:03:04'),
(296, 19, 16, 4, '2024-12-05 01:03:04'),
(297, 19, 17, 5, '2024-12-05 01:03:04'),
(298, 19, 18, 4, '2024-12-05 01:03:04'),
(299, 19, 19, 5, '2024-12-05 01:03:04'),
(300, 19, 21, 4, '2024-12-05 01:03:04');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `section_id` int(11) NOT NULL,
  `sections` varchar(11) NOT NULL,
  `status` enum('Active','Archived') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`section_id`, `sections`, `status`) VALUES
(1, 'A', 'Active'),
(2, 'B', 'Active'),
(3, 'C', 'Active'),
(4, 'D', 'Active');

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
(69, 'Clint', NULL, 'Casil', NULL, '9282877890', 1, 'w', 'Sta Rita', 'Sto Domingo', 'Nueva Ecija', '3133', '2003-07-09', 'Male', 'Student', 'rcanlas012003@gmail.com ', '$2y$10$gdLBzV7orFb/aQesuJI2w.lzByPKx75i4Sf7V5bi05aZShkxayuVS', 0),
(70, 'Bain', NULL, 'Hansly', NULL, '9282877890', 1, 'w', 'Sta Rita', 'Sto Domingo', 'Nueva Ecija', '3133', '2003-07-09', 'Male', 'Student', 'villacillomarchie@gmail.com', '$2y$10$yJL8D.IuD/Hp1fw4a.r2CegFSzxMa7wobqRm7vKCVF4ZBgeiMgEDS', 0);

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
(1, 18, 1, 1);

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
(8, 2, 22, 1, '2024-12-03 00:00:00'),
(9, 2, 61, 1, '2024-12-03 00:00:00'),
(11, 1, 67, 1, '2024-12-03 00:00:00');

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
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `class_student`
--
ALTER TABLE `class_student`
  ADD PRIMARY KEY (`class_student_id`),
  ADD KEY `advisory_class_id` (`advisory_class_id`),
  ADD KEY `dep_id` (`dep_id`),
  ADD KEY `sub_id` (`sub_id`),
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `ay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `advisory_class`
--
ALTER TABLE `advisory_class`
  MODIFY `advisory_class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `class_student`
--
ALTER TABLE `class_student`
  MODIFY `class_student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `class_teacher`
--
ALTER TABLE `class_teacher`
  MODIFY `class_teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

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
  MODIFY `eval_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
  MODIFY `ratings_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=301;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `user_class`
--
ALTER TABLE `user_class`
  MODIFY `user_class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_dep`
--
ALTER TABLE `user_dep`
  MODIFY `user_dep_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
-- Constraints for table `class`
--
ALTER TABLE `class`
  ADD CONSTRAINT `class_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `section` (`section_id`);

--
-- Constraints for table `class_student`
--
ALTER TABLE `class_student`
  ADD CONSTRAINT `class_student_ibfk_1` FOREIGN KEY (`advisory_class_id`) REFERENCES `advisory_class` (`advisory_class_id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `class_student_ibfk_2` FOREIGN KEY (`dep_id`) REFERENCES `department` (`dep_id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `class_student_ibfk_3` FOREIGN KEY (`sub_id`) REFERENCES `subject` (`sub_id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `class_student_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE NO ACTION;

--
-- Constraints for table `class_teacher`
--
ALTER TABLE `class_teacher`
  ADD CONSTRAINT `class_teacher_ibfk_1` FOREIGN KEY (`advisory_class_id`) REFERENCES `advisory_class` (`advisory_class_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `class_teacher_ibfk_2` FOREIGN KEY (`sub_id`) REFERENCES `subject` (`sub_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `class_teacher_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `dep_sub`
--
ALTER TABLE `dep_sub`
  ADD CONSTRAINT `dep_sub_ibfk_1` FOREIGN KEY (`dep_id`) REFERENCES `department` (`dep_id`),
  ADD CONSTRAINT `dep_sub_ibfk_2` FOREIGN KEY (`sub_id`) REFERENCES `subject` (`sub_id`);

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
