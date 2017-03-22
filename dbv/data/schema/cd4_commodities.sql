CREATE TABLE `cd4_commodities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(45) DEFAULT NULL,
  `commodity_name` varchar(45) NOT NULL,
  `unit_of_issue` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `reporting_status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1