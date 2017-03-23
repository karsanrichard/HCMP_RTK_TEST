CREATE TABLE `commodity_source` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_name` varchar(100) NOT NULL,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1