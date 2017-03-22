CREATE TABLE `lab_commodities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commodity_name` varchar(50) NOT NULL,
  `category` int(11) NOT NULL,
  `unit_of_issue` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1