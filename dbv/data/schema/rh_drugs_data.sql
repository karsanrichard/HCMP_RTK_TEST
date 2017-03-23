CREATE TABLE `rh_drugs_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `Beginning_Balance` varchar(30) NOT NULL,
  `Received_This_Month` varchar(30) NOT NULL,
  `Dispensed` varchar(30) NOT NULL,
  `Losses` varchar(30) NOT NULL,
  `Adjustments` varchar(30) NOT NULL,
  `Ending_Balance` varchar(30) NOT NULL,
  `Quantity_Requested` varchar(30) NOT NULL,
  `Report_Date` date NOT NULL,
  `report_id` int(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1