CREATE TABLE `cd4_facilityequipments` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `facility` int(10) DEFAULT NULL,
  `fname` varchar(100) DEFAULT NULL,
  `equipment` int(10) DEFAULT NULL,
  `equipmentname` varchar(100) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `reason` varchar(50) DEFAULT NULL,
  `serialNum` varchar(30) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `equipment` (`equipment`),
  KEY `facility` (`facility`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1