CREATE TABLE `eid_taqmandeliveries` (
  `ID` int(32) NOT NULL AUTO_INCREMENT,
  `testtype` int(1) NOT NULL DEFAULT '0',
  `lab` int(32) DEFAULT NULL,
  `quarter` int(32) DEFAULT '0',
  `source` int(32) DEFAULT '0',
  `labfrom` int(32) DEFAULT '0',
  `kitlotno` varchar(100) DEFAULT NULL,
  `expirydate` date DEFAULT NULL,
  `qualkitreceived` int(32) DEFAULT '0',
  `qualkitdamaged` int(32) DEFAULT '0',
  `spexagentreceived` int(32) DEFAULT '0',
  `spexagentdamaged` int(32) DEFAULT '0',
  `ampinputreceived` int(32) DEFAULT '0',
  `ampinputdamaged` int(32) DEFAULT '0',
  `ampflaplessreceived` int(32) DEFAULT '0',
  `ampflaplessdamaged` int(32) DEFAULT '0',
  `ampktipsreceived` int(32) DEFAULT '0',
  `ampktipsdamaged` int(32) DEFAULT '0',
  `ampwashreceived` int(32) DEFAULT '0',
  `ampwashdamaged` int(32) DEFAULT '0',
  `ktubesreceived` int(32) DEFAULT '0',
  `ktubesdamaged` int(32) DEFAULT '0',
  `consumablesreceived` int(32) DEFAULT '0',
  `consumablesdamaged` int(32) DEFAULT '0',
  `receivedby` int(32) DEFAULT '0',
  `datereceived` date DEFAULT NULL,
  `status` int(32) DEFAULT '0',
  `dateentered` date DEFAULT NULL,
  `enteredby` int(32) DEFAULT '0',
  `flag` int(32) DEFAULT '1',
  `synchronized` int(32) DEFAULT '0',
  `datesynchronized` date DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `lab` (`lab`),
  KEY `source` (`source`),
  KEY `quarter` (`quarter`),
  KEY `labfrom` (`labfrom`),
  KEY `kitlotno` (`kitlotno`),
  KEY `expirydate` (`expirydate`),
  KEY `qualkitreceived` (`qualkitreceived`),
  KEY `qualkitdamaged` (`qualkitdamaged`),
  KEY `spexagentreceived` (`spexagentreceived`),
  KEY `spexagetdamaged` (`spexagentdamaged`),
  KEY `ampinputreceived` (`ampinputreceived`),
  KEY `ampinputdamaged` (`ampinputdamaged`),
  KEY `ampflaplessreceived` (`ampflaplessreceived`),
  KEY `ampflaplessdamaged` (`ampflaplessdamaged`),
  KEY `ampktipsreceived` (`ampktipsreceived`),
  KEY `ampktipsdamaged` (`ampktipsdamaged`),
  KEY `ampwashreceived` (`ampwashreceived`),
  KEY `ampwashdamaged` (`ampwashdamaged`),
  KEY `ktubesreceived` (`ktubesreceived`),
  KEY `ktubesdamaged` (`ktubesdamaged`),
  KEY `consumablesreceived` (`consumablesreceived`),
  KEY `consumablesdamaged` (`consumablesdamaged`),
  KEY `receivedby` (`receivedby`),
  KEY `datereceived` (`datereceived`),
  KEY `status` (`status`),
  KEY `dateentered` (`dateentered`),
  KEY `enteredby` (`enteredby`),
  KEY `flag` (`flag`),
  KEY `synchronized` (`synchronized`),
  KEY `datesynchronized` (`datesynchronized`),
  KEY `testtype` (`testtype`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='monitors the quarterly deliveries received by the lab'