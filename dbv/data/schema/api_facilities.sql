CREATE TABLE `api_facilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api_gen_id` int(11) NOT NULL,
  `mfl` int(9) NOT NULL,
  `json` varchar(10000) NOT NULL,
  `period` varchar(20) DEFAULT NULL,
  `time` varchar(30) NOT NULL,
  `month` int(2) NOT NULL,
  `year` int(4) NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1