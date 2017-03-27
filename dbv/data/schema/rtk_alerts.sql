CREATE TABLE `rtk_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `reference` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1