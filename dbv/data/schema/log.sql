CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `start_time_of_event` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `action_id` int(11) NOT NULL DEFAULT '0',
  `end_time_of_event` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `issued` int(1) NOT NULL DEFAULT '0',
  `ordered` int(1) NOT NULL DEFAULT '0',
  `decommissioned` int(1) NOT NULL DEFAULT '0',
  `redistribute` int(1) NOT NULL DEFAULT '0',
  `add_stock` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1