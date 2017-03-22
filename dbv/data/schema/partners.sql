CREATE TABLE `partners` (
  `ID` int(32) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `flag` int(45) DEFAULT '1' COMMENT '1 for active 0 for inactive',
  PRIMARY KEY (`ID`),
  KEY `name` (`name`),
  KEY `flag` (`flag`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1