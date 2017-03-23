CREATE TABLE `lab_commodity_details1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `facility_code` varchar(10) NOT NULL,
  `commodity_id` int(11) NOT NULL,
  `unit_of_issue` int(11) NOT NULL,
  `q_used` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order` (`order_id`),
  KEY `facility_code` (`facility_code`),
  KEY `commodity` (`commodity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1