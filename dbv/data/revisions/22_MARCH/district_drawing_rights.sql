ALTER TABLE `district_drawing_rights` 
CHANGE COLUMN `user_id` `user_id` INT(11) NULL AFTER `confirmatory_used`,
CHANGE COLUMN `screening` `screening_allocated` INT(11) NOT NULL ,
CHANGE COLUMN `screening_current_amount` `screening_used` INT(11) NOT NULL ,
CHANGE COLUMN `confirmatory` `confirmatory_allocated` INT(11) NOT NULL ,
CHANGE COLUMN `confirmatory_current_amount` `confirmatory_used` INT(11) NOT NULL ,
CHANGE COLUMN `updated_on` `updated_at` TIMESTAMP(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
ADD COLUMN `created_at` TIMESTAMP(0) NULL DEFAULT CURRENT_TIMESTAMP AFTER `user_id`;
