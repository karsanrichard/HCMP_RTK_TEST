CREATE TABLE `facility_transaction_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_code` int(11) NOT NULL,
  `commodity_id` varchar(10) NOT NULL,
  `opening_balance` int(11) NOT NULL DEFAULT '0',
  `total_receipts` int(11) NOT NULL DEFAULT '0',
  `total_issues` int(11) NOT NULL DEFAULT '0',
  `closing_stock` int(11) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  `days_out_of_stock` int(11) NOT NULL DEFAULT '0',
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `adjustmentpve` int(11) NOT NULL DEFAULT '0',
  `adjustmentnve` int(11) NOT NULL DEFAULT '0',
  `losses` int(11) NOT NULL DEFAULT '0',
  `quantity_ordered` int(11) DEFAULT NULL,
  `comment` varchar(100) DEFAULT NULL,
  `status` int(5) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1