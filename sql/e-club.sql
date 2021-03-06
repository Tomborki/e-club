-- Adminer 4.7.7 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `divisions`;
CREATE TABLE `divisions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nameDivision` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `chiefUserId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `chiefUserId` (`chiefUserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `divisions` (`id`, `nameDivision`, `chiefUserId`) VALUES
(1,	'Muži A',	15),
(2,	'Muži B',	18),
(4,	'Starší dorost',	19),
(5,	'Mladší dorost',	15),
(6,	'Starší žáci',	5),
(7,	'Mladší žáci',	18),
(8,	'Přípravka',	18),
(29,	'Stará garda',	20);

DROP TABLE IF EXISTS `finer`;
CREATE TABLE `finer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeFines_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `paid` tinyint(4) NOT NULL,
  `cashierId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `typeFines_id` (`typeFines_id`),
  KEY `users_id` (`users_id`),
  KEY `cashierId` (`cashierId`),
  CONSTRAINT `finer_ibfk_1` FOREIGN KEY (`typeFines_id`) REFERENCES `typefines` (`id`),
  CONSTRAINT `finer_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`),
  CONSTRAINT `finer_ibfk_3` FOREIGN KEY (`cashierId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `finer` (`id`, `typeFines_id`, `users_id`, `date`, `paid`, `cashierId`) VALUES
(3,	1,	2,	'2021-11-29 23:06:18',	0,	2),
(4,	14,	15,	'2021-12-17 21:35:12',	1,	2),
(5,	25,	15,	'2021-12-17 21:35:12',	1,	15),
(6,	10,	15,	'2021-12-17 21:35:12',	1,	2),
(8,	9,	15,	'2021-12-17 22:55:01',	0,	15),
(9,	3,	3,	'2019-12-21 12:05:43',	0,	15),
(10,	6,	3,	'2019-12-21 12:05:43',	0,	15),
(11,	3,	5,	'2019-12-21 12:05:43',	0,	15),
(12,	6,	5,	'2019-12-21 12:05:43',	0,	15),
(13,	4,	15,	'2021-12-19 00:07:12',	0,	15),
(15,	26,	15,	'2021-12-19 00:08:00',	1,	15),
(16,	33,	15,	'2021-12-19 00:08:00',	0,	15),
(17,	36,	15,	'2021-12-19 00:08:00',	0,	15),
(18,	5,	15,	'2021-12-19 01:15:48',	0,	15),
(22,	3,	15,	'2021-12-19 01:19:11',	1,	15),
(24,	5,	2,	'2021-12-19 01:20:23',	0,	15),
(27,	21,	2,	'2021-12-19 03:08:34',	0,	2),
(28,	21,	3,	'2021-12-19 03:08:34',	1,	2),
(29,	2,	15,	'2021-12-19 16:00:12',	1,	15),
(30,	12,	18,	'2021-12-21 00:50:09',	0,	15);

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
  CONSTRAINT `matches_ibfk_4` FOREIGN KEY (`idDivision`) REFERENCES `divisions` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `matches` (`id`, `idTeam1`, `idTeam2`, `idDivision`, `date`, `team1Score`, `team2Score`, `end`) VALUES
(1,	4,	2,	1,	'2021-10-15',	2,	1,	1),
(2,	5,	4,	1,	'2021-12-05',	0,	1,	1),
(3,	7,	4,	1,	'2021-12-12',	0,	1,	1),
(4,	4,	8,	1,	'2021-12-19',	NULL,	NULL,	0),
(5,	4,	2,	2,	'2021-10-15',	2,	5,	1),
(6,	5,	4,	2,	'2021-10-20',	1,	1,	1),
(7,	7,	4,	2,	'2021-11-11',	3,	0,	1),
(8,	4,	8,	2,	'2021-12-19',	NULL,	NULL,	0),
(12,	10,	4,	2,	'2021-12-25',	NULL,	NULL,	0),
(13,	13,	4,	1,	'2021-12-25',	NULL,	NULL,	0),
(14,	4,	6,	1,	'2022-01-02',	NULL,	NULL,	0);

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `content` mediumtext COLLATE utf8_czech_ci NOT NULL,
  `date` datetime NOT NULL,
  `chiefId` int(11) NOT NULL,
  `divisionId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `chiefId` (`chiefId`),
  KEY `divisionId` (`divisionId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `messages` (`id`, `title`, `content`, `date`, `chiefId`, `divisionId`) VALUES
(5,	'Informace ohledně soustředění',	'<p>Ahoj sportovci,&nbsp;<br>posílám informace ohledně zimního soustředění. Informace jsou dostupné na následujícím odkaze:&nbsp;<a href=\"https://www.youtube.com/watch?v=dQw4w9WgXcQ&amp;ab_channel=RickAstley\" target=\"_blank\">Odkaz na informace</a><br><br>Díky a sportu zdar!&nbsp;</p>',	'2021-12-20 21:27:03',	15,	1),
(3,	'etstets',	'<p>estest</p>',	'2021-12-20 17:22:47',	15,	7),
(4,	'Testovací zpráva',	'<p><b>Ahoj</b></p><p>Musím říct, že tuty zprávy jsou super!&nbsp;</p>',	'2021-12-20 20:40:39',	15,	5),
(9,	'Testovací zpráva',	'<p>asdfasfasfsadf</p><p>asdf</p><p>asdf</p><p>asdfasdfasdf</p>',	'2021-12-21 00:40:43',	15,	1);

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
(2,	'Pozdní příchod na zápas (nad 5 minut)',	50),
(3,	'Neomluvená účast na zápas',	500),
(4,	'Omluvení neúčasti na zápas v den zápasu trenérovi',	250),
(5,	'Opilectví (každých 0,1‰)',	100),
(6,	'Opilectví (nad 0,5‰)',	500),
(7,	'Žlutá karta (kecy)',	100),
(8,	'Čtvrtá žlutá karta v sezoně',	100),
(9,	'Osmá žlutá karta v sezoně',	200),
(10,	'Červená karta (běžný faul)',	100),
(11,	'Červená karta (kecy)',	500),
(12,	'Hádka s trenérem nebo spoluhráčem',	100),
(13,	'První zápas za \"A\"',	100),
(14,	'První gól za \"A\"',	100),
(15,	'První gól v nové sezoně',	20),
(16,	'Zápas v den svých narozenin',	200),
(17,	'Hattrick',	200),
(18,	'Poprvé kapitánem za \"A\"',	100),
(19,	'První zápas v nových kopačkách',	20),
(20,	'První gól v nových kopačkách',	200),
(21,	'První udržená nula v sezoně',	50),
(22,	'Udržená nula v den svých narozenin',	200),
(23,	'Neproměněná penalta (ve hře)',	100),
(24,	'Neproměněná penalta (závěrečný rozstřel)',	30),
(25,	'Narození dítěte',	500),
(26,	'Svatba',	500),
(27,	'Barevné foto (noviny/net)',	100),
(28,	'Černobíle foto (noviny/net)',	50),
(29,	'Prokazatelná účast v TV',	200),
(30,	'Zvonění telefonu (v době srazu)',	20),
(31,	'Kouření (v době srazu)',	50),
(32,	'Močení ve sprše',	50),
(33,	'Prdění v kabině',	20),
(34,	'Měkký balon na tréninku',	100),
(35,	'Nedodržení akce \"Movember\"',	200),
(36,	'Ukončení středoškolského vždělání',	250),
(37,	'Ukončení vysokoškolského studia',	500),
(38,	'Testovací pokuta',	10000),
(39,	'Pokuta pro terku ',	159);

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
  `avatarImageName` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idRole` (`idRole`),
  KEY `idDivision` (`idDivision`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`idRole`) REFERENCES `roles` (`id`),
  CONSTRAINT `users_ibfk_4` FOREIGN KEY (`idDivision`) REFERENCES `divisions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `users` (`id`, `username`, `password`, `name`, `surname`, `email`, `tel`, `idRole`, `idDivision`, `cashier`, `avatarImageName`) VALUES
(2,	'terka',	'$2y$10$xGgL46PabI0DUC3RqRy0XeO.VyxKqvxSaEBhmi8BNhzASYol8MztS',	'Terka',	'Richterová',	'terka@gmail.com',	'+420789456123',	3,	1,	1,	'avatarbb4953fc74929b17f2e9114c3c69b85fde5203ff.jpg'),
(3,	'filip',	'$2y$10$K/KLqtPNm6VpYkFO8GdltOmZrAWUu71B6upuxgaYF19SvMfK.sJ/m',	'Filip',	'Borkovec',	'asdf@gamilc.om',	'+420159852222',	3,	1,	1,	'avatar72d7e2267899ebb00b385217a6ec53a26aabe22e.png'),
(5,	'petr',	'$2y$10$Zg3XtJo1SJvRePpm2/Udp.vfrVToH33QBBcnpczMDVg2oS91d9FNK',	'Petr',	'Bartovský',	'petr@gmail.com',	'+420111444777',	3,	4,	0,	'avatar7e51ff471e315bdbc789132238a72cb8920bc4a1.jpg'),
(15,	'tomborki',	'$2y$10$OW/ZOhGJzYIIBXpXP3GcM.Aa8FCAIbXVH.QVivLpL2PAmrPDRC7TO',	'Tomáš',	'Borkovec',	'tomasborki@gmail.com',	'+420158559996',	1,	1,	0,	'avatarc34444a6ec18ca34e59526eb30d94717d9b069b4.jpg'),
(16,	'michal',	'$2y$10$gFTTvoYqh3UTDOBLBgHM3etv6XQF9dV24pswlwj3Lk1ilKoFEZY2m',	'Michal',	'Čerepjuk',	'cery@gmail.com',	'+420156663225',	3,	1,	0,	'avatarb9504865f1b1d924f2b311bcee0c44517d13850d.jpg'),
(17,	'dobrak',	'$2y$10$vNkDnrzeBTQmL8ks1vHude.CNKqBdmWFw/XUjyPovHNh/Mm5gXBxm',	'Josef',	'Dobrý',	'db@gmail.com',	'+420157441853',	3,	1,	0,	'avatar3e22a21c49de964fcb146d55221b1295211d4fab.jpg'),
(18,	'tomik',	'$2y$10$FoNuMjChAZfgR6r90FdKgeoJvRL0/38v.2NR93X0cDRRZFNdBW1y6',	'Tomáš',	'Hanách',	'hanach@seznam.cz',	'+420987777456',	1,	5,	0,	'avatar08ee7803b800d9e2daf7d4c6e634825d3230bc4e.png'),
(19,	'lhotakdavid',	'$2y$10$U7zbi8yPtNO3PM7uuDif8.pXfdlFi91D5H21EWnNiqATJOUYbrl5.',	'Jakub',	'Lhoták',	'lhotakdavid@gmail.com',	'+420155963255',	3,	5,	0,	'avatar61a15c0cd7c53d13472a4ae1c92e5c4578290f53.jpg'),
(20,	'jirkagertner',	'$2y$10$Rs.XEyaRjI3zNANmgusSYO4Px3zLSm3Omd0KZZ9RSQagCYfuZTUaG',	'Jiří',	'Gernter',	'jirkagertner@seznam.cz',	'+420147458889',	3,	NULL,	0,	'avatarce7c6a2669424751f22444a65904881ad2f81460.jpg');

-- 2021-12-21 00:01:16
