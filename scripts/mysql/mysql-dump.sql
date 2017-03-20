# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 192.168.99.100 (MySQL 5.7.17)
# Database: reportbook_dev
# Generation Time: 2017-03-09 14:52:26 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table comment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comment`;

CREATE TABLE `comment` (
  `id` varchar(255) NOT NULL DEFAULT '',
  `content` text,
  `date` datetime NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT '',
  `userId` varchar(255) NOT NULL DEFAULT '',
  `reportId` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `reportId` (`reportId`),
  KEY `userId` (`userId`),
  CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`id`),
  CONSTRAINT `comment_ibfk_3` FOREIGN KEY (`reportId`) REFERENCES `report` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table profile
# ------------------------------------------------------------

DROP TABLE IF EXISTS `profile`;

CREATE TABLE `profile` (
  `userId` varchar(255) NOT NULL DEFAULT '',
  `forename` varchar(255) DEFAULT '',
  `surname` varchar(255) DEFAULT '',
  `dateOfBirth` date DEFAULT NULL,
  `school` varchar(255) DEFAULT '',
  `grade` varchar(255) DEFAULT '',
  `jobTitle` varchar(255) DEFAULT '',
  `trainingYear` int(1) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `startOfTraining` date DEFAULT NULL,
  `image` blob,
  `imageType` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`userId`),
  CONSTRAINT `profile_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table report
# ------------------------------------------------------------

DROP TABLE IF EXISTS `report`;

CREATE TABLE `report` (
  `id` varchar(255) NOT NULL DEFAULT '',
  `content` text,
  `date` date NOT NULL,
  `calendarWeek` int(2) NOT NULL,
  `calendarYear` year(4) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT '',
  `category` varchar(255) NOT NULL DEFAULT '',
  `traineeId` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `traineeId` (`traineeId`),
  CONSTRAINT `report_ibfk_1` FOREIGN KEY (`traineeId`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `roleName` varchar(255) NOT NULL DEFAULT '',
  `roleStatus` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
