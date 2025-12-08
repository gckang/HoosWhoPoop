-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 07, 2025 at 11:52 AM
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
-- Database: `poo`
--

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `day` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `goal_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`user_id`, `event_id`, `day`, `start_time`, `end_time`, `goal_id`) VALUES
(1, 1, '2025-11-01', '09:00:00', '10:00:00', 1),
(1, 2, '2025-11-02', '18:00:00', '19:00:00', 2),
(1, 3, '2025-11-03', '20:00:00', '21:00:00', 3),
(2, 1, '2025-11-05', '10:00:00', '11:00:00', 1),
(2, 2, '2025-11-06', '15:00:00', '16:00:00', 2),
(3, 1, '2025-11-07', '17:00:00', '18:00:00', 1),
(3, 2, '2025-11-08', '06:00:00', '07:00:00', 2),
(3, 3, '2025-11-09', '14:00:00', '15:00:00', 3),
(3, 4, '2025-11-10', '16:00:00', '17:00:00', 4),
(4, 1, '2025-11-03', '19:00:00', '20:00:00', 1),
(4, 2, '2025-11-04', '20:00:00', '21:00:00', 2),
(4, 3, '2025-11-05', '18:00:00', '19:00:00', 3),
(5, 1, '2025-11-01', '08:00:00', '09:00:00', 1),
(5, 2, '2025-11-02', '07:00:00', '08:00:00', 2),
(5, 3, '2025-11-03', '09:00:00', '10:00:00', 3),
(6, 1, '2025-11-01', '21:00:00', '22:00:00', 1),
(6, 2, '2025-11-02', '22:00:00', '23:00:00', 2),
(6, 3, '2025-11-03', '17:00:00', '18:00:00', 3),
(7, 1, '2025-11-03', '14:00:00', '15:00:00', 1),
(7, 2, '2025-11-04', '16:00:00', '17:00:00', 2),
(7, 3, '2025-11-05', '17:00:00', '18:00:00', 3),
(8, 1, '2025-11-06', '08:00:00', '09:00:00', 1),
(8, 2, '2025-11-07', '18:00:00', '19:00:00', 2),
(8, 3, '2025-11-08', '19:00:00', '20:00:00', 3),
(9, 1, '2025-11-09', '11:00:00', '12:00:00', 1),
(9, 2, '2025-11-10', '13:00:00', '14:00:00', 2),
(9, 3, '2025-11-11', '17:00:00', '18:00:00', 3),
(9, 4, '2025-11-12', '15:00:00', '16:00:00', 4),
(10, 1, '2025-11-01', '07:00:00', '08:00:00', 1),
(10, 2, '2025-11-02', '08:00:00', '09:00:00', 2),
(10, 3, '2025-11-03', '09:00:00', '10:00:00', 3),
(11, 1, '2025-11-04', '14:00:00', '15:00:00', 1),
(11, 2, '2025-11-05', '15:00:00', '16:00:00', 2),
(11, 3, '2025-11-06', '16:00:00', '17:00:00', 3),
(11, 4, '2025-11-07', '17:00:00', '18:00:00', 4),
(12, 1, '2025-11-08', '12:00:00', '13:00:00', 1),
(12, 2, '2025-11-09', '13:00:00', '14:00:00', 2),
(12, 3, '2025-11-10', '18:00:00', '19:00:00', 3),
(13, 1, '2025-11-11', '09:00:00', '10:00:00', 1),
(13, 2, '2025-11-12', '10:00:00', '11:00:00', 2),
(13, 3, '2025-11-13', '16:00:00', '17:00:00', 3),
(1, 4, '2025-11-09', '22:11:00', '22:30:00', 4),
(1, 5, '2025-12-08', '02:23:00', '18:23:00', 4),
(1, 6, '2025-12-01', '06:21:00', '08:26:00', 3),
(1, 7, '2025-12-06', '05:42:00', '20:43:00', 1);

--
-- Triggers `event`
--
DELIMITER $$
CREATE TRIGGER `update_hours_after_delete` AFTER DELETE ON `event` FOR EACH ROW BEGIN
    UPDATE goal g
    SET g.hours_completed = (
        SELECT COALESCE(SUM(TIME_TO_SEC(e.end_time) - TIME_TO_SEC(e.start_time)) / 3600, 0.00)
        FROM event e
        WHERE e.user_id = OLD.user_id AND e.goal_id = OLD.goal_id
    )
    WHERE g.user_id = OLD.user_id AND g.goal_id = OLD.goal_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_hours_after_insert` AFTER INSERT ON `event` FOR EACH ROW BEGIN
    UPDATE goal g
    SET g.hours_completed = (
        SELECT COALESCE(SUM(TIME_TO_SEC(e.end_time) - TIME_TO_SEC(e.start_time)) / 3600, 0.00)
        FROM event e
        WHERE e.user_id = NEW.user_id AND e.goal_id = NEW.goal_id
    )
    WHERE g.user_id = NEW.user_id AND g.goal_id = NEW.goal_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_hours_after_update` AFTER UPDATE ON `event` FOR EACH ROW BEGIN
	-- update old goal
    IF OLD.goal_id != NEW.goal_id THEN
        UPDATE goal g
        SET g.hours_completed = (
            SELECT COALESCE(SUM(TIME_TO_SEC(e.end_time) - TIME_TO_SEC(e.start_time)) / 3600, 0.00)
            FROM event e
            WHERE e.user_id = OLD.user_id AND e.goal_id = OLD.goal_id
        )
        WHERE g.user_id = OLD.user_id AND g.goal_id = OLD.goal_id;
    END IF;

    -- update new goal / same goal (duration change)
    UPDATE goal g
    SET g.hours_completed = (
        SELECT COALESCE(SUM(TIME_TO_SEC(e.end_time) - TIME_TO_SEC(e.start_time)) / 3600, 0.00)
        FROM event e
        WHERE e.user_id = NEW.user_id AND e.goal_id = NEW.goal_id
    )
    WHERE g.user_id = NEW.user_id AND g.goal_id = NEW.goal_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `friend`
--

CREATE TABLE `friend` (
  `user_id_1` int(11) NOT NULL,
  `user_id_2` int(11) NOT NULL
) ;

--
-- Dumping data for table `friend`
--

INSERT INTO `friend` (`user_id_1`, `user_id_2`) VALUES
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13);

--
-- Triggers `friend`
--
DELIMITER $$
CREATE TRIGGER `prevent_self_friendship` BEFORE INSERT ON `friend` FOR EACH ROW BEGIN
    IF NEW.user_id_1 = NEW.user_id_2 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'A user cannot be friends with themselves.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `goal`
--

CREATE TABLE `goal` (
  `user_id` int(11) NOT NULL,
  `goal_id` int(11) NOT NULL,
  `habit_id` int(11) NOT NULL,
  `deadline` date NOT NULL,
  `goal_time` int(11) NOT NULL,
  `hours_completed` decimal(7,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goal`
