CREATE TABLE `commodity_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `category_name` text,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1