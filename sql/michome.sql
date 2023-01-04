-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               10.5.15-MariaDB-0+deb11u1 - Debian 11
-- Операционная система:         debian-linux-gnu
-- HeidiSQL Версия:              12.3.0.6589
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Дамп структуры базы данных michome
CREATE DATABASE IF NOT EXISTS `michome` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `michome`;

-- Дамп структуры для таблица michome.logging
CREATE TABLE IF NOT EXISTS `logging` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='Основная таблица логов';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица michome.michom
CREATE TABLE IF NOT EXISTS `michom` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='Основная таблица данных';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица michome.modules
CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` text NOT NULL,
  `type` text NOT NULL,
  `mID` text NOT NULL,
  `urls` text NOT NULL DEFAULT '',
  `setting` text NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='Настройки модулей';

INSERT INTO `modules` (`id`, `ip`, `type`, `mID`, `urls`, `setting`) VALUES
	(0, 'localhost', 'Geteway', 'Geteway', '/=главная', ' ');


-- Дамп структуры для таблица michome.scenes
CREATE TABLE IF NOT EXISTS `scenes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `TStart` time NOT NULL,
  `TEnd` time NOT NULL,
  `Module` text NOT NULL,
  `Data` text NOT NULL,
  `NData` text NOT NULL,
  `CSE` time NOT NULL,
  `Timeout` int(11) NOT NULL DEFAULT 5,
  `Enable` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='Настройки скриптов';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица michome.UsersVK
CREATE TABLE IF NOT EXISTS `UsersVK` (
  `ID` bigint(20) NOT NULL,
  `Type` text NOT NULL,
  `Enable` tinyint(1) NOT NULL DEFAULT 1,
  `KeyboardID` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Настройка VK бота';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица michome.WebPages
CREATE TABLE IF NOT EXISTS `WebPages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Type` enum('M','R','C','Null') DEFAULT 'Null',
  `SubType` enum('TextValue') DEFAULT NULL,
  `Name` text DEFAULT NULL,
  `Value` text DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='Настройки web страниц';

-- Экспортируемые данные не выделены.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
