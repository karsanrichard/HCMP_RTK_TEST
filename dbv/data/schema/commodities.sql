CREATE TABLE `commodities` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `commodity_code` varchar(20) DEFAULT NULL,
  `commodity_name` text CHARACTER SET utf8,
  `unit_size` varchar(100) DEFAULT NULL,
  `unit_cost` varchar(100) DEFAULT NULL,
  `commodity_sub_category_id` int(11) NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `total_commodity_units` int(11) NOT NULL,
  `commodity_source_id` int(11) NOT NULL,
  `status` int(11) DEFAULT '1',
  `tracer_item` int(11) NOT NULL DEFAULT '0',
  `commodity_division` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1