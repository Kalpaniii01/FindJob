-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 24, 2024 at 03:54 AM
-- Server version: 8.0.32
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `findjob`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  PRIMARY KEY (`admin_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `user_id`, `full_name`) VALUES
(1, 13, '');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

DROP TABLE IF EXISTS `applications`;
CREATE TABLE IF NOT EXISTS `applications` (
  `application_id` int NOT NULL AUTO_INCREMENT,
  `job_id` int NOT NULL,
  `candidate_id` int NOT NULL,
  `date_applied` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) DEFAULT 'pending',
  PRIMARY KEY (`application_id`),
  KEY `job_id` (`job_id`),
  KEY `candidate_id` (`candidate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`application_id`, `job_id`, `candidate_id`, `date_applied`, `status`) VALUES
(1, 3, 14, '2024-06-22 09:03:35', 'approved'),
(2, 1, 14, '2024-06-22 10:58:15', 'approved'),
(3, 2, 14, '2024-06-22 11:44:33', 'pending'),
(4, 4, 17, '2024-06-22 14:25:56', 'pending'),
(5, 2, 17, '2024-06-22 14:25:58', 'rejected'),
(6, 1, 23, '2024-06-22 16:11:43', 'approved'),
(7, 3, 23, '2024-06-22 16:11:44', 'rejected'),
(8, 2, 23, '2024-06-22 16:11:45', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

DROP TABLE IF EXISTS `candidates`;
CREATE TABLE IF NOT EXISTS `candidates` (
  `candidate_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text,
  `cv_file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`candidate_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`candidate_id`, `user_id`, `full_name`, `phone_number`, `address`, `cv_file`) VALUES
(5, 14, 'Buddhi Chanuka jayashan', '0711866883', '66/A/1, Brahammanagama, Pannipitiya.', '../uploads/cv_files/cv_66769f715b2ef9.46614274.pdf'),
(6, 16, 'Navodya Hewage', '0712345689', 'Senewirathna Niwesa , Bandarawela', 'Buddhi Chanuka Jayashan (1).pdf'),
(7, 17, 'Kavishka Perera', '0123456789', '15/A/1,Brahammanagama,Pannipitiya', 'Chath Induwara CV.pdf'),
(8, 19, 'Koshika Dulanjana', '0777889925', '42/A/1, Ingiriya', 'Chath Induwara CV.pdf'),
(9, 20, 'Asini Diwanjana', '0789456123', '189/D/1, Kandy', 'Buddhi Chanuka Jayashan (1).pdf'),
(10, 21, 'Hirantha Lahiru', '0798456123', '67/2 , Kottawa', 'Buddhi Chanuka Jayashan FrontEnd.pdf'),
(11, 23, 'Kalpani', '0123456789', 'Kandy', 'Chath Induwara CV.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
CREATE TABLE IF NOT EXISTS `companies` (
  `company_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `industry` varchar(100) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`company_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `user_id`, `company_name`, `location`, `industry`, `description`) VALUES
(2, 15, 'Monara Creation Pvt Ltd', 'Homagama', 'IT', 'Sri Lanka Largest Company.'),
(3, 18, 'IFS Sri Lanka', 'Colombo', 'IT', 'Number 1 Company In Sri Lanka'),
(4, 22, 'Navitsa Technologist', 'Colombo', 'IT', 'Australian Company'),
(5, 24, 'Virtusa Sri Lanka', 'Colombo', 'IT', 'Largest IT Company in the world');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `job_id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text,
  `requirements` text,
  `salary` decimal(10,2) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `date_posted` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `location` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`job_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `company_id`, `title`, `description`, `requirements`, `salary`, `category`, `date_posted`, `location`) VALUES
(1, 2, 'Full-Stack Developer', 'We are seeking a highly skilled and experienced Senior Full-Stack Developer to join our dynamic team. The ideal candidate will have a strong background in React, NodeJS, Redux, and PostgreSQL, and be comfortable working across the full Software Development Life Cycle (SDLC).', 'Experience: Minimum of 3-4 years of professional experience in full-stack development, with a strong focus on React, NestJS, Redux, and PostgreSQL.\r\nEducation: Bachelor’s degree in Computer Science, Information Technology, or a related field, or equivalent practical experience (Must have a portfolio of prior work if self-taught)', '250000.00', 'IT', '2024-06-22 05:11:52', '0'),
(2, 2, 'DevOps Engineer', 'The DevSecOps team within A&D is working on facilitating that the whole solution is building together by owning and promoting the use of modern CI-pipelines and automating delivery of various test environments.', 'A degree in Computer Science, Software Engineering or Information Technology,\r\nMinimum 1 to 2 years&#39; experience in a similar role is expected though, fresh graduates with right skillset are welcome.', '250000.00', 'IT', '2024-06-22 05:20:06', '0'),
(3, 2, 'ML Engineer - Group Analytics & AI', 'Study and transform data science prototypes into deployable prototypes by defining decision boundaries\r\nDefine the pre-processing or feature engineering to be done on a given dataset\r\nTrain models, tune their hyperparameters and define validation strategies', 'Bachelor&#39;s degree in Computer Science, Mathematics, Physics or Statistics.\r\nA minimum of 3+ years of industry experiences in Software Engineering or Data Engineering or Data Science/ML. ', '250000.00', 'IT', '2024-06-22 05:33:52', '0'),
(4, 3, 'DevOps Engineer - Linux', 'We are looking for a senior-level DevOps Engineer with Kubernetes, AWS, and bare-metal experience to deliver high-performance, secure products. We are the growth-oriented agile team behind FreeConvert.com, which millions of visitors use every month to transcode, compress, and edit files.', 'Bachelor’s/Master’s degree in Computer Science (or equivalent experience)\r\n3+ years of experience in a DevOps role\r\nExperience working with Kubernetes, Docker, and AWS', '200000.00', 'IT', '2024-06-22 14:24:55', '0'),
(5, 2, 'Backend Developer', 'Writing well-designed, testable and efficient code.\r\nGathering and evaluating user feedback.\r\nWorking as a part of a dynamic team to deliver winning products.', 'You possess a Degree/Diploma in Computer Science, Engineering or related field.\r\nYou have at least 4 years experience, ideally within a Software Engineer role.', '250000.00', 'IT', '2024-06-22 16:14:54', '0');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('candidate','company','admin') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(13, 'Admin', '$2y$10$2HO7tF.Fzcxtr.UsfQexcuS4xCwTFZjp2MiZa1o6r7AELTYoFROu2', 'admin@admin.com', 'admin', '2024-06-21 13:06:42'),
(14, 'Chanuka', '$2y$10$.Aspwn10Vjrnf5r0wdJESO/Ljffqql4Y4oTmAbgR6PcBsloYJQW0a', 'chanuka@gmail.com', 'candidate', '2024-06-21 13:09:16'),
(15, 'Monara', '$2y$10$y2WFUs/9FQfktePu4P/UL.x5glwtNdkUpvorWBbYRXV2AC7D1R3a2', 'monara@gmail.com', 'company', '2024-06-21 17:55:39'),
(16, 'Navodya', '$2y$10$W0l.3ZpGm.aosIhcUin9su27FveG4cPVqazOsfER7vtzIsq7Ph0Ay', 'navodya@gmail.com', 'candidate', '2024-06-22 11:27:37'),
(17, 'Kavishka', '$2y$10$OEpiBqWGjwNKqOTbfAQvx.BHJUcRYeKnpPX39WEIQsg3K8Uwq9wQa', 'kavishka@gmail.com', 'candidate', '2024-06-22 14:10:37'),
(18, 'IFS', '$2y$10$ziqxZ5yWffpOt2geK51fmeo3FFXZHZom6qDW5VNW5CsDPwzqXOJ6.', 'ifs@gmail.com', 'company', '2024-06-22 14:23:35'),
(19, 'Koshika', '$2y$10$C/RJggluHxVZU/4nfAvd1eJ59aaYeqfUVf.8wTGAfyHRZMmjaywVS', 'koshika@gmail.com', 'candidate', '2024-06-22 15:49:43'),
(20, 'Asini', '$2y$10$V0TmuaYUtTQZQGWBCc5K/uqTo7SZ.9GTK7YG1/D2gZLNAReiwDp12', 'asini@gmail.com', 'candidate', '2024-06-22 15:53:49'),
(21, 'Hirantha', '$2y$10$BgzKBVpvg5xn6vPhlYIMe.mjqqgx6llCjzoomqR.FYZ0PBis/V44u', 'hirantha@gmail.com', 'candidate', '2024-06-22 15:57:49'),
(22, 'Navitsa', '$2y$10$/tAbjAyQa6SaVd/jWwE6D.8pTEK8iWyXvQnaMBKTbHz8XAkWH5wXS', 'navitsa@gmail.com', 'company', '2024-06-22 16:01:15'),
(23, 'Kalpani', '$2y$10$3uiGrXyCajZWj5IVIpCOcuFA.I1l.2lyLpoGRgSPbv1i4vOu./Qd.', 'kalpani@gmail.com', 'candidate', '2024-06-22 16:09:05'),
(24, 'Virtusa', '$2y$10$3sR.ydnmmOZ7UDkO4..6gOPOmI8sgz.eS/Ouww63mCrjRgZlaIb42', 'virtusa@gmail.com', 'company', '2024-06-22 16:13:16');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
