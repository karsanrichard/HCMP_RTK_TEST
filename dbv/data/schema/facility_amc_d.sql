CREATE TABLE `facility_amc_d` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_code` int(11) NOT NULL,
  `commodity_id` int(11) NOT NULL,
  `amc` varchar(10) NOT NULL,
  `amc_6` int(11) NOT NULL,
  `last_update` varchar(100) NOT NULL,
  `latest` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `facility_code` (`facility_code`),
  KEY `commodity` (`commodity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1