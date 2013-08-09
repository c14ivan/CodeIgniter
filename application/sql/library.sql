DROP TABLE IF EXISTS `lb_category`;

CREATE TABLE `lb_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catid` varchar(5) DEFAULT NULL,
  `catparent` bigint(11) DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `lb_book`;

CREATE TABLE `lb_book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ident` varchar(45) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `editorialid` bigint(20) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `keywords` text,
  `year` varchar(45) DEFAULT NULL,
  `edition` varchar(45) DEFAULT NULL,
  `categoryid` bigint(10) DEFAULT NULL,
  `creator` bigint(10) DEFAULT '0',
  `timecreated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `lb_editorial`;

CREATE TABLE `lb_editorial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;