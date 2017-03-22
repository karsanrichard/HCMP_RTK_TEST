CREATE TABLE `counties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `county` varchar(30) NOT NULL,
  `zone` varchar(45) DEFAULT NULL,
  `kenya_map_id` int(11) NOT NULL,
  `htc` varchar(20) NOT NULL,
  `pmtct` varchar(20) NOT NULL,
  `screening_drawing_rights` varchar(20) NOT NULL,
  `screening_current_amount` varchar(20) NOT NULL,
  `confimatory_drawing_rights` varchar(45) NOT NULL,
  `confirmatory_current_amount` varchar(45) DEFAULT NULL,
  `tiebreaker_drawing_rights` varchar(45) NOT NULL,
  `tiebreaker_current_amount` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1