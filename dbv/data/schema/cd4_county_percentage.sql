CREATE TABLE `cd4_county_percentage` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `county_id` int(11) NOT NULL,
  `cd4_facilities` int(11) NOT NULL,
  `cd4_reported` int(11) NOT NULL,
  `cd4_percentage` int(11) NOT NULL,
  `reported_month` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`county_id`,`cd4_percentage`),
  KEY `province` (`cd4_reported`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1