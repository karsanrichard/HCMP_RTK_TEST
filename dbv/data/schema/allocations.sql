CREATE TABLE `allocations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `county_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `month` varchar(45) DEFAULT NULL,
  `status` varchar(45) DEFAULT 'Pending',
  `county_comment` longtext,
  `district_comment` longtext,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1