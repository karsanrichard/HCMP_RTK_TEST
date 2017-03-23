CREATE TABLE `commodity_division_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `division_name` varchar(255) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1