CREATE TABLE `rtk_partner_percentage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `facilities` int(11) NOT NULL,
  `reported` int(11) NOT NULL,
  `percentage` int(11) NOT NULL,
  `month` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `partner` (`partner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1