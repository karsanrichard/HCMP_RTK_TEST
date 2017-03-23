CREATE TABLE `cd4_facility_device` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_code` varchar(20) NOT NULL,
  `device` int(11) NOT NULL,
  `enabled` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1