CREATE TABLE `drug_commodity_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old_name` varchar(100) NOT NULL,
  `old_code` varchar(50) NOT NULL,
  `old_total_units` int(11) NOT NULL,
  `old_id` int(11) NOT NULL,
  `new_name` varchar(100) NOT NULL,
  `new_code` varchar(50) NOT NULL,
  `new_total_units` int(11) NOT NULL,
  `new_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `as_of` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `unit_size_` varchar(50) NOT NULL,
  `new_price` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1