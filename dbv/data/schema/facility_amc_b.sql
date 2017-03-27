CREATE TABLE `facility_amc_b` (
  `id` int(11) NOT NULL,
  `facility_code` int(11) NOT NULL,
  `commodity_id` int(5) NOT NULL,
  `amc` varchar(6) NOT NULL,
  `amc_6` int(11) DEFAULT NULL,
  `last_update` varchar(15) NOT NULL,
  `latest` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1