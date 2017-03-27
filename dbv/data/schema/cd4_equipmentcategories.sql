CREATE TABLE `cd4_equipmentcategories` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) DEFAULT NULL,
  `flag` int(10) DEFAULT '1',
  PRIMARY KEY (`ID`),
  KEY `description` (`description`),
  KEY `flag` (`flag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1