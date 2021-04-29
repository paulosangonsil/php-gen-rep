CREATE DATABASE IF NOT EXISTS `gen_rep`;
USE `gen_rep`;

CREATE TABLE `gen_rep_appinfos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` text,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE `gen_rep_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usrname` varchar(50) NOT NULL,
  `passwd` tinytext NOT NULL,
  `textname` tinytext,
  `roleinapp` blob,
  `prefs` blob,
  `last_access` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usrname` (`usrname`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE `gen_rep_entry` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `idusr` bigint unsigned NOT NULL,
  `idapp` bigint unsigned NOT NULL,
  `data` blob,
  `tstamp` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `usrid_ibfk_1` FOREIGN KEY (`idusr`) REFERENCES `gen_rep_user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `idapp_ibfk_1` FOREIGN KEY (`idapp`) REFERENCES `gen_rep_appinfos` (`id`) ON DELETE CASCADE
) DEFAULT CHARSET=utf8mb4;
