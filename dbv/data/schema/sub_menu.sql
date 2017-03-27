CREATE TABLE `sub_menu` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `subm_text` varchar(100) NOT NULL,
  `subm_url` varchar(100) NOT NULL,
  `parent_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1