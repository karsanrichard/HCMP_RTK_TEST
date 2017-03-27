CREATE TABLE `rtk_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reference` int(11) NOT NULL,
  `reference_object` int(11) NOT NULL,
  `timestamp` varchar(15) NOT NULL,
  `current_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1