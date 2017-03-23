CREATE TABLE `district_drawing_rights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `county_id` int(11) NOT NULL,
  `district_id` int(11) NOT NULL,
  `screening` int(11) NOT NULL,
  `screening_current_amount` int(11) NOT NULL,
  `confirmatory` int(11) NOT NULL,
  `confirmatory_current_amount` int(11) NOT NULL,
  `updated_on` varchar(250) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1