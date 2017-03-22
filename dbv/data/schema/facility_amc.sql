CREATE TABLE `facility_amc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_code` int(11) NOT NULL,
  `commodity_id` int(5) NOT NULL,
  `amc` varchar(6) NOT NULL,
  `amc_6` int(11) DEFAULT NULL,
  `last_update` varchar(15) NOT NULL,
  `latest` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `facility_code` (`facility_code`),
  KEY `commodity` (`commodity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1