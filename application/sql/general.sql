DROP TABLE IF EXISTS `loc_cities`;

CREATE TABLE `loc_cities` (
  `idcity` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `iddept` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`idcity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `loc_dept`;

CREATE TABLE `loc_dept` (
  `iddept` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`iddept`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;