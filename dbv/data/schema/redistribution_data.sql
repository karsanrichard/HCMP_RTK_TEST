CREATE TABLE `redistribution_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_facility_code` int(11) NOT NULL,
  `receive_facility_code` int(11) NOT NULL,
  `commodity_id` int(11) NOT NULL,
  `quantity_sent` int(11) NOT NULL,
  `quantity_received` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `manufacturer` varchar(100) NOT NULL,
  `batch_no` varchar(100) NOT NULL,
  `expiry_date` date NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `facility_stock_ref_id` int(11) NOT NULL,
  `date_sent` date NOT NULL,
  `date_received` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1