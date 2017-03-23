CREATE TABLE `cd4_districts` (
  `ID` int(14) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `county` int(32) NOT NULL,
  `countyname` varchar(100) NOT NULL,
  `comment` varchar(32) DEFAULT NULL,
  `flag` int(32) DEFAULT '1',
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`),
  KEY `province` (`countyname`),
  KEY `county` (`county`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1