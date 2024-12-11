-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 11, 2024 at 08:56 PM
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
  `year_start` varchar(20) NOT NULL,
  `year_end` varchar(10) NOT NULL,
  `isActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `acad_year`
--

INSERT INTO `acad_year` (`ay_id`, `year_start`, `year_end`, `isActive`) VALUES
(13, '2024', '2025', 1),
(19, '2022', '2023', 0);

-- --------------------------------------------------------

--
-- Table structure for table `advisory_class`
--

CREATE TABLE `advisory_class` (
  `advisory_class_id` int(11) NOT NULL,
  `class_dep_id` int(11) NOT NULL,
  `ay_id` int(11) NOT NULL,
  `sem_id` int(11) NOT NULL,
  `isActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advisory_class`
--

INSERT INTO `advisory_class` (`advisory_class_id`, `class_dep_id`, `ay_id`, `sem_id`, `isActive`) VALUES
(32, 2, 13, 1, 1),
(33, 3, 13, 1, 1),
(34, 4, 13, 1, 1);

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
(12, 1, 15),
(13, 1, 16),
(14, NULL, 17),
(15, NULL, 18),
(16, NULL, 17),
(17, 2, 15),
(18, 2, 16),
(19, NULL, 18);

-- --------------------------------------------------------

--
-- Table structure for table `class_dep`
--

CREATE TABLE `class_dep` (
  `class_dep_id` int(11) NOT NULL,
  `dep_id` int(10) NOT NULL,
  `class_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_dep`
--

INSERT INTO `class_dep` (`class_dep_id`, `dep_id`, `class_id`) VALUES
(2, 1, 12),
(3, 2, 12),
(4, 1, 17);

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
(52, 32, 'Adviser', 1, 17),
(53, 32, 'Subject Teacher', 2, 11),
(54, 32, 'Subject Teacher', 3, 18),
(55, 32, 'Subject Teacher', 4, 19),
(56, 32, 'Subject Teacher', 5, 20),
(57, 32, 'Subject Teacher', 6, 22),
(58, 34, 'Adviser', 1, 73),
(60, 34, 'Subject Teacher', 2, 17),
(61, 34, 'Subject Teacher', 3, 11),
(62, 34, 'Subject Teacher', 4, 18),
(63, 34, 'Subject Teacher', 5, 19),
(64, 34, 'Subject Teacher', 6, 20);

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
(33, 'asdasdsadsa', 5.00, 73, 45, '2024-12-10 17:09:10', 'E0781A04-1733821750'),
(34, 'dsadasdasd', 1.00, 69, 50, '2024-12-11 02:25:08', '640049AA-1733855108'),
(35, 'asdasdasda', 1.55, 69, 48, '2024-12-11 02:25:26', 'C221E0AC-1733855126'),
(36, 'asdasd', 2.20, 69, 46, '2024-12-11 02:25:49', 'EDE44514-1733855149'),
(37, 'asdadad', 1.10, 69, 45, '2024-12-11 02:26:08', '6B9CDE75-1733855168'),
(40, 'asdasasd', 4.45, 69, 49, '2024-12-11 10:14:16', 'B927C85F-1733883256'),
(46, 'asdasdd', 4.45, 69, 54, '2024-12-12 02:53:01', 'B1468A65-1733943181'),
(47, 'asdasdasd', 5.00, 69, 57, '2024-12-12 02:54:29', 'BD8872E4-1733943269'),
(48, '', 1.00, 69, 55, '2024-12-12 02:54:45', '2F880EB4-1733943285'),
(51, 'asdsad', 1.00, 69, 53, '2024-12-12 02:57:03', '00F2A646-1733943423'),
(52, 'asdadda', 1.00, 69, 52, '2024-12-12 02:57:56', '1061FC5F-1733943476'),
(53, 'asdadaa', 5.00, 69, 56, '2024-12-12 02:58:17', '5CCC4793-1733943497'),
(54, 'asdsadasd', 5.00, 73, 53, '2024-12-12 03:18:12', 'A87B025D-1733944692'),
(55, 'asdasdasd', 5.00, 73, 52, '2024-12-12 03:18:31', 'C0AA74CB-1733944711'),
(56, 'asdasdasd', 5.00, 73, 54, '2024-12-12 03:19:00', '52C747CB-1733944740'),
(57, 'asdad', 5.00, 73, 55, '2024-12-12 03:19:16', '444B76C7-1733944756'),
(58, 'asdasdasd', 5.00, 73, 56, '2024-12-12 03:19:28', 'E35AD04F-1733944768'),
(59, 'asdasd', 5.00, 73, 57, '2024-12-12 03:19:45', 'B397957D-1733944785'),
(60, 'asdsda', 5.00, 73, 58, '2024-12-12 03:20:00', '739CB97E-1733944800');

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
(580, 33, 21, 5, '2024-12-10 17:09:10'),
(581, 34, 1, 1, '2024-12-11 02:25:08'),
(582, 34, 2, 1, '2024-12-11 02:25:08'),
(583, 34, 3, 1, '2024-12-11 02:25:08'),
(584, 34, 4, 1, '2024-12-11 02:25:08'),
(585, 34, 5, 1, '2024-12-11 02:25:08'),
(586, 34, 6, 1, '2024-12-11 02:25:08'),
(587, 34, 7, 1, '2024-12-11 02:25:08'),
(588, 34, 8, 1, '2024-12-11 02:25:08'),
(589, 34, 9, 1, '2024-12-11 02:25:08'),
(590, 34, 10, 1, '2024-12-11 02:25:08'),
(591, 34, 11, 1, '2024-12-11 02:25:08'),
(592, 34, 12, 1, '2024-12-11 02:25:08'),
(593, 34, 13, 1, '2024-12-11 02:25:08'),
(594, 34, 14, 1, '2024-12-11 02:25:08'),
(595, 34, 15, 1, '2024-12-11 02:25:08'),
(596, 34, 16, 1, '2024-12-11 02:25:08'),
(597, 34, 17, 1, '2024-12-11 02:25:08'),
(598, 34, 18, 1, '2024-12-11 02:25:08'),
(599, 34, 19, 1, '2024-12-11 02:25:08'),
(600, 34, 21, 1, '2024-12-11 02:25:08'),
(601, 35, 1, 2, '2024-12-11 02:25:26'),
(602, 35, 2, 2, '2024-12-11 02:25:26'),
(603, 35, 3, 1, '2024-12-11 02:25:26'),
(604, 35, 4, 2, '2024-12-11 02:25:26'),
(605, 35, 5, 1, '2024-12-11 02:25:26'),
(606, 35, 6, 2, '2024-12-11 02:25:26'),
(607, 35, 7, 1, '2024-12-11 02:25:26'),
(608, 35, 8, 2, '2024-12-11 02:25:26'),
(609, 35, 9, 1, '2024-12-11 02:25:26'),
(610, 35, 10, 2, '2024-12-11 02:25:26'),
(611, 35, 11, 1, '2024-12-11 02:25:26'),
(612, 35, 12, 2, '2024-12-11 02:25:26'),
(613, 35, 13, 1, '2024-12-11 02:25:26'),
(614, 35, 14, 2, '2024-12-11 02:25:26'),
(615, 35, 15, 1, '2024-12-11 02:25:26'),
(616, 35, 16, 2, '2024-12-11 02:25:26'),
(617, 35, 17, 1, '2024-12-11 02:25:26'),
(618, 35, 18, 2, '2024-12-11 02:25:26'),
(619, 35, 19, 1, '2024-12-11 02:25:26'),
(620, 35, 21, 2, '2024-12-11 02:25:26'),
(621, 36, 1, 2, '2024-12-11 02:25:49'),
(622, 36, 2, 2, '2024-12-11 02:25:49'),
(623, 36, 3, 3, '2024-12-11 02:25:49'),
(624, 36, 4, 2, '2024-12-11 02:25:49'),
(625, 36, 5, 2, '2024-12-11 02:25:49'),
(626, 36, 6, 3, '2024-12-11 02:25:49'),
(627, 36, 7, 2, '2024-12-11 02:25:49'),
(628, 36, 8, 1, '2024-12-11 02:25:49'),
(629, 36, 9, 2, '2024-12-11 02:25:49'),
(630, 36, 10, 2, '2024-12-11 02:25:49'),
(631, 36, 11, 2, '2024-12-11 02:25:49'),
(632, 36, 12, 3, '2024-12-11 02:25:49'),
(633, 36, 13, 2, '2024-12-11 02:25:49'),
(634, 36, 14, 3, '2024-12-11 02:25:49'),
(635, 36, 15, 2, '2024-12-11 02:25:49'),
(636, 36, 16, 3, '2024-12-11 02:25:49'),
(637, 36, 17, 2, '2024-12-11 02:25:49'),
(638, 36, 18, 3, '2024-12-11 02:25:49'),
(639, 36, 19, 2, '2024-12-11 02:25:49'),
(640, 36, 21, 1, '2024-12-11 02:25:49'),
(641, 37, 1, 1, '2024-12-11 02:26:08'),
(642, 37, 2, 1, '2024-12-11 02:26:08'),
(643, 37, 3, 1, '2024-12-11 02:26:08'),
(644, 37, 4, 1, '2024-12-11 02:26:08'),
(645, 37, 5, 2, '2024-12-11 02:26:08'),
(646, 37, 6, 1, '2024-12-11 02:26:08'),
(647, 37, 7, 2, '2024-12-11 02:26:08'),
(648, 37, 8, 1, '2024-12-11 02:26:08'),
(649, 37, 9, 1, '2024-12-11 02:26:08'),
(650, 37, 10, 1, '2024-12-11 02:26:08'),
(651, 37, 11, 1, '2024-12-11 02:26:08'),
(652, 37, 12, 1, '2024-12-11 02:26:08'),
(653, 37, 13, 1, '2024-12-11 02:26:08'),
(654, 37, 14, 1, '2024-12-11 02:26:08'),
(655, 37, 15, 1, '2024-12-11 02:26:08'),
(656, 37, 16, 1, '2024-12-11 02:26:08'),
(657, 37, 17, 1, '2024-12-11 02:26:08'),
(658, 37, 18, 1, '2024-12-11 02:26:08'),
(659, 37, 19, 1, '2024-12-11 02:26:08'),
(660, 37, 21, 1, '2024-12-11 02:26:08'),
(701, 40, 1, 1, '2024-12-11 10:14:16'),
(702, 40, 2, 2, '2024-12-11 10:14:16'),
(703, 40, 3, 3, '2024-12-11 10:14:16'),
(704, 40, 4, 4, '2024-12-11 10:14:16'),
(705, 40, 5, 5, '2024-12-11 10:14:16'),
(706, 40, 6, 4, '2024-12-11 10:14:16'),
(707, 40, 7, 5, '2024-12-11 10:14:16'),
(708, 40, 8, 5, '2024-12-11 10:14:16'),
(709, 40, 9, 5, '2024-12-11 10:14:16'),
(710, 40, 10, 5, '2024-12-11 10:14:16'),
(711, 40, 11, 5, '2024-12-11 10:14:16'),
(712, 40, 12, 5, '2024-12-11 10:14:16'),
(713, 40, 13, 5, '2024-12-11 10:14:16'),
(714, 40, 14, 5, '2024-12-11 10:14:16'),
(715, 40, 15, 5, '2024-12-11 10:14:16'),
(716, 40, 16, 5, '2024-12-11 10:14:16'),
(717, 40, 17, 5, '2024-12-11 10:14:16'),
(718, 40, 18, 5, '2024-12-11 10:14:16'),
(719, 40, 19, 5, '2024-12-11 10:14:16'),
(720, 40, 21, 5, '2024-12-11 10:14:16'),
(821, 46, 1, 5, '2024-12-12 02:53:01'),
(822, 46, 2, 4, '2024-12-12 02:53:01'),
(823, 46, 3, 3, '2024-12-12 02:53:01'),
(824, 46, 4, 4, '2024-12-12 02:53:01'),
(825, 46, 5, 5, '2024-12-12 02:53:01'),
(826, 46, 6, 4, '2024-12-12 02:53:01'),
(827, 46, 7, 5, '2024-12-12 02:53:01'),
(828, 46, 8, 4, '2024-12-12 02:53:01'),
(829, 46, 9, 5, '2024-12-12 02:53:01'),
(830, 46, 10, 4, '2024-12-12 02:53:01'),
(831, 46, 11, 5, '2024-12-12 02:53:01'),
(832, 46, 12, 4, '2024-12-12 02:53:01'),
(833, 46, 13, 5, '2024-12-12 02:53:01'),
(834, 46, 14, 4, '2024-12-12 02:53:01'),
(835, 46, 15, 5, '2024-12-12 02:53:01'),
(836, 46, 16, 4, '2024-12-12 02:53:01'),
(837, 46, 17, 5, '2024-12-12 02:53:01'),
(838, 46, 18, 4, '2024-12-12 02:53:01'),
(839, 46, 19, 5, '2024-12-12 02:53:01'),
(840, 46, 21, 5, '2024-12-12 02:53:01'),
(841, 47, 1, 5, '2024-12-12 02:54:29'),
(842, 47, 2, 5, '2024-12-12 02:54:29'),
(843, 47, 3, 5, '2024-12-12 02:54:29'),
(844, 47, 4, 5, '2024-12-12 02:54:29'),
(845, 47, 5, 5, '2024-12-12 02:54:29'),
(846, 47, 6, 5, '2024-12-12 02:54:29'),
(847, 47, 7, 5, '2024-12-12 02:54:29'),
(848, 47, 8, 5, '2024-12-12 02:54:29'),
(849, 47, 9, 5, '2024-12-12 02:54:29'),
(850, 47, 10, 5, '2024-12-12 02:54:29'),
(851, 47, 11, 5, '2024-12-12 02:54:29'),
(852, 47, 12, 5, '2024-12-12 02:54:29'),
(853, 47, 13, 5, '2024-12-12 02:54:29'),
(854, 47, 14, 5, '2024-12-12 02:54:29'),
(855, 47, 15, 5, '2024-12-12 02:54:29'),
(856, 47, 16, 5, '2024-12-12 02:54:29'),
(857, 47, 17, 5, '2024-12-12 02:54:29'),
(858, 47, 18, 5, '2024-12-12 02:54:29'),
(859, 47, 19, 5, '2024-12-12 02:54:29'),
(860, 47, 21, 5, '2024-12-12 02:54:29'),
(861, 48, 1, 1, '2024-12-12 02:54:45'),
(862, 48, 2, 1, '2024-12-12 02:54:45'),
(863, 48, 3, 1, '2024-12-12 02:54:45'),
(864, 48, 4, 1, '2024-12-12 02:54:45'),
(865, 48, 5, 1, '2024-12-12 02:54:45'),
(866, 48, 6, 1, '2024-12-12 02:54:45'),
(867, 48, 7, 1, '2024-12-12 02:54:45'),
(868, 48, 8, 1, '2024-12-12 02:54:45'),
(869, 48, 9, 1, '2024-12-12 02:54:45'),
(870, 48, 10, 1, '2024-12-12 02:54:45'),
(871, 48, 11, 1, '2024-12-12 02:54:45'),
(872, 48, 12, 1, '2024-12-12 02:54:45'),
(873, 48, 13, 1, '2024-12-12 02:54:45'),
(874, 48, 14, 1, '2024-12-12 02:54:45'),
(875, 48, 15, 1, '2024-12-12 02:54:45'),
(876, 48, 16, 1, '2024-12-12 02:54:45'),
(877, 48, 17, 1, '2024-12-12 02:54:45'),
(878, 48, 18, 1, '2024-12-12 02:54:45'),
(879, 48, 19, 1, '2024-12-12 02:54:45'),
(880, 48, 21, 1, '2024-12-12 02:54:45'),
(921, 51, 1, 1, '2024-12-12 02:57:03'),
(922, 51, 2, 1, '2024-12-12 02:57:03'),
(923, 51, 3, 1, '2024-12-12 02:57:03'),
(924, 51, 4, 1, '2024-12-12 02:57:03'),
(925, 51, 5, 1, '2024-12-12 02:57:03'),
(926, 51, 6, 1, '2024-12-12 02:57:03'),
(927, 51, 7, 1, '2024-12-12 02:57:03'),
(928, 51, 8, 1, '2024-12-12 02:57:03'),
(929, 51, 9, 1, '2024-12-12 02:57:03'),
(930, 51, 10, 1, '2024-12-12 02:57:03'),
(931, 51, 11, 1, '2024-12-12 02:57:03'),
(932, 51, 12, 1, '2024-12-12 02:57:03'),
(933, 51, 13, 1, '2024-12-12 02:57:03'),
(934, 51, 14, 1, '2024-12-12 02:57:03'),
(935, 51, 15, 1, '2024-12-12 02:57:03'),
(936, 51, 16, 1, '2024-12-12 02:57:03'),
(937, 51, 17, 1, '2024-12-12 02:57:03'),
(938, 51, 18, 1, '2024-12-12 02:57:03'),
(939, 51, 19, 1, '2024-12-12 02:57:03'),
(940, 51, 21, 1, '2024-12-12 02:57:03'),
(941, 52, 1, 1, '2024-12-12 02:57:56'),
(942, 52, 2, 1, '2024-12-12 02:57:56'),
(943, 52, 3, 1, '2024-12-12 02:57:56'),
(944, 52, 4, 1, '2024-12-12 02:57:56'),
(945, 52, 5, 1, '2024-12-12 02:57:56'),
(946, 52, 6, 1, '2024-12-12 02:57:56'),
(947, 52, 7, 1, '2024-12-12 02:57:56'),
(948, 52, 8, 1, '2024-12-12 02:57:56'),
(949, 52, 9, 1, '2024-12-12 02:57:56'),
(950, 52, 10, 1, '2024-12-12 02:57:56'),
(951, 52, 11, 1, '2024-12-12 02:57:56'),
(952, 52, 12, 1, '2024-12-12 02:57:56'),
(953, 52, 13, 1, '2024-12-12 02:57:56'),
(954, 52, 14, 1, '2024-12-12 02:57:56'),
(955, 52, 15, 1, '2024-12-12 02:57:56'),
(956, 52, 16, 1, '2024-12-12 02:57:56'),
(957, 52, 17, 1, '2024-12-12 02:57:56'),
(958, 52, 18, 1, '2024-12-12 02:57:56'),
(959, 52, 19, 1, '2024-12-12 02:57:56'),
(960, 52, 21, 1, '2024-12-12 02:57:56'),
(961, 53, 1, 5, '2024-12-12 02:58:17'),
(962, 53, 2, 5, '2024-12-12 02:58:17'),
(963, 53, 3, 5, '2024-12-12 02:58:17'),
(964, 53, 4, 5, '2024-12-12 02:58:17'),
(965, 53, 5, 5, '2024-12-12 02:58:17'),
(966, 53, 6, 5, '2024-12-12 02:58:17'),
(967, 53, 7, 5, '2024-12-12 02:58:17'),
(968, 53, 8, 5, '2024-12-12 02:58:17'),
(969, 53, 9, 5, '2024-12-12 02:58:17'),
(970, 53, 10, 5, '2024-12-12 02:58:17'),
(971, 53, 11, 5, '2024-12-12 02:58:17'),
(972, 53, 12, 5, '2024-12-12 02:58:17'),
(973, 53, 13, 5, '2024-12-12 02:58:17'),
(974, 53, 14, 5, '2024-12-12 02:58:17'),
(975, 53, 15, 5, '2024-12-12 02:58:17'),
(976, 53, 16, 5, '2024-12-12 02:58:17'),
(977, 53, 17, 5, '2024-12-12 02:58:17'),
(978, 53, 18, 5, '2024-12-12 02:58:17'),
(979, 53, 19, 5, '2024-12-12 02:58:17'),
(980, 53, 21, 5, '2024-12-12 02:58:17'),
(981, 54, 1, 5, '2024-12-12 03:18:12'),
(982, 54, 2, 5, '2024-12-12 03:18:12'),
(983, 54, 3, 5, '2024-12-12 03:18:12'),
(984, 54, 4, 5, '2024-12-12 03:18:12'),
(985, 54, 5, 5, '2024-12-12 03:18:12'),
(986, 54, 6, 5, '2024-12-12 03:18:12'),
(987, 54, 7, 5, '2024-12-12 03:18:12'),
(988, 54, 8, 5, '2024-12-12 03:18:12'),
(989, 54, 9, 5, '2024-12-12 03:18:12'),
(990, 54, 10, 5, '2024-12-12 03:18:12'),
(991, 54, 11, 5, '2024-12-12 03:18:12'),
(992, 54, 12, 5, '2024-12-12 03:18:12'),
(993, 54, 13, 5, '2024-12-12 03:18:12'),
(994, 54, 14, 5, '2024-12-12 03:18:12'),
(995, 54, 15, 5, '2024-12-12 03:18:12'),
(996, 54, 16, 5, '2024-12-12 03:18:12'),
(997, 54, 17, 5, '2024-12-12 03:18:12'),
(998, 54, 18, 5, '2024-12-12 03:18:12'),
(999, 54, 19, 5, '2024-12-12 03:18:12'),
(1000, 54, 21, 5, '2024-12-12 03:18:12'),
(1001, 55, 1, 5, '2024-12-12 03:18:31'),
(1002, 55, 2, 5, '2024-12-12 03:18:31'),
(1003, 55, 3, 5, '2024-12-12 03:18:31'),
(1004, 55, 4, 5, '2024-12-12 03:18:31'),
(1005, 55, 5, 5, '2024-12-12 03:18:31'),
(1006, 55, 6, 5, '2024-12-12 03:18:31'),
(1007, 55, 7, 5, '2024-12-12 03:18:31'),
(1008, 55, 8, 5, '2024-12-12 03:18:31'),
(1009, 55, 9, 5, '2024-12-12 03:18:31'),
(1010, 55, 10, 5, '2024-12-12 03:18:31'),
(1011, 55, 11, 5, '2024-12-12 03:18:31'),
(1012, 55, 12, 5, '2024-12-12 03:18:31'),
(1013, 55, 13, 5, '2024-12-12 03:18:31'),
(1014, 55, 14, 5, '2024-12-12 03:18:31'),
(1015, 55, 15, 5, '2024-12-12 03:18:31'),
(1016, 55, 16, 5, '2024-12-12 03:18:31'),
(1017, 55, 17, 5, '2024-12-12 03:18:31'),
(1018, 55, 18, 5, '2024-12-12 03:18:31'),
(1019, 55, 19, 5, '2024-12-12 03:18:31'),
(1020, 55, 21, 5, '2024-12-12 03:18:31'),
(1021, 56, 1, 5, '2024-12-12 03:19:00'),
(1022, 56, 2, 5, '2024-12-12 03:19:00'),
(1023, 56, 3, 5, '2024-12-12 03:19:00'),
(1024, 56, 4, 5, '2024-12-12 03:19:00'),
(1025, 56, 5, 5, '2024-12-12 03:19:00'),
(1026, 56, 6, 5, '2024-12-12 03:19:00'),
(1027, 56, 7, 5, '2024-12-12 03:19:00'),
(1028, 56, 8, 5, '2024-12-12 03:19:00'),
(1029, 56, 9, 5, '2024-12-12 03:19:00'),
(1030, 56, 10, 5, '2024-12-12 03:19:00'),
(1031, 56, 11, 5, '2024-12-12 03:19:00'),
(1032, 56, 12, 5, '2024-12-12 03:19:00'),
(1033, 56, 13, 5, '2024-12-12 03:19:00'),
(1034, 56, 14, 5, '2024-12-12 03:19:00'),
(1035, 56, 15, 5, '2024-12-12 03:19:00'),
(1036, 56, 16, 5, '2024-12-12 03:19:00'),
(1037, 56, 17, 5, '2024-12-12 03:19:00'),
(1038, 56, 18, 5, '2024-12-12 03:19:00'),
(1039, 56, 19, 5, '2024-12-12 03:19:00'),
(1040, 56, 21, 5, '2024-12-12 03:19:00'),
(1041, 57, 1, 5, '2024-12-12 03:19:16'),
(1042, 57, 2, 5, '2024-12-12 03:19:16'),
(1043, 57, 3, 5, '2024-12-12 03:19:16'),
(1044, 57, 4, 5, '2024-12-12 03:19:16'),
(1045, 57, 5, 5, '2024-12-12 03:19:16'),
(1046, 57, 6, 5, '2024-12-12 03:19:16'),
(1047, 57, 7, 5, '2024-12-12 03:19:16'),
(1048, 57, 8, 5, '2024-12-12 03:19:16'),
(1049, 57, 9, 5, '2024-12-12 03:19:16'),
(1050, 57, 10, 5, '2024-12-12 03:19:16'),
(1051, 57, 11, 5, '2024-12-12 03:19:16'),
(1052, 57, 12, 5, '2024-12-12 03:19:16'),
(1053, 57, 13, 5, '2024-12-12 03:19:16'),
(1054, 57, 14, 5, '2024-12-12 03:19:16'),
(1055, 57, 15, 5, '2024-12-12 03:19:16'),
(1056, 57, 16, 5, '2024-12-12 03:19:16'),
(1057, 57, 17, 5, '2024-12-12 03:19:16'),
(1058, 57, 18, 5, '2024-12-12 03:19:16'),
(1059, 57, 19, 5, '2024-12-12 03:19:16'),
(1060, 57, 21, 5, '2024-12-12 03:19:16'),
(1061, 58, 1, 5, '2024-12-12 03:19:28'),
(1062, 58, 2, 5, '2024-12-12 03:19:28'),
(1063, 58, 3, 5, '2024-12-12 03:19:28'),
(1064, 58, 4, 5, '2024-12-12 03:19:28'),
(1065, 58, 5, 5, '2024-12-12 03:19:28'),
(1066, 58, 6, 5, '2024-12-12 03:19:28'),
(1067, 58, 7, 5, '2024-12-12 03:19:28'),
(1068, 58, 8, 5, '2024-12-12 03:19:28'),
(1069, 58, 9, 5, '2024-12-12 03:19:28'),
(1070, 58, 10, 5, '2024-12-12 03:19:28'),
(1071, 58, 11, 5, '2024-12-12 03:19:28'),
(1072, 58, 12, 5, '2024-12-12 03:19:28'),
(1073, 58, 13, 5, '2024-12-12 03:19:28'),
(1074, 58, 14, 5, '2024-12-12 03:19:28'),
(1075, 58, 15, 5, '2024-12-12 03:19:28'),
(1076, 58, 16, 5, '2024-12-12 03:19:28'),
(1077, 58, 17, 5, '2024-12-12 03:19:28'),
(1078, 58, 18, 5, '2024-12-12 03:19:28'),
(1079, 58, 19, 5, '2024-12-12 03:19:28'),
(1080, 58, 21, 5, '2024-12-12 03:19:28'),
(1081, 59, 1, 5, '2024-12-12 03:19:45'),
(1082, 59, 2, 5, '2024-12-12 03:19:45'),
(1083, 59, 3, 5, '2024-12-12 03:19:45'),
(1084, 59, 4, 5, '2024-12-12 03:19:45'),
(1085, 59, 5, 5, '2024-12-12 03:19:45'),
(1086, 59, 6, 5, '2024-12-12 03:19:45'),
(1087, 59, 7, 5, '2024-12-12 03:19:45'),
(1088, 59, 8, 5, '2024-12-12 03:19:45'),
(1089, 59, 9, 5, '2024-12-12 03:19:45'),
(1090, 59, 10, 5, '2024-12-12 03:19:45'),
(1091, 59, 11, 5, '2024-12-12 03:19:45'),
(1092, 59, 12, 5, '2024-12-12 03:19:45'),
(1093, 59, 13, 5, '2024-12-12 03:19:45'),
(1094, 59, 14, 5, '2024-12-12 03:19:45'),
(1095, 59, 15, 5, '2024-12-12 03:19:45'),
(1096, 59, 16, 5, '2024-12-12 03:19:45'),
(1097, 59, 17, 5, '2024-12-12 03:19:45'),
(1098, 59, 18, 5, '2024-12-12 03:19:45'),
(1099, 59, 19, 5, '2024-12-12 03:19:45'),
(1100, 59, 21, 5, '2024-12-12 03:19:45'),
(1101, 60, 1, 5, '2024-12-12 03:20:00'),
(1102, 60, 2, 5, '2024-12-12 03:20:00'),
(1103, 60, 3, 5, '2024-12-12 03:20:00'),
(1104, 60, 4, 5, '2024-12-12 03:20:00'),
(1105, 60, 5, 5, '2024-12-12 03:20:00'),
(1106, 60, 6, 5, '2024-12-12 03:20:00'),
(1107, 60, 7, 5, '2024-12-12 03:20:00'),
(1108, 60, 8, 5, '2024-12-12 03:20:00'),
(1109, 60, 9, 5, '2024-12-12 03:20:00'),
(1110, 60, 10, 5, '2024-12-12 03:20:00'),
(1111, 60, 11, 5, '2024-12-12 03:20:00'),
(1112, 60, 12, 5, '2024-12-12 03:20:00'),
(1113, 60, 13, 5, '2024-12-12 03:20:00'),
(1114, 60, 14, 5, '2024-12-12 03:20:00'),
(1115, 60, 15, 5, '2024-12-12 03:20:00'),
(1116, 60, 16, 5, '2024-12-12 03:20:00'),
(1117, 60, 17, 5, '2024-12-12 03:20:00'),
(1118, 60, 18, 5, '2024-12-12 03:20:00'),
(1119, 60, 19, 5, '2024-12-12 03:20:00'),
(1120, 60, 21, 5, '2024-12-12 03:20:00');

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
(15, 'A', 'Active'),
(16, 'B', 'Active'),
(17, 'G', 'Active'),
(18, 'C', 'Active');

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
(2, 'IT-SP01', 'Social and Professional Issues', 3, 0, 3, 'Social and Professional Issues'),
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
(11, 'Rodolfo', '', 'Dela Cruz', '', '9555726637', 14, 'Zone 4', 'Licaong', 'Science City of Munoz', 'Nueva Ecija', '3119', '2003-03-21', 'Male', 'Instructor', 'repatobain@gmail.com', NULL, 1),
(13, 'Enrique', '', 'Santos', 'Jr.', '9755896058', 13, 'Villa Pinili', 'Bantug', 'Science City of Munoz', 'Nueva Ecija', '3119', '2001-04-22', 'Male', 'Student', 'esantosjr45@gmail.com', NULL, 0),
(15, 'Jenalyn', '', 'Sabado', '', '9555726637', 12, 'Zone 4', 'Licaong', 'Science City of Munoz', 'Nueva Ecija', '3119', '2003-03-21', 'Male', 'Student', 'jenalynsabado29@gmail.com', NULL, 0),
(16, 'Chriszy Mae', '', 'Salamanca', '', '9363640508', 313, 'Zone1', 'San Fabian', 'Sto Domingo', 'Nueva Ecija', '3133', '2002-04-08', 'Female', 'Student', 'chriszymaesalamancalaureta@gmail.com', NULL, 0),
(17, 'Eduard', '', 'Manalili', 'Jr.', '9876234567', 12, 'Zone 2', 'Pinagpanaan', 'Talavera', 'Nueva Ecija', '3112', '1999-03-09', 'Male', 'Instructor', 'endo@gmail.com', NULL, 0),
(18, 'Rey John', '', 'Aguillar', '', '9675452345', 1, 'Zone 3', 'Sumacab', 'cabanatuan', 'Nueva Ecija', '3', '1989-06-08', 'Male', 'Instructor', 'rey@gmail.com', NULL, 0),
(19, 'Johnica', '', 'Alejandro', '', '9374651723', 4, 'Z', 'Jasj', 'cga', 'Nueva Ecija', '2', '1990-11-02', 'Female', 'Instructor', 'jn@gmail.com', NULL, 0),
(20, 'Miguel', '', 'Santos', '', '9283818273', 5, 'a', 's', 'ii', 'Nueva Ecija', '6', '1981-06-01', 'Male', 'Instructor', 'miguel@gmail.com', NULL, 0),
(21, 'Gayle', '', 'De Jesus', '', '9232451234', 7, 'Highway 1', 'Pag asa', 'Talavera', 'Nueva Ecija', '8', '1984-05-05', 'Male', 'Instructor', 'gayle@gmail.com', NULL, 0),
(22, 'Camillo', '', 'Villaviza', '', '9764562534', 1, 'j', 'Jasj', 'cabanatuan', 'Nueva Ecija', '9', '1993-07-03', 'Male', 'Instructor', 'camillo@gmail.com', NULL, 0),
(47, 'Nicka Joy', 'Bernal', 'Dayag', '', '9655883839', 2, 'Purok 2', ' Brgy Villa Santos', 'Science City of Muñoz', 'Nueva Ecija', '3119', '2003-11-05', 'Female', 'Student', 'dayagnickajoy05@gmail.com', NULL, 1),
(48, 'Ricah ', 'Pable', 'Paraguison', '', '9636029948', 2, 'Purok 2', ' Brgy Villa Santos', 'Science City of Muñoz', 'Nueva Ecija', '3119', '2003-10-31', 'Female', 'Student', 'ricahparaguison@gmail.com ', NULL, 0),
(61, 'Johnicaa', NULL, 'Alejandroa', NULL, '9374651723', 4, 'Z', 'Jasj', 'cga', 'Nueva Ecija', '2', '1990-11-02', 'Female', 'Instructor', 'jn@gmail.com', '$2y$10$Ei9TAcRxDImB8xIyfQ8d3.aXcdWRMLPrbxGA6TObdmRf4Cr8pK.5u', 0),
(62, 'Miguela', NULL, 'Santosa', NULL, '9283818273', 5, 'a', 's', 'ii', 'Nueva Ecija', '6', '1981-06-01', 'Male', 'Instructor', 'miguel@gmail.com', '$2y$10$qOt6IMSJAv.cD.UKkYHOEOnxiyNQhmOtCl8QU8xns.E/K4uockYTm', 0),
(63, 'Gaylea', NULL, 'De Jesusa', NULL, '9232451234', 7, 'Highway 1', 'Pag asa', 'Talavera', 'Nueva Ecija', '8', '1984-05-05', 'Male', 'Instructor', 'gayle@gmail.com', '$2y$10$VIcWlDxV0p6RuF2tqFr2ReqnfbbH3snoTgEILcVuVmchNp4Kn1Onu', 0),
(64, 'Camilloa', NULL, 'Villavizaa', NULL, '9764562534', 1, 'j', 'Jasj', 'cabanatuan', 'Nueva Ecija', '9', '1993-07-03', 'Male', 'Instructor', 'camillo@gmail.com', '$2y$10$jOjpiU65lHZA3JikFE0IgOTHVcojhUz/EyG/h2HJrB4NsmK8CPq6u', 0),
(67, 'CHIE', NULL, 'chay', NULL, '9876234567', 12, 'Zone 2', 'Pinagpanaan', 'Talavera', 'Nueva Ecija', '3112', '1999-03-09', 'Male', 'Instructor', 'chiechay111@gmail.com', '$2y$10$o9YhtpIZDjo.gNUYbOiEJuxmFakwiyZq0H9JzD2I/eYWtjs9QOpRi', 0),
(69, 'Clint', NULL, 'Casil', NULL, '9282877890', 1, 'w', 'Sta Rita', 'Sto Domingo', 'Nueva Ecija', '3133', '2003-07-09', 'Male', 'Student', 'rcanlas012003@gmail.com ', '$2y$10$gPSiw4Csme5Vg45DVMiEPuEv9Zhxa5XoQD12SSoXaD9A.Qo9U3anu', 0),
(70, 'Bain', '', 'Hansly', '', '9282877890', 1, 'w', 'Sta Rita', 'Sto Domingo', 'Nueva Ecija', '3133', '2003-07-09', 'Male', 'Student', 'villacillomarchie@gmail.com', '$2y$10$Ckb9cTxtR8s8PFhQ8WgMbudpTg9rJr7oL.C9mSO6OTiVZT7nj.hIi', 0),
(72, 'Clarence', 'Omana', 'Villacillo', 'Jr', '09719989313', 13822, 'NE AirPorT wAy', 'santor', 'portland', 'OR', '3128', '2024-12-06', 'Male', 'Student', 'clarencesk8@gmail.com', '$2y$10$PWkJ6NsiIazRwr6T324V8.Xhs5LWDwUGtb5g.jLBoVa8L7SjHY8RK', 0),
(73, 'Adonis', 'Omana', 'Canlas', 'Jr', '09511018949', 13822, 'NE AirPorT wAy', '', 'Bongabon', 'Nueva Ecija', '3128', '2024-12-06', 'Male', 'Instructor', 'ilovedonkeys483@gmail.com', '$2y$10$ToXTvoZq/gaPuNRWrQ1reeFhYiIO.GJ6VI3Q1BelSRxMdF2q/k6KO', 0),
(76, 'Ralph Ashley', 'Omana', 'O. Canlas', '', '09511018949', 13822, 'NE AirPorT wAy', '', 'Bongabon', 'Nueva Ecija', '3128', '2024-12-08', 'Male', 'Admin', 'floresjohnalber19@gmail.com', '$2y$10$bUMQsdcXNTH0p0VMuYLNd.NCmikD8pLckh3rQ/YRgOhw5fjlNGNNq', 0),
(78, 'lei', NULL, 'punzalan', NULL, '9282877890', 1, 'w', 'Sta Rita', 'Sto Domingo', 'Nueva Ecija', '3133', '2003-07-09', 'Male', 'Student', 'punzalanleicedrick03@gmail.com', '$2y$10$adHTvyb.wBE3TInpJGMBaOx2.P14EmdMwe58Ihlt0vLpolDmuGRBG', 0),
(79, 'felix', NULL, 'balmores', NULL, '9282877890', 1, 'w', 'Sta Rita', 'Sto Domingo', 'Nueva Ecija', '3133', '2003-07-09', 'Male', 'Student', 'felixbalmores08@gmail.com', '$2y$10$tIDFCKzJUqJY0vzfUrjU/exyGl4vbD2U6HkQ90hN1eHIO2JRo3h6C', 0),
(80, 'John', '', 'Doe', '', '09511018949', 775, 'rizal st', 'santor', 'Bongabon', 'Samar', '3128', '2024-12-11', 'Male', 'Student', 'chiyo@gmail.com', '$2y$10$q9B/83bMDJxDeHV/c7Djfeo4HjVEfK6/qAnPycN/.VB2Cx1Uy3AHO', 0),
(81, 'John', 'Miguel', 'Villena', '', '09511018949', 775, 'Purok 5', 'Palestina', 'San Jose', 'Nueva Ecija', '123', '2024-12-11', 'Male', 'Instructor', 'TimothyBaxter@gmail.com', '$2y$10$J0LdndvBLVvidTp1n1jNIeKYeCbcVp9IaP0AiWuVqEZ16fAy9bsWm', 0),
(82, 'John1', 'Miguel1', 'John1', '', '09511018949', 775, 'Purok 5', 'Palestina', 'San Jose', 'Nueva Ecija', '3128', '2024-12-11', 'Male', 'Instructor', 'canlasralph@gmail.com', '$2y$10$/MNVZBoAz/0MsyP8cP/PkegpOwQz2d8GMfdiNZF9h03vtYUuA0s52', 0),
(83, 'John', 'Miguel', 'John', '', '09511018949', 775, 'Purok 5', 'Palestina', 'San Jose', 'Nueva Ecija', '3128', '2024-12-11', 'Male', 'Admin', 'genesis.nav19@gmail.com', '$2y$10$LGFe.Mjpk01/CT0LAbro4OayQRSSf6yaygJLeimD/ecaKMnJ.qWHW', 0);

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
(28, 13, 32, 1),
(29, 13, 32, 1),
(30, 13, 32, 1),
(31, 13, 32, 1),
(32, 13, 32, 1),
(33, 13, 32, 1),
(34, 69, 32, 1),
(35, 69, 32, 1),
(36, 69, 32, 1),
(37, 69, 32, 1),
(38, 69, 32, 1),
(39, 69, 32, 1);

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
(20, 2, 47, 1, '2024-12-07 00:00:00'),
(21, 2, 48, 1, '2024-12-07 00:00:00'),
(30, 1, 16, 1, '2024-12-11 00:00:00'),
(31, 1, 69, 1, '2024-12-11 00:00:00'),
(32, 1, 70, 1, '2024-12-11 00:00:00'),
(33, 1, 76, 1, '2024-12-11 00:00:00'),
(35, 1, 78, 1, '2024-12-11 00:00:00');

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
  ADD KEY `class_id` (`class_dep_id`),
  ADD KEY `ay_id` (`ay_id`),
  ADD KEY `sem_id` (`sem_id`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `class_dep`
--
ALTER TABLE `class_dep`
  ADD PRIMARY KEY (`class_dep_id`);

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
  MODIFY `ay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `advisory_class`
--
ALTER TABLE `advisory_class`
  MODIFY `advisory_class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `class_dep`
--
ALTER TABLE `class_dep`
  MODIFY `class_dep_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `class_teacher`
--
ALTER TABLE `class_teacher`
  MODIFY `class_teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

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
  MODIFY `eval_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

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
  MODIFY `ratings_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1121;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `user_class`
--
ALTER TABLE `user_class`
  MODIFY `user_class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `user_dep`
--
ALTER TABLE `user_dep`
  MODIFY `user_dep_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `advisory_class`
--
ALTER TABLE `advisory_class`
  ADD CONSTRAINT `advisory_class_ibfk_3` FOREIGN KEY (`sem_id`) REFERENCES `semester` (`sem_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
