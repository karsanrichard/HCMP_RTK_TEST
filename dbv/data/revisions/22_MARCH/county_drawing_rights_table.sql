CREATE OR REPLACE TABLE `county_drawing_rights` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `county_id` INT NULL,
  `zone` VARCHAR(45) NULL,
  `duration` VARCHAR(45) NULL,
  `screening_amount` INT NULL,
  `confirmatory_amount` INT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `rtk`.`lab_commodity_details` 
ADD INDEX `created_at` (`created_at` ASC);

CREATE INDEX query_one_index ON lab_commodity_details(facility_code, commodity_id,created_at);

ALTER TABLE `rtk`.`county_drawing_rights` 
ADD COLUMN `created_at` TIMESTAMP(0) NULL DEFAULT CURRENT_TIMESTAMP AFTER `confirmatory_amount`,
ADD COLUMN `updated_at` TIMESTAMP(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

ALTER TABLE `rtk`.`county_drawing_rights` 
CHANGE COLUMN `screening_amount` `screening_total` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `confirmatory_amount` `confirmatory_total` INT(11) NULL DEFAULT NULL ,
ADD COLUMN `screening_used` INT NULL AFTER `screening_total`,
ADD COLUMN `confirmatory_used` INT NULL AFTER `confirmatory_total`;

ALTER TABLE `rtk`.`county_drawing_rights` 
CHANGE COLUMN `screening_total` `screening_total` INT(11) NULL DEFAULT 0 ,
CHANGE COLUMN `screening_used` `screening_used` INT(11) NULL DEFAULT 0 ,
CHANGE COLUMN `confirmatory_total` `confirmatory_total` INT(11) NULL DEFAULT 0 ,
CHANGE COLUMN `confirmatory_used` `confirmatory_used` INT(11) NULL DEFAULT 0 ;
