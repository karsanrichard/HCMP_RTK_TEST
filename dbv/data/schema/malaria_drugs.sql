CREATE TABLE `malaria_drugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `drug_name` text NOT NULL,
  `unit_size` varchar(100) NOT NULL,
  `unit_cost` varchar(20) NOT NULL,
  `drug_category` varchar(10) NOT NULL,
  `kemsa_code` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kemsa_code` (`kemsa_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1