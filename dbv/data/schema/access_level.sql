CREATE TABLE `access_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` varchar(255) NOT NULL,
  `user_indicator` varchar(50) NOT NULL,
  `moh_permissions` int(11) NOT NULL,
  `district_permissions` int(11) NOT NULL,
  `county_permissions` int(11) NOT NULL,
  `facilityadmin_permissions` int(11) NOT NULL,
  `facility_permissions` int(11) NOT NULL,
  `super_permissions` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1