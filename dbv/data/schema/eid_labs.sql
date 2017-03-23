CREATE TABLE `eid_labs` (
  `ID` int(14) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(32) DEFAULT NULL,
  `labname` varchar(50) DEFAULT NULL,
  `labdesc` varchar(50) DEFAULT NULL,
  `lablocation` varchar(50) DEFAULT NULL,
  `labtel1` varchar(32) DEFAULT NULL,
  `labtel2` varchar(32) DEFAULT NULL,
  `taqman` int(1) DEFAULT '1',
  `abbott` int(1) DEFAULT '1',
  PRIMARY KEY (`ID`),
  KEY `name` (`name`),
  KEY `labname` (`labname`),
  KEY `labdesc` (`labdesc`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1