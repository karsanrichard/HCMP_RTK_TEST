CREATE TABLE `commodity_sub_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `commodity_category_id` int(11) DEFAULT NULL,
  `sub_category_name` text,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1