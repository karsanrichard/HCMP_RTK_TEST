CREATE TABLE `dmlt_districts` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `dmlt` int(5) NOT NULL,
  `district` int(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`dmlt`),
  KEY `district` (`district`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1