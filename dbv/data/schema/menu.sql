CREATE TABLE `menu` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `menu_describe` varchar(100) NOT NULL,
  `menu_text` varchar(100) NOT NULL,
  `menu_url` varchar(100) NOT NULL,
  `user_group` int(10) NOT NULL,
  `parent_status` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1