CREATE TABLE `cd4_reagents` (
  `reagentID` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(15) NOT NULL,
  `reagentname` varchar(100) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `categoryID` int(11) NOT NULL,
  PRIMARY KEY (`reagentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1