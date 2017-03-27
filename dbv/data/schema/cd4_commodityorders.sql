CREATE TABLE `cd4_commodityorders` (
  `orderID` int(11) NOT NULL AUTO_INCREMENT,
  `reagentID` int(11) NOT NULL,
  `endbalance` int(11) NOT NULL,
  `required` int(11) NOT NULL,
  `allocationrate` int(11) NOT NULL,
  `dod` date NOT NULL,
  `received` int(11) NOT NULL DEFAULT '0',
  `rejected` int(11) NOT NULL DEFAULT '0',
  `comment` varchar(100) NOT NULL,
  `enddate` date NOT NULL,
  `facility` varchar(15) NOT NULL,
  `fromdate` date NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`orderID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1