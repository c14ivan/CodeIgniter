DROP TABLE IF EXISTS `lb_category`;

CREATE TABLE `lb_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catid` int(11) DEFAULT NULL,
  `catparent` bigint(11) DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `lb_book`;

CREATE TABLE `lb_book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `editorialid` bigint(20) DEFAULT NULL,
  `autor` varchar(255) DEFAULT NULL,
  `keywords` text,
  `year` varchar(45) DEFAULT NULL,
  `edition` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;