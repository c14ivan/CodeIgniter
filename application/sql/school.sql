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
  `description` TEXT DEFAULT NULL,
  `blocked` int(4) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scareacicle`;

CREATE TABLE `scareacicle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scareaid` int(11) DEFAULT NULL,
  `sccicleid` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scsubject`;

CREATE TABLE `scsubject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scareaid` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `shortname` varchar(5) DEFAULT NULL,
  `description` text,
  `blocked` int(4) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scsubjectasign`;

CREATE TABLE `scsubjectasign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scareacicleid` int(11) DEFAULT NULL,
  `scsubjectid` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
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
  `name` varchar(100) DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `sc_enrolmethods`;

CREATE TABLE `ci`.`sc_enrolmethods` (
  `idenrolmethods` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `scplanid` bigint(10) DEFAULT NULL,
  `method` varchar(20) DEFAULT NULL,
  `hasinscription` tinyint(4) DEFAULT NULL,
  `hasform` tinyint(4) DEFAULT NULL,
  `roleid` bigint(10) DEFAULT NULL,
  `mode` varchar(20) DEFAULT NULL,
  `statusinsc` tinyint(4) DEFAULT '0',
  `statusenrol` tinyint(4) DEFAULT '0',
  `longmode` tinyint(4) DEFAULT NULL,
  `time` bigint(10) DEFAULT NULL,
  `fini` datetime DEFAULT NULL,
  `fend` datetime DEFAULT NULL,
  PRIMARY KEY (`idenrolmethods`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `sc_student`;

CREATE  TABLE `ci`.`sc_student` (
  `idstudent` INT NOT NULL AUTO_INCREMENT ,
  `nuip` VARCHAR(45) NOT NULL ,
  `lastnames` VARCHAR(50) NULL ,
  `names` VARCHAR(50) NULL ,
  `nuipfrom` BIGINT(10) NULL ,
  `bornday` DATETIME NULL ,
  `bornplace` BIGINT(10) NULL ,
  `adress` VARCHAR(100) NULL ,
  `neighborhood` VARCHAR(100) NULL ,
  `phone` VARCHAR(100) NULL ,
  `stratum` BIGINT(10) NULL ,
  `rh` VARCHAR(5) NULL ,
  `eps` VARCHAR(50) NULL ,
  `inscriptionid` BIGINT(10) NULL ,
  PRIMARY KEY (`idstudent`) ,
  UNIQUE INDEX `nuip_UNIQUE` (`nuip` ASC) );

DROP TABLE IF EXISTS `sc_parent`;
  
CREATE  TABLE `ci`.`sc_parent` (
  `idparent` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NULL ,
  `phone` VARCHAR(100) NULL ,
  `kinship` VARCHAR(20) NULL ,
  `company` VARCHAR(100) NULL ,
  `job` VARCHAR(100) NULL ,
  `jobtime` VARCHAR(45) NULL ,
  `studentid` BIGINT(10) NULL ,
  PRIMARY KEY (`idparent`) 
);

DROP TABLE IF EXISTS `sc_inscription`;

CREATE  TABLE `sc_inscription` (
  `idinscription` INT NOT NULL AUTO_INCREMENT ,
  `lastnames` VARCHAR(50) NULL ,
  `names` VARCHAR(50) NULL ,
  `nuip` VARCHAR(45) NULL ,
  `nuipfrom` BIGINT(10) NULL ,
  `bornday` DATETIME NULL ,
  `bornplace` DATETIME NULL ,
  `adress` VARCHAR(100) NULL ,
  `neighborhood` VARCHAR(100) NULL ,
  `phone` VARCHAR(100) NULL ,
  `stratum` BIGINT(10) NULL ,
  `conduct` VARCHAR(20) NULL ,
  `relatives` TINYINT(4) NULL ,
  `ownhouse` TINYINT(4) NULL ,
  `family` VARCHAR(255) NULL ,
  `interviewcoment` TEXT NULL ,
  `interviewresult` VARCHAR(20) NULL ,
  `schoolfrom` VARCHAR(100) NULL ,
  PRIMARY KEY (`idinscription`) 
);

DROP TABLE IF EXISTS `sc_preparent`;

CREATE TABLE `sc_preparent` (
  `idpreparent` int(11) NOT NULL AUTO_INCREMENT,
  `inscriptionid` bigint(10) DEFAULT NULL,
  `parentname` varchar(100) DEFAULT NULL,
  `kinship` varchar(20) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `comptime` varchar(10) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idpreparent`)
);