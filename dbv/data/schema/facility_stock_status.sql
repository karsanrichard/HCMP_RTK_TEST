CREATE TABLE `facility_stock_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status_desc` varchar(20) NOT NULL,
  `status` int(5) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1