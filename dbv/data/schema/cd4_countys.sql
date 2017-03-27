CREATE TABLE `cd4_countys` (
  `ID` int(32) NOT NULL,
  `name` varchar(150) NOT NULL,
  `provincename` varchar(150) NOT NULL,
  `province` int(150) NOT NULL,
  `letter` varchar(1) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `name` (`name`,`letter`),
  KEY `province` (`province`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1