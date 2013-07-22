CREATE TABLE `scsystem` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `timecreated` datetime DEFAULT NULL,
  `modifier` int(11) DEFAULT NULL,
  `timemod` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `scsysversion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scsystemid` int(11) DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `description` text,
  `creator` int(11) DEFAULT NULL,
  `timecreated` datetime DEFAULT NULL,
  `modifier` int(11) DEFAULT NULL,
  `timemodified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sccicle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scsysversionid` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `serial` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `scarea` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) DEFAULT NULL,
  `shortname` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `scareacicle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scareaid` int(11) DEFAULT NULL,
  `sccicle` varchar(45) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `scsubject` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  `shortname` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `scsubjectversion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scsubjectid` int(11) DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `scsubjectasign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scareacicleid` int(11) DEFAULT NULL,
  `scsubjectid` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

