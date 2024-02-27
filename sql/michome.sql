-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               10.5.18-MariaDB-0+deb11u1 - Debian 11
-- Операционная система:         debian-linux-gnu
-- HeidiSQL Версия:              11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Дамп структуры базы данных michome
CREATE DATABASE IF NOT EXISTS `michome` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `michome`;

-- Дамп структуры для таблица michome.botcmd
CREATE TABLE IF NOT EXISTS `botcmd` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Enabled` enum('1','0') DEFAULT '0',
  `Name` text DEFAULT NULL,
  `Desc` text DEFAULT NULL,
  `Cmd` text DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Комманды для ботов';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица michome.calendarPresets
CREATE TABLE IF NOT EXISTS `calendarPresets` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id строки',
  `Name` text NOT NULL COMMENT 'Имя пресета',
  `Module` text NOT NULL COMMENT 'ID модуля',
  `Type` text NOT NULL COMMENT 'Тип данных',
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Список пресетов календаря';

-- Экспортируемые данные не выделены.

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
) ENGINE=InnoDB AUTO_INCREMENT=476600 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Данные логирования';

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
) ENGINE=InnoDB AUTO_INCREMENT=900677 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Основная таблица данных';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица michome.mods
CREATE TABLE IF NOT EXISTS `mods` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ModName` text NOT NULL,
  `ParamType` text DEFAULT NULL,
  `ParamName` text DEFAULT NULL,
  `ParamValue` text DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Настройки модов';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица michome.modules
CREATE TABLE IF NOT EXISTS `modules` (
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Настройка модулей';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица michome.rooms
CREATE TABLE IF NOT EXISTS `rooms` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text DEFAULT 'Новая комната',
  `Data` text DEFAULT NULL,
  `Modules` text DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Комнаты';

-- Экспортируемые данные не выделены.

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
  `Enable` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Сценарии автоматизации';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица michome.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID настройки',
  `Name` text NOT NULL DEFAULT '' COMMENT 'Ключ настройки',
  `Value` text DEFAULT '' COMMENT 'Значение настройки',
  `Descreption` text DEFAULT '' COMMENT 'Описание для веба',
  `LastModify` datetime NOT NULL DEFAULT curtime() COMMENT 'Последнее изменение',
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Настройки системы Michome';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица michome.UsersVK
CREATE TABLE IF NOT EXISTS `UsersVK` (
  `ID` bigint(20) NOT NULL,
  `Type` text NOT NULL,
  `Enable` tinyint(1) NOT NULL DEFAULT 1,
  `KeyboardID` int(11) NOT NULL DEFAULT 0,
  `Messanger` enum('VK','TG') NOT NULL DEFAULT 'VK',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Пользователи ВК и ТГ ботов';

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица michome.WebPages
CREATE TABLE IF NOT EXISTS `WebPages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Type` enum('M','R','C','H','G','Null') DEFAULT 'Null',
  `SubType` enum('TextValue','SpanValue','HeaderValue') DEFAULT 'TextValue',
  `Name` text DEFAULT NULL,
  `Value` text DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Настройки web страниц';

-- Экспортируемые данные не выделены.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
