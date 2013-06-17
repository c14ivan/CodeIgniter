delimiter $$

CREATE TABLE `role` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `weight` bigint(10) NOT NULL DEFAULT '0',
  `shortname` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_role_nam_uix` (`name`),
  UNIQUE KEY `mdl_role_sho_uix` (`shortname`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin$$

delimiter $$

CREATE TABLE `role_assignments` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `roleid` bigint(10) NOT NULL DEFAULT '0',
  `contextid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `modifierid` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin$$

delimiter $$

CREATE TABLE `capabilities` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `capability` bigint(10) NOT NULL DEFAULT '0',
  `url` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `weight` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `modifierid` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin$$

delimiter $$

CREATE TABLE `role_capabilities` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `contextid` bigint(10) NOT NULL DEFAULT '0',
  `roleid` bigint(10) NOT NULL DEFAULT '0',
  `capabilityid` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `icon` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `permission` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `modifierid` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin$$

delimiter $$

CREATE TABLE `context` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `contextlevel` bigint(10) NOT NULL DEFAULT '0',
  `instanceid` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin$$

