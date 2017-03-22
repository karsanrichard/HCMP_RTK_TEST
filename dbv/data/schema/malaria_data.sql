CREATE TABLE `malaria_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `Beginning_Balance` varchar(30) NOT NULL,
  `Quantity_Received` varchar(30) NOT NULL,
  `Quantity_Dispensed` varchar(30) NOT NULL,
  `Losses_Excluding_Expiries` varchar(30) NOT NULL,
  `Positive_Adjustments` varchar(30) NOT NULL,
  `Negative_Adjustments` varchar(30) NOT NULL,
  `Physical_Count` varchar(30) NOT NULL,
  `Expired_Drugs` varchar(30) NOT NULL,
  `Days_Out_Stock` int(11) NOT NULL,
  `Report_Total` varchar(30) NOT NULL,
  `Kemsa_Code` varchar(30) NOT NULL,
  `Report_Date` datetime NOT NULL,
  `report_id` int(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1