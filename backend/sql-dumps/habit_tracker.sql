-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2025 at 03:55 AM
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
-- Database: 'poo'
--
USE poo;

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_completed_goaltime` (INOUT `user_id` INT, INOUT `goal_id` INT, INOUT `goal_time` INT, INOUT `deadline` DATE, IN `end_time` TIME, IN `start_time` TIME, OUT `total_time_completed` TIME)  COMMENT 'gets the amount of time across all related events spent per goal' SELECT *
FROM (
    SELECT
        g.user_id,
        g.goal_id,
        SEC_TO_TIME(SUM(TIME_TO_SEC(e.end_time) - TIME_TO_SEC(e.start_time))) AS total_time_completed,
        g.goal_time,
        g.deadline
    FROM Goal g
    JOIN Event e
      ON g.user_id = e.user_id
     AND g.goal_id = e.goal_id
    GROUP BY g.user_id, g.goal_id, g.goal_time, g.deadline
) AS time_completed$$

DELIMITER ;

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
(13, 3, '2025-11-13', '16:00:00', '17:00:00', 3);

-- --------------------------------------------------------

--
-- Table structure for table `friend`
--

CREATE TABLE `friend` (
  `user_id_1` int(11) NOT NULL,
  `user_id_2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `goal_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goal`
--

INSERT INTO `goal` (`user_id`, `goal_id`, `habit_id`, `deadline`, `goal_time`) VALUES
(1, 1, 1, '2025-11-01', 9),
(1, 2, 2, '2025-11-02', 18),
(1, 3, 3, '2025-11-03', 20),
(2, 1, 1, '2025-11-05', 10),
(2, 2, 2, '2025-11-06', 15),
(3, 1, 1, '2025-11-07', 17),
(3, 2, 2, '2025-11-08', 6),
(3, 3, 3, '2025-11-09', 14),
(3, 4, 4, '2025-11-10', 16),
(4, 1, 1, '2025-11-03', 19),
(4, 2, 2, '2025-11-04', 20),
(4, 3, 3, '2025-11-05', 18),
(5, 1, 1, '2025-11-01', 8),
(5, 2, 2, '2025-11-02', 7),
(5, 3, 3, '2025-11-03', 9),
(6, 1, 1, '2025-11-01', 21),
(6, 2, 2, '2025-11-02', 22),
(6, 3, 3, '2025-11-03', 17),
(7, 1, 1, '2025-11-03', 14),
(7, 2, 2, '2025-11-04', 16),
(7, 3, 3, '2025-11-05', 17),
(8, 1, 1, '2025-11-06', 8),
(8, 2, 2, '2025-11-07', 18),
(8, 3, 3, '2025-11-08', 19),
(9, 1, 1, '2025-11-09', 11),
(9, 2, 2, '2025-11-10', 13),
(9, 3, 3, '2025-11-11', 17),
(9, 4, 4, '2025-11-12', 15),
(10, 1, 1, '2025-11-01', 7),
(10, 2, 2, '2025-11-02', 8),
(10, 3, 3, '2025-11-03', 9),
(11, 1, 1, '2025-11-04', 14),
(11, 2, 2, '2025-11-05', 15),
(11, 3, 3, '2025-11-06', 16),
(11, 4, 4, '2025-11-07', 17),
(12, 1, 1, '2025-11-08', 12),
(12, 2, 2, '2025-11-09', 13),
(12, 3, 3, '2025-11-10', 18),
(13, 1, 1, '2025-11-11', 9),
(13, 2, 2, '2025-11-12', 10),
(13, 3, 3, '2025-11-13', 16);

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
(13, 3, 'Studying for CSO2');

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
-- Stand-in structure for view `time_completed`
-- (See below for the actual view)
--
CREATE TABLE `time_completed` (
`user_id` int(11)
,`goal_id` int(11)
,`total_time_completed` time
,`goal_time` int(11)
,`deadline` date
);

-- --------------------------------------------------------

--
-- Table structure for table `useraccount`
-- already exists in new sql file 

-- CREATE TABLE `useraccount` (
--   `user_id` int(11) NOT NULL,
--   `username` varchar(50) NOT NULL,
--   `password` varchar(100) NOT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --
-- -- Dumping data for table `useraccount`
-- --

-- INSERT INTO `useraccount` (`user_id`, `username`, `password`) VALUES
-- (1, 'Seungcheol', 'passSeungcheol!'),
-- (2, 'Jeonghan', 'passJeonghan!'),
-- (3, 'Joshua', 'passJoshua!'),
-- (4, 'Jun', 'passJun!'),
-- (5, 'Hoshi', 'passHoshi!'),
-- (6, 'Wonwoo', 'passWonwoo!'),
-- (7, 'Woozi', 'passWoozi!'),
-- (8, 'Minghao', 'passMinghao!'),
-- (9, 'Mingyu', 'passMingyu!'),
-- (10, 'DK', 'passDK!'),
-- (11, 'Seungkwan', 'passSeungkwan!'),
-- (12, 'Vernon', 'passVernon!'),
-- (13, 'Dino', 'passDino!');

-- --------------------------------------------------------

--
-- Structure for view `time_completed`
--
DROP TABLE IF EXISTS `time_completed`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `time_completed`  AS SELECT `g`.`user_id` AS `user_id`, `g`.`goal_id` AS `goal_id`, sec_to_time(sum(time_to_sec(`e`.`end_time`) - time_to_sec(`e`.`start_time`))) AS `total_time_completed`, `g`.`goal_time` AS `goal_time`, `g`.`deadline` AS `deadline` FROM (`goal` `g` join `event` `e` on(`g`.`user_id` = `e`.`user_id` and `g`.`goal_id` = `e`.`goal_id`)) GROUP BY `g`.`user_id`, `g`.`goal_id`, `g`.`goal_time`, `g`.`deadline` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`user_id`,`event_id`),
  ADD KEY `user_id` (`user_id`,`goal_id`);

--
-- Indexes for table `friend`
--
ALTER TABLE `friend`
  ADD PRIMARY KEY (`user_id_1`,`user_id_2`),
  ADD KEY `user_id_2` (`user_id_2`);

--
-- Indexes for table `goal`
--
ALTER TABLE `goal`
  ADD PRIMARY KEY (`user_id`,`goal_id`),
  ADD KEY `user_id` (`user_id`,`habit_id`);

--
-- Indexes for table `habit`
--
ALTER TABLE `habit`
  ADD PRIMARY KEY (`user_id`,`habit_id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `roomjoin`
--
ALTER TABLE `roomjoin`
  ADD PRIMARY KEY (`room_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `useraccount`
--
ALTER TABLE `useraccount`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`user_id`,`goal_id`) REFERENCES `goal` (`user_id`, `goal_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `friend`
--
ALTER TABLE `friend`
  ADD CONSTRAINT `friend_ibfk_1` FOREIGN KEY (`user_id_1`) REFERENCES `useraccount` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `friend_ibfk_2` FOREIGN KEY (`user_id_2`) REFERENCES `useraccount` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `goal`
--
ALTER TABLE `goal`
  ADD CONSTRAINT `goal_ibfk_1` FOREIGN KEY (`user_id`,`habit_id`) REFERENCES `habit` (`user_id`, `habit_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `habit`
--
ALTER TABLE `habit`
  ADD CONSTRAINT `habit_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `useraccount` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `useraccount` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `roomjoin`
--
ALTER TABLE `roomjoin`
  ADD CONSTRAINT `roomjoin_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `roomjoin_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `useraccount` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
