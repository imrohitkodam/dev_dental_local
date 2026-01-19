ALTER TABLE `#__tjlms_orders` ADD `accept_terms`  int(11) NOT NULL DEFAULT 0 AFTER `customer_note`;
UPDATE `#__tjlms_course_track` SET `status`='I' WHERE `status`='';
DELETE FROM `#__tj_reports` WHERE `plugin`='categoryreport' AND `client`='com_tjlms'
