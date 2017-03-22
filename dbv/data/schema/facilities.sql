CREATE TABLE `facilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_code` int(11) DEFAULT NULL,
  `facility_name` varchar(100) DEFAULT NULL,
  `district` int(11) DEFAULT NULL,
  `partner` int(11) NOT NULL DEFAULT '0',
  `drawing_rights` int(50) DEFAULT NULL,
  `owner` varchar(100) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `level` varchar(30) DEFAULT NULL,
  `rtk_enabled` int(11) DEFAULT NULL,
  `pepfar_supported` int(11) NOT NULL DEFAULT '0',
  `cd4_enabled` tinyint(4) DEFAULT NULL,
  `drawing_rights_balance` int(11) DEFAULT NULL,
  `using_hcmp` int(11) DEFAULT NULL,
  `date_of_activation` date DEFAULT NULL,
  `zone` varchar(6) DEFAULT NULL,
  `contactperson` varchar(50) DEFAULT NULL,
  `cellphone` int(15) DEFAULT NULL,
  `targetted` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `facility_code` (`facility_code`),
  KEY `district` (`district`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1