CREATE TABLE `rtk_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deadline` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `5_day_alert` text NOT NULL,
  `report_day_alert` text NOT NULL,
  `overdue_alert` text NOT NULL,
  `zone` varchar(15) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1