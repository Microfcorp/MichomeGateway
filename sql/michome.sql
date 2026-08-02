/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: michome
-- ------------------------------------------------------
-- Server version	11.8.6-MariaDB-0+deb13u1 from Debian-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Current Database: `michome`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `michome` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `michome`;

--
-- Table structure for table `UsersVK`
--

DROP TABLE IF EXISTS `UsersVK`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `UsersVK` (
  `ID` bigint(20) NOT NULL,
  `Type` text NOT NULL,
  `Enable` tinyint(1) NOT NULL DEFAULT 1,
  `KeyboardID` int(11) NOT NULL DEFAULT 0,
  `Messanger` enum('VK','TG') NOT NULL DEFAULT 'VK',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Пользователи ВК и ТГ ботов';
/*!40101 SET character_set_client = @saved_cs_client */;
\n\nINSERT INTO `logging` (`id`, `ip`, `type`, `rssi`, `log`, `date`) VALUES (1, 'Gateway', 'Starting', '0', 'Starting', CURTIME());
