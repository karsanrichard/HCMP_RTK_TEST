CREATE TABLE `rtk_county_percentage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `county_id` int(11) NOT NULL,
  `facilities` int(11) NOT NULL,
  `reported` int(11) NOT NULL,
  `percentage` int(11) NOT NULL,
  `month` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `county` (`county_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1