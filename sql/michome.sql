-- MariaDB dump 10.19  Distrib 10.5.18-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: michome
-- ------------------------------------------------------
-- Server version	10.5.18-MariaDB-0+deb11u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UsersVK` (
  `ID` bigint(20) NOT NULL,
  `Type` text NOT NULL,
  `Enable` tinyint(1) NOT NULL DEFAULT 1,
  `KeyboardID` int(11) NOT NULL DEFAULT 0,
  `Messanger` enum('VK','TG') NOT NULL DEFAULT 'VK',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Пользователи ВК и ТГ ботов';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `WebPages`
--

DROP TABLE IF EXISTS `WebPages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `WebPages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Type` enum('M','R','C','H','G','Null') DEFAULT 'Null',
  `SubType` enum('TextValue','SpanValue','HeaderValue') DEFAULT 'TextValue',
  `Name` text DEFAULT NULL,
  `Value` text DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Настройки web страниц';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `botcmd`
--

DROP TABLE IF EXISTS `botcmd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `botcmd` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Enabled` enum('1','0') DEFAULT '0',
  `Name` text DEFAULT NULL,
  `Desc` text DEFAULT NULL,
  `Cmd` text DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Комманды для ботов';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendarPresets`
--

DROP TABLE IF EXISTS `calendarPresets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendarPresets` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id строки',
  `Name` text NOT NULL COMMENT 'Имя пресета',
  `Module` text NOT NULL COMMENT 'ID модуля',
  `Type` text NOT NULL COMMENT 'Тип данных',
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Список пресетов календаря';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logging`
--

DROP TABLE IF EXISTS `logging`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logging` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` text NOT NULL,
  `type` text NOT NULL,
  `rssi` text NOT NULL,
  `log` text NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Данные логирования';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `michom`
--

DROP TABLE IF EXISTS `michom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `michom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `data` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `temp` float NOT NULL DEFAULT 0,
  `humm` float NOT NULL DEFAULT 0,
  `dawlen` float NOT NULL DEFAULT 0,
  `visota` float NOT NULL DEFAULT 0,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Основная таблица данных';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mods`
--

DROP TABLE IF EXISTS `mods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mods` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ModName` text NOT NULL,
  `ParamType` text DEFAULT NULL,
  `ParamName` text DEFAULT NULL,
  `ParamValue` text DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Настройки модов';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mac` text NOT NULL,
  `ip` text NOT NULL,
  `type` text NOT NULL,
  `mID` text NOT NULL,
  `urls` text NOT NULL DEFAULT '',
  `setting` text NOT NULL DEFAULT '',
  `msetting` text DEFAULT NULL,
  `laststart` datetime DEFAULT curtime(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Настройка модулей';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rooms` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text DEFAULT 'Новая комната' COMMENT 'Название комнаты',
  `Data` text DEFAULT NULL COMMENT 'Текст комнаты',
  `Modules` text DEFAULT NULL COMMENT 'Список модулей в комнате',
  `mName` text DEFAULT 'leroom' COMMENT 'Название для модулей',
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Комнаты';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scenes`
--

DROP TABLE IF EXISTS `scenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scenes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `TStart` time NOT NULL,
  `TEnd` time NOT NULL,
  `Module` text NOT NULL,
  `Data` text NOT NULL,
  `NData` text NOT NULL,
  `CSE` time NOT NULL,
  `Timeout` int(11) NOT NULL DEFAULT 5,
  `Enable` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Сценарии автоматизации';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID настройки',
  `Name` text NOT NULL DEFAULT '' COMMENT 'Ключ настройки',
  `Value` text DEFAULT '' COMMENT 'Значение настройки',
  `Descreption` text DEFAULT '' COMMENT 'Описание для веба',
  `LastModify` datetime NOT NULL DEFAULT curtime() COMMENT 'Последнее изменение',
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Настройки системы Michome';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;



INSERT INTO `logging` (`id`, `ip`, `type`, `rssi`, `log`, `date`) VALUES (1, 'Gateway', 'Starting', '0', 'Starting', CURTIME());