--

INSERT INTO `goal` (`user_id`, `goal_id`, `habit_id`, `deadline`, `goal_time`, `hours_completed`) VALUES
(1, 1, 1, '2025-11-01', 9, 16.02),
(1, 2, 2, '2025-11-02', 18, 1.00),
(1, 3, 3, '2025-11-03', 20, 3.08),
(2, 1, 1, '2025-11-05', 10, 1.00),
(2, 2, 2, '2025-11-06', 15, 1.00),
(3, 1, 1, '2025-11-07', 17, 1.00),
(3, 2, 2, '2025-11-08', 6, 1.00),
(3, 3, 3, '2025-11-09', 14, 1.00),
(3, 4, 4, '2025-11-10', 16, 1.00),
(4, 1, 1, '2025-11-03', 19, 1.00),
(4, 2, 2, '2025-11-04', 20, 1.00),
(4, 3, 3, '2025-11-05', 18, 1.00),
(5, 1, 1, '2025-11-01', 8, 1.00),
(5, 2, 2, '2025-11-02', 7, 1.00),
(5, 3, 3, '2025-11-03', 9, 1.00),
(6, 1, 1, '2025-11-01', 21, 1.00),
(6, 2, 2, '2025-11-02', 22, 1.00),
(6, 3, 3, '2025-11-03', 17, 1.00),
(7, 1, 1, '2025-11-03', 14, 1.00),
(7, 2, 2, '2025-11-04', 16, 1.00),
(7, 3, 3, '2025-11-05', 17, 1.00),
(8, 1, 1, '2025-11-06', 8, 1.00),
(8, 2, 2, '2025-11-07', 18, 1.00),
(8, 3, 3, '2025-11-08', 19, 1.00),
(9, 1, 1, '2025-11-09', 11, 1.00),
(9, 2, 2, '2025-11-10', 13, 1.00),
(9, 3, 3, '2025-11-11', 17, 1.00),
(9, 4, 4, '2025-11-12', 15, 1.00),
(10, 1, 1, '2025-11-01', 7, 1.00),
(10, 2, 2, '2025-11-02', 8, 1.00),
(10, 3, 3, '2025-11-03', 9, 1.00),
(11, 1, 1, '2025-11-04', 14, 1.00),
(11, 2, 2, '2025-11-05', 15, 1.00),
(11, 3, 3, '2025-11-06', 16, 1.00),
(11, 4, 4, '2025-11-07', 17, 1.00),
(12, 1, 1, '2025-11-08', 12, 1.00),
(12, 2, 2, '2025-11-09', 13, 1.00),
(12, 3, 3, '2025-11-10', 18, 1.00),
(13, 1, 1, '2025-11-11', 9, 1.00),
(13, 2, 2, '2025-11-12', 10, 1.00),
(13, 3, 3, '2025-11-13', 16, 1.00),
(1, 4, 4, '2025-11-11', 5, 16.32);

