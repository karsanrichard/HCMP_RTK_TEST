CREATE TABLE `cd4_allocation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_code` int(6) NOT NULL,
  `reagentID` int(5) NOT NULL,
  `allocation_for` int(30) NOT NULL,
  `qty` int(11) NOT NULL,
  `date_allocated` int(12) NOT NULL,
  `allocated_by` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1