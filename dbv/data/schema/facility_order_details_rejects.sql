CREATE TABLE `facility_order_details_rejects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_order_details_id` int(11) NOT NULL,
  `status` int(5) DEFAULT '1',
  `reason` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1