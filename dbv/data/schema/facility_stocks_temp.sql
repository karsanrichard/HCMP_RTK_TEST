CREATE TABLE `facility_stocks_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commodity_id` int(11) NOT NULL,
  `unit_size` varchar(50) NOT NULL,
  `batch_no` varchar(50) NOT NULL,
  `manu` varchar(50) NOT NULL,
  `expiry_date` varchar(50) NOT NULL,
  `stock_level` varchar(50) NOT NULL,
  `total_unit_count` int(11) NOT NULL,
  `facility_code` varchar(50) NOT NULL,
  `unit_issue` varchar(20) NOT NULL,
  `total_units` int(11) NOT NULL,
  `source_of_item` int(11) NOT NULL,
  `supplier` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1