--
-- Triggers `goal`
--
DELIMITER $$
CREATE TRIGGER `check_deadline_before_insert` BEFORE INSERT ON `goal` FOR EACH ROW BEGIN
    IF NEW.deadline < CURRENT_DATE THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Goal deadline has already passed!';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `check_deadline_before_update` BEFORE UPDATE ON `goal` FOR EACH ROW BEGIN
	IF NEW.deadline <> OLD.deadline OR 
    (NEW.deadline IS NULL AND OLD.deadline IS NOT NULL) OR 
    (NEW.deadline IS NOT NULL AND OLD.deadline IS NULL) 
    THEN
    IF NEW.deadline < CURRENT_DATE THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Goal deadline has already passed!';
    END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `habit`
--

CREATE TABLE `habit` (
  `user_id` int(11) NOT NULL,
  `habit_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `habit`
--

INSERT INTO `habit` (`user_id`, `habit_id`, `category`) VALUES
(1, 1, 'Rapping'),
(1, 2, 'Gym'),
(1, 3, 'Gaming'),
(2, 1, 'Gaslighting'),
(2, 2, 'Studying for Cybersecurity'),
(3, 1, 'Playing Guitar'),
(3, 2, 'Running'),
(3, 3, 'Gaslighting'),
(3, 4, 'Baking'),
(4, 1, 'Dancing'),
(4, 2, 'Acting'),
(4, 3, 'Martial Arts'),
(5, 1, 'Dancing'),
(5, 2, 'Stretching'),
(5, 3, 'Becoming a tiger'),
(6, 1, 'Gaming'),
(6, 2, 'Sleeping'),
(6, 3, 'Studying for DSA1'),
(7, 1, 'Producing'),
(7, 2, 'Playing Piano'),
(7, 3, 'Singing'),
(8, 1, 'Drinking tea'),
(8, 2, 'Dancing'),
(8, 3, 'Studying for DSA1'),
(9, 1, 'Cooking'),
(9, 2, 'Photography'),
(9, 3, 'Gym'),
(9, 4, 'Drawing'),
(10, 1, 'Singing'),
(10, 2, 'Throwing it back'),
(10, 3, 'Studying for CSO2'),
(11, 1, 'Singing'),
(11, 2, 'Losing aura'),
(11, 3, 'Running'),
(11, 4, 'Playing volleyball'),
(12, 1, 'Rapping'),
(12, 2, 'Producing'),
(12, 3, 'Watching movies'),
(13, 1, 'Dancing'),
(13, 2, 'Acting'),
(13, 3, 'Studying for CSO2'),
(1, 4, 'Sewing');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `room_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`room_id`, `owner_id`) VALUES
(1, 1),
(2, 1),
(3, 5),
(4, 6);

-- --------------------------------------------------------

--
-- Table structure for table `roomjoin`
--

