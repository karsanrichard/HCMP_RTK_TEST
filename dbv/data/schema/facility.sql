CREATE TABLE `facility` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `mfl_code` varchar(11) NOT NULL,
  `site_prefix` varchar(100) DEFAULT NULL,
  `sub_county_id` int(11) DEFAULT NULL COMMENT 'FK to sub_county',
  `partner_id` int(11) DEFAULT NULL,
  `facility_type_id` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT '0',
  `central_site_id` int(11) DEFAULT '0',
  `email` varchar(250) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `rollout_status` int(11) DEFAULT '1',
  `rollout_date` date DEFAULT NULL,
  `google_maps` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Facilities'