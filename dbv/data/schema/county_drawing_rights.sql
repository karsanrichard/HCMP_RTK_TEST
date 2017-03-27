CREATE TABLE `county_drawing_rights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `county_id` int(11) DEFAULT NULL,
  `zone` varchar(45) DEFAULT NULL,
  `duration` varchar(45) DEFAULT NULL,
  `screening_amount` int(11) DEFAULT NULL,
  `confirmatory_amount` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1