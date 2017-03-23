CREATE TABLE `facility_stocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_code` int(11) NOT NULL,
  `commodity_id` int(11) NOT NULL,
  `batch_no` varchar(20) NOT NULL,
  `manufacture` varchar(50) NOT NULL,
  `initial_quantity` int(11) NOT NULL,
  `current_balance` int(11) NOT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `source_of_commodity` int(11) NOT NULL,
  `status` int(5) DEFAULT '1',
  `expiry_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1