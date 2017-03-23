CREATE TABLE `api_gen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `json` varchar(30000) NOT NULL,
  `date_sync` int(12) NOT NULL,
  `month` int(2) NOT NULL,
  `year` int(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1