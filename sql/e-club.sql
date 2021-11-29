-- Adminer 4.7.7 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `divisions`;
CREATE TABLE `divisions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nameDivision` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `divisions` (`id`, `nameDivision`) VALUES
(1,	'Muži A'),
(2,	'Muži B'),
(3,	'Stará garda'),
(4,	'Starší dorost'),
(5,	'Mladší dorost'),
(6,	'Starší žáci'),
(7,	'Mladší žáci'),
(8,	'Přípravka');

DROP TABLE IF EXISTS `finer`;
CREATE TABLE `finer` (
  `typeFines_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `paid` tinyint(4) NOT NULL,
  PRIMARY KEY (`typeFines_id`,`users_id`),
  KEY `users_id` (`users_id`),
  CONSTRAINT `finer_ibfk_1` FOREIGN KEY (`typeFines_id`) REFERENCES `typefines` (`id`),
  CONSTRAINT `finer_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `matches`;
CREATE TABLE `matches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idTeam1` int(11) NOT NULL,
  `idTeam2` int(11) NOT NULL,
  `idDivision` int(11) NOT NULL,
  `date` date NOT NULL,
  `team1Score` smallint(1) NOT NULL,
  `team2Score` smallint(1) NOT NULL,
  `end` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`,`idTeam1`,`idTeam2`,`idDivision`),
  KEY `idTeam1` (`idTeam1`),
  KEY `idTeam2` (`idTeam2`),
  KEY `idDivision` (`idDivision`),
  CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`idTeam1`) REFERENCES `teams` (`id`),
  CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`idTeam2`) REFERENCES `teams` (`id`),
  CONSTRAINT `matches_ibfk_3` FOREIGN KEY (`idDivision`) REFERENCES `divisions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleName` varchar(225) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `roles` (`id`, `roleName`) VALUES
(1,	'Superadmin'),
(2,	'Admin'),
(3,	'Normální uživatel');

DROP TABLE IF EXISTS `teams`;
CREATE TABLE `teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teamName` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `typefines`;
CREATE TABLE `typefines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nameFine` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `tel` varchar(11) COLLATE utf8_czech_ci DEFAULT NULL,
  `idRole` int(11) NOT NULL,
  `idDivision` int(11) NOT NULL,
  PRIMARY KEY (`id`,`idRole`,`idDivision`),
  KEY `idRole` (`idRole`),
  KEY `idDivision` (`idDivision`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`idRole`) REFERENCES `roles` (`id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`idDivision`) REFERENCES `divisions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `users` (`id`, `username`, `password`, `name`, `surname`, `email`, `tel`, `idRole`, `idDivision`) VALUES
(1,	'tomborki',	'$2y$10$okYrpDvCDRhY0SrblNUWXOpCZr7im55qqTqRrJ31bVfWI5RTQ0Erm',	'Tomáš',	'Borkovec',	'tomasborki@gmail.com',	'720141853',	1,	1),
(2,	'terka',	'$2y$10$okYrpDvCDRhY0SrblNUWXOpCZr7im55qqTqRrJ31bVfWI5RTQ0Erm',	'Tereza',	'Richterová',	'terka@gmail.com',	'789456123',	2,	2),
(3,	'filip',	'$2y$10$okYrpDvCDRhY0SrblNUWXOpCZr7im55qqTqRrJ31bVfWI5RTQ0Erm',	'Filip',	'Borkovec',	'filip@gmail.com',	'789456123',	3,	3);

-- 2021-11-29 21:16:42
