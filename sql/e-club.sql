-- Adminer 4.7.7 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `divisions`;
CREATE TABLE `divisions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nameDivision` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `chief` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `telContact` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `emailContact` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `divisions` (`id`, `nameDivision`, `chief`, `telContact`, `emailContact`) VALUES
(1,	'Muži A',	'Josef Dobrý',	'+420153698226',	'josefdobry@gmail.com'),
(2,	'Muži B',	'František Novák',	'+420157854256',	'frno@gmail.com'),
(4,	'Starší dorost',	'Tomáš Bohdan',	'+420345728473',	NULL),
(5,	'Mladší dorost',	'',	'',	''),
(6,	'Starší žáci',	'',	'',	''),
(7,	'Mladší žáci',	'',	'',	''),
(8,	'Přípravka',	'',	'',	''),
(29,	'Stará garda',	'Jiří Gertner',	'+420159875552',	'gertnerj@centrum.cz');

DROP TABLE IF EXISTS `finer`;
CREATE TABLE `finer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeFines_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `paid` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `typeFines_id` (`typeFines_id`),
  KEY `users_id` (`users_id`),
  CONSTRAINT `finer_ibfk_1` FOREIGN KEY (`typeFines_id`) REFERENCES `typefines` (`id`),
  CONSTRAINT `finer_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `finer` (`id`, `typeFines_id`, `users_id`, `date`, `paid`) VALUES
(1,	1,	1,	'2021-11-29 23:05:47',	0),
(2,	2,	1,	'2021-11-29 23:06:02',	0),
(3,	1,	2,	'2021-11-29 23:06:18',	0);

DROP TABLE IF EXISTS `matches`;
CREATE TABLE `matches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idTeam1` int(11) NOT NULL,
  `idTeam2` int(11) NOT NULL,
  `idDivision` int(11) NOT NULL,
  `date` date NOT NULL,
  `team1Score` smallint(1) DEFAULT NULL,
  `team2Score` smallint(1) DEFAULT NULL,
  `end` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`,`idTeam1`,`idTeam2`,`idDivision`),
  KEY `idTeam1` (`idTeam1`),
  KEY `idTeam2` (`idTeam2`),
  KEY `idDivision` (`idDivision`),
  CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`idTeam1`) REFERENCES `teams` (`id`),
  CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`idTeam2`) REFERENCES `teams` (`id`),
  CONSTRAINT `matches_ibfk_3` FOREIGN KEY (`idDivision`) REFERENCES `divisions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `matches` (`id`, `idTeam1`, `idTeam2`, `idDivision`, `date`, `team1Score`, `team2Score`, `end`) VALUES
(1,	1,	2,	1,	'2021-11-29',	2,	1,	1),
(2,	5,	3,	1,	'2021-11-29',	0,	1,	1),
(3,	7,	9,	1,	'2021-11-29',	NULL,	NULL,	0);

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

INSERT INTO `teams` (`id`, `teamName`) VALUES
(1,	'Zruč B'),
(2,	'Sokol Lhota B'),
(3,	'Rapid Plzeň B'),
(4,	'Plzeň-Letná'),
(5,	'Plzeň-Černice B'),
(6,	'Starý Plzenec'),
(7,	'Plzeň-Litice'),
(8,	'Union Plzeň'),
(9,	'Košutka Plzeň B'),
(10,	'Prazdroj Plzeň'),
(11,	'VS Plzeň'),
(12,	'Sokol Druztová'),
(13,	'Plzeň-Hradiště'),
(14,	'FC Chotíkov B');

DROP TABLE IF EXISTS `typefines`;
CREATE TABLE `typefines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nameFine` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `money` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `typefines` (`id`, `nameFine`, `money`) VALUES
(1,	'Pozdní příchod na zápas (do 5 minut)',	20),
(2,	'Pozdní příchod na zápas (nad 5 minut)',	50);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `tel` varchar(13) COLLATE utf8_czech_ci DEFAULT NULL,
  `idRole` int(11) NOT NULL,
  `idDivision` int(11) DEFAULT NULL,
  `cashier` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idRole` (`idRole`),
  KEY `idDivision` (`idDivision`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`idRole`) REFERENCES `roles` (`id`),
  CONSTRAINT `users_ibfk_3` FOREIGN KEY (`idDivision`) REFERENCES `divisions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `users` (`id`, `username`, `password`, `name`, `surname`, `email`, `tel`, `idRole`, `idDivision`, `cashier`) VALUES
(1,	'tomborki',	'$2y$10$okYrpDvCDRhY0SrblNUWXOpCZr7im55qqTqRrJ31bVfWI5RTQ0Erm',	'Tomáš',	'Borkovec',	'tomasborki@gmail.com',	'720141853',	1,	2,	0),
(2,	'terka',	'$2y$10$okYrpDvCDRhY0SrblNUWXOpCZr7im55qqTqRrJ31bVfWI5RTQ0Erm',	'Tereza',	'Richterová',	'terka@gmail.com',	'789456123',	2,	1,	0),
(3,	'filip',	'$2y$10$okYrpDvCDRhY0SrblNUWXOpCZr7im55qqTqRrJ31bVfWI5RTQ0Erm',	'Filip',	'Borkovec',	'filip@gmail.com',	'789456123',	3,	6,	1),
(5,	'petr',	'$2y$10$wOJr4UYq5TT1D03Wo9uoM.q4pFbIy72DJT0Oxh9cDHOtDR5cuXb5.',	'Petr',	'Bartovský',	'petr@gmail.com',	'+420111444777',	3,	4,	0);

-- 2021-12-13 23:30:40