CREATE TABLE `roomjoin` (
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_rank` varchar(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roomjoin`
--

INSERT INTO `roomjoin` (`room_id`, `user_id`, `user_rank`) VALUES
(1, 1, '0'),
(1, 2, '0'),
(1, 3, '0'),
(1, 4, '0'),
(1, 5, '0'),
(1, 6, '0'),
(1, 7, '0'),
(1, 8, '0'),
(1, 9, '0'),
(1, 10, '0'),
(1, 11, '0'),
(1, 12, '0'),
(1, 13, '0'),
(2, 1, '0'),
(2, 6, '0'),
(2, 9, '0'),
(2, 12, '0'),
(3, 4, '0'),
(3, 5, '0'),
(3, 8, '0'),
(3, 13, '0'),
(4, 2, '0'),
(4, 3, '0'),
(4, 7, '0'),
(4, 10, '0'),
(4, 11, '0');

-- --------------------------------------------------------

--
-- Table structure for table `useraccount`
--

CREATE TABLE `useraccount` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `useraccount`
--

INSERT INTO `useraccount` (`user_id`, `username`, `password`) VALUES
(1, 'Seungcheol', '$2y$10$OKXf5J3SDvW2hK6kMIx8ue3tS4DUmDAzJ6Buoc5u04LNKYxb4AIb2'),
(2, 'Jeonghan', '$2y$10$AeRLLPsVIpxAapcyMAsA8eCkG49AoHefhpQR.Fk9Mmvb1XEtqr0Ym'),
(3, 'Joshua', '$2y$10$PfW6Gqixwx.Co68wwK8msu5mN2GzIC8mjIfcXvrbH2W8x7wrY23bK'),
(4, 'Jun', '$2y$10$GBiPVRL9LOgRIeQ4pxAKOe.MMgbA.e2NsF6LpmE7YpjrXr8y5.zGu'),
(5, 'Hoshi', '$2y$10$YRYiM0fbgCOql30ssJ0ALeUXFVlOnoDuhqTt.3ybSZ9ESHXaqIkwu'),
(6, 'Wonwoo', '$2y$10$4uW.Dl4jZbWV2ZdJ6CD3k.V6Lbz0Ju6jtd32B5HeFZtZeTMnP6bcG'),
(7, 'Woozi', '$2y$10$UU9RD6FwBzreKAUCml/VAuJHfJzlmp1zP025l6sspXcndZaZiRygG'),
(8, 'Minghao', '$2y$10$J9zkUEU.ye4e6iVHzxOpQuF7Kc9lbJKcEEKArgfvWw01LMHLCzHhS'),
(9, 'Mingyu', '$2y$10$LKjKGOFaWEZSl6Z8kY33Teggx6vwG1KHBTaev/yHNY6dkPDJyQkbS'),
(10, 'DK', '$2y$10$eA685tXCf/UexkGnnU1DuephAWsShbtu.7uW06MUsix7sBWRDoBgG'),
(11, 'Seungkwan', '$2y$10$jA7Ra.cjqRib82YlKaKTautGhioALWXoYpq/Tf2z26oysDxAjQuv2'),
(12, 'Vernon', '$2y$10$1FEDn5YOpnHziIFiF4BAYusgv5Zpia69HP3cMZL5wyc9yw0kq4zd6'),
(13, 'Dino', '$2y$10$x8msq52DQsRMpdUZfooWJ.NKUSf0uPGMOgSbpvjSvt.kzZL7TsdJG'),
(14, 'blaire', '$2y$10$4A/0Pdy2RiWNpxuw7Y9pf.6K1wP0xN4h1dnjLPTNuSa/oeqUncQ9K');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `friend`
--
ALTER TABLE `friend`
  ADD KEY `user_id_1` (`user_id_1`,`user_id_2`);

--
-- Indexes for table `useraccount`
--
ALTER TABLE `useraccount`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `useraccount`
--
ALTER TABLE `useraccount`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;
-- =========================================
-- ROLES
-- =========================================
CREATE ROLE IF NOT EXISTS dev_role;
CREATE ROLE IF NOT EXISTS app_role;

-- =========================================
-- DEVELOPER DATABASE USERS
-- =========================================
CREATE USER IF NOT EXISTS 'dev_1' IDENTIFIED BY 'DevPassword1!';
CREATE USER IF NOT EXISTS 'dev_2' IDENTIFIED BY 'DevPassword2!';

GRANT dev_role TO 'dev_1';
GRANT dev_role TO 'dev_2';

-- Developers should have full control of your database schema
GRANT 
  SELECT, INSERT, UPDATE, DELETE,
  CREATE, ALTER, INDEX, DROP
ON `poo`.*
TO dev_role;

-- =========================================
-- APPLICATION DATABASE USER
-- =========================================
CREATE USER IF NOT EXISTS 'habit_app' IDENTIFIED BY 'StrongAppPassword!';
GRANT app_role TO 'habit_app';

-- =========================================
-- LEAST PRIVILEGE ACCESS FOR THE APPLICATION
-- (matched to tables in your dump)
-- =========================================

-- Users table (app must register/login/update profile)
GRANT SELECT, INSERT, UPDATE 
ON `poo`.`useraccount` 
TO app_role;

-- Rooms (app can show and create rooms)
GRANT SELECT, INSERT 
ON `poo`.`room` 
TO app_role;

-- room joins (joining/leaving rooms)
GRANT SELECT, INSERT, DELETE 
ON `poo`.`roomjoin` 
TO app_role;

-- habits (users create, update, delete their habits)
GRANT SELECT, INSERT, UPDATE, DELETE 
ON `poo`.`habit` 
TO app_role;

-- goals (users create, update, delete goals)
GRANT SELECT, INSERT, UPDATE, DELETE 
ON `poo`.`goal` 
TO app_role;

-- events / progress logs (app can read and insert)
GRANT SELECT, INSERT 
ON `poo`.`event` 
TO app_role;

-- Remove advanced permissions from app role
REVOKE ALTER, DROP, CREATE, INDEX 
ON `poo`.*
FROM app_role;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
