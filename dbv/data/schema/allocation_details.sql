CREATE TABLE `allocation_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `county_id` varchar(45) DEFAULT NULL,
  `county` varchar(35) NOT NULL,
  `district_id` varchar(45) DEFAULT NULL,
  `district` varchar(35) NOT NULL,
  `facility_code` varchar(10) NOT NULL,
  `facility_name` varchar(100) NOT NULL,
  `zone` varchar(10) NOT NULL,
  `amc_s` int(11) NOT NULL,
  `ending_bal_s` int(11) DEFAULT NULL,
  `allocate_s` int(11) NOT NULL,
  `mmos_s` int(11) NOT NULL,
  `remark_s` varchar(250) DEFAULT NULL,
  `decision_s` varchar(250) DEFAULT NULL,
  `amc_c` int(11) NOT NULL,
  `ending_bal_c` int(11) DEFAULT NULL,
  `allocate_c` int(11) NOT NULL,
  `mmos_c` int(11) NOT NULL,
  `remark_c` varchar(250) DEFAULT NULL,
  `decision_c` varchar(250) DEFAULT NULL,
  `amc_t` int(11) NOT NULL,
  `allocate_t` int(11) NOT NULL,
  `amc_d` varchar(45) DEFAULT NULL,
  `allocate_d` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `month` varchar(45) DEFAULT NULL,
  `user_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`,`allocate_c`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1