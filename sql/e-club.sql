-- Adminer 4.7.7 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleName` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `roles` (`id`, `roleName`) VALUES
(1,	'superadmin'),
(2,	'admin'),
(3,	'Normalní uživatel');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password` mediumtext COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `idRole` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `users` (`id`, `username`, `password`, `name`, `surname`, `idRole`) VALUES
(2,	'tomborki',	'$2y$10$okYrpDvCDRhY0SrblNUWXOpCZr7im55qqTqRrJ31bVfWI5RTQ0Erm',	'Tomáš',	'Borkovec',	1),
(3,	'terka',	'$2y$10$okYrpDvCDRhY0SrblNUWXOpCZr7im55qqTqRrJ31bVfWI5RTQ0Erm',	'Tereza',	'Richterová',	2),
(4,	'filip',	'$2y$10$okYrpDvCDRhY0SrblNUWXOpCZr7im55qqTqRrJ31bVfWI5RTQ0Erm',	'Filip',	'Borkovec',	3);

-- 2021-11-25 19:49:48
