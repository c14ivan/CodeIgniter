DROP TABLE IF EXISTS `scsystem`;

CREATE TABLE `scsystem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `duration` int(4) DEFAULT NULL,
  `status` int(4) DEFAULT 0,
  `creator` int(11) DEFAULT NULL,
  `timecreated` datetime DEFAULT NULL,
  `modifier` int(11) DEFAULT NULL,
  `timemod` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scsystemdiv`;

CREATE TABLE `scsystemdiv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scsystemid` int(11) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `timecreated` datetime DEFAULT NULL,
  `modifier` int(11) DEFAULT NULL,
  `timemod` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scplan`;

CREATE TABLE `scplan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scsystemid` int(11) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `timecreated` datetime DEFAULT NULL,
  `modifier` int(11) DEFAULT NULL,
  `timemodified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scplanversion`;

CREATE TABLE `scplanversion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `planid` int(11) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `version` int(4) DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `timecreated` datetime DEFAULT NULL,
  `modifier` int(11) DEFAULT NULL,
  `timemodified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `sccicle`;

CREATE TABLE `sccicle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scsystemid` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `abbr` varchar(5) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scarea`;

CREATE TABLE `scarea` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) DEFAULT NULL,
  `shortname` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scsubject`;

CREATE TABLE `scsubject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scareaid` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `shortname` varchar(5) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scsubjectplan`;

CREATE TABLE `scsubjectplan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sccicleid` int(11) DEFAULT NULL,
  `scsubjectid` int(11) DEFAULT NULL,
  `scplanversionid` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `ih` int(11) DEFAULT NULL,
  `credits` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scsubjectversion`;

CREATE TABLE `scsubjectversion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scsubjectid` int(11) DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


