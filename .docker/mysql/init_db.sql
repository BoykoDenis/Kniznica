-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 02, 2021 at 04:02 PM
-- Server version: 5.7.31
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `boykovolf`
--

-- --------------------------------------------------------

--
-- Table structure for table `ads`
--

DROP TABLE IF EXISTS `ads`;
CREATE TABLE IF NOT EXISTS `ads` (
  `addID` int(11) NOT NULL AUTO_INCREMENT,
  `AddName` varchar(30) COLLATE utf8_bin NOT NULL,
  `AddImage` varchar(100) COLLATE utf8_bin NOT NULL,
  `url_link` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`addID`),
  UNIQUE KEY `AddImage` (`AddImage`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `ads`
--

INSERT INTO `ads` (`addID`, `AddName`, `AddImage`, `url_link`) VALUES
(1, 'world of tanks', 'images/wot.jpg', 'https://www.googleadservices.com/pagead/aclk?sa=L&ai=DChcSEwj5woPhicHtAhWY5HcKHXmxAT8YABAAGgJlZg&ohost=www.google.com&cid=CAESQOD2J_imZx3xOYrFyFIPtA2bJHmEJiO909JvwsQh5YWXmOzP1p19do5Mt8iciU8Y0Dc3wit3qBObiSh14KrGh8s&sig=AOD64_0nOkURfpEHkYrM-jfL-mX16lsp4w&q&adurl&ved=2ahUKEwjVufrgicHtAhXF-6QKHX80AzQQ0Qx6BAgHEAE'),
(2, 'WarThunder', 'images/War_Thunder.png', 'https://www.googleadservices.com/pagead/aclk?sa=L&ai=DChcSEwiH3ajwicHtAhUGqncKHWYJC6MYABAAGgJlZg&ohost=www.google.com&cid=CAESQOD2SyYzpyBb8GFnmHvONWz1bRNpbMlaRn3fhpLZu_-dwAGP21UPHzcsb8d78_rrEPYxvyWAlMIpEiaxmMFWs_0&sig=AOD64_0BjOZhxvotzPhj0lPoDqgjG-qbww&q&adurl&ved=2ahUKEwj1p5_wicHtAhUyMewKHX2KAs8Q0Qx6BAgaEAE');

-- --------------------------------------------------------

--
-- Table structure for table `authorbook`
--

DROP TABLE IF EXISTS `authorbook`;
CREATE TABLE IF NOT EXISTS `authorbook` (
  `aid` int(11) NOT NULL,
  `bid` int(11) NOT NULL,
  PRIMARY KEY (`aid`,`bid`),
  KEY `bid(a-b)` (`bid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `authorbook`
--

INSERT INTO `authorbook` (`aid`, `bid`) VALUES
(5, 1),
(4, 2),
(4, 3),
(5, 4);

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

DROP TABLE IF EXISTS `authors`;
CREATE TABLE IF NOT EXISTS `authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) COLLATE utf8_bin NOT NULL,
  `date_of_birth` date NOT NULL,
  `date_of_death` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `AuthorName` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id`, `name`, `date_of_birth`, `date_of_death`) VALUES
(4, 'Isak Asimov', '1976-12-08', '1910-12-01'),
(5, 'Arthur Klark', '2020-12-07', '2020-12-03'),
(6, 'Vasia Pupkin Vcerarodilsovic', '2020-12-01', '2020-12-02'),
(7, 'Pavlik Morozov', '2020-12-02', '2020-12-03'),
(8, 'QwerAsdf', '2020-12-10', '2030-12-11'),
(9, 'QwerAsdf', '2020-12-10', '2030-12-11'),
(12, 'Vasia', '2030-12-10', '2077-12-12'),
(13, 'QwerAsdf', '2020-12-10', '2030-12-11'),
(14, 'Bill Gates Jr', '1961-02-03', '2040-02-03'),
(15, 'Blabla blabla', '1976-12-08', '1900-12-01'),
(16, 'Jules Di Verne', '2030-12-10', '2077-12-12'),
(17, 'Jules Di Verne', '2030-12-10', '2077-12-12'),
(19, 'Jules Di Verne', '2030-12-10', '2077-12-12'),
(20, 'Blabla blabla', '1976-12-08', '1900-12-01'),
(21, 'gggg', '2030-12-10', '2077-12-12');

-- --------------------------------------------------------

--
-- Table structure for table `bookgenre`
--

DROP TABLE IF EXISTS `bookgenre`;
CREATE TABLE IF NOT EXISTS `bookgenre` (
  `bid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  PRIMARY KEY (`bid`,`gid`),
  KEY `gid` (`gid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_bin NOT NULL,
  `date_published` date NOT NULL,
  `isbn` bigint(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `date_published`, `isbn`) VALUES
(1, 'Roadside Picnic', '1987-07-07', 9781613743416),
(2, 'Foundation', '1978-08-08', 7115873),
(3, 'Diabologic', '1999-09-09', 55828),
(4, 'The Grim Ripper', '1922-12-12', 12121212);

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

DROP TABLE IF EXISTS `genres`;
CREATE TABLE IF NOT EXISTS `genres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gname` varchar(30) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `GenreName` (`gname`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `gname`) VALUES
(3, 'Action and adventure'),
(2, 'Fantasy'),
(4, 'Horror'),
(5, 'Mystery'),
(1, 'Sci-fi'),
(6, 'Western');

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

DROP TABLE IF EXISTS `photos`;
CREATE TABLE IF NOT EXISTS `photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_bin NOT NULL,
  `url` text COLLATE utf8_bin NOT NULL,
  `aid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author's photo` (`aid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `rid` int(11) NOT NULL AUTO_INCREMENT,
  `rtext` text COLLATE utf8_bin NOT NULL,
  `bid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`rid`),
  KEY `bid(r)` (`bid`),
  KEY `uid(r)` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `userbook`
--

DROP TABLE IF EXISTS `userbook`;
CREATE TABLE IF NOT EXISTS `userbook` (
  `uid` int(11) NOT NULL,
  `bid` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`bid`),
  KEY `bid(u-b)` (`bid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uname` varchar(30) COLLATE utf8_bin NOT NULL,
  `uemail` text COLLATE utf8_bin NOT NULL,
  `upass` varchar(40) COLLATE utf8_bin NOT NULL,
  `privileges` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Username` (`uname`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uname`, `uemail`, `upass`, `privileges`) VALUES
(1, 'Doctor', 'denya.boyko@gmail.com', 'apple', 1),
(2, 'SteamPunk123', 'arrayon@hotmail.com', 'filip', 0),
(3, 'amthos', 'volf.t@gjh.sk', 'tomas', 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `authorbook`
--
ALTER TABLE `authorbook`
  ADD CONSTRAINT `aid(a-b)` FOREIGN KEY (`aid`) REFERENCES `authors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bid(a-b)` FOREIGN KEY (`bid`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bookgenre`
--
ALTER TABLE `bookgenre`
  ADD CONSTRAINT `bid` FOREIGN KEY (`bid`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gid` FOREIGN KEY (`gid`) REFERENCES `genres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `author's photo` FOREIGN KEY (`aid`) REFERENCES `authors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `bid(r)` FOREIGN KEY (`bid`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `uid(r)` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `userbook`
--
ALTER TABLE `userbook`
  ADD CONSTRAINT `bid(u-b)` FOREIGN KEY (`bid`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `uid(u-b)` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
