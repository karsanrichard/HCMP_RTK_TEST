CREATE DEFINER=`root`@`localhost` PROCEDURE `get_allocation_data`(given_facility_code VARCHAR (45), created_at VARCHAR(45))
BEGIN
	SELECT amc,
        closing_stock,
        days_out_of_stock,
        q_requested
        FROM
        lab_commodity_details AS a
        WHERE
        facility_code = given_facility_code
        AND commodity_id between 4 and 6
        AND created_at = created_at;
END