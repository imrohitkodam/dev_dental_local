ALTER TABLE `#__tjlms_lesson_track` ENGINE = InnoDB;
ALTER TABLE `#__tjlms_modules` ADD `description` text;
ALTER TABLE `#__tjlms_modules` ADD `image` varchar(255) DEFAULT '';
ALTER TABLE `#__tjlms_modules` ADD `storage` varchar(50) DEFAULT '';
ALTER TABLE `#__tjlms_certificate_template` ADD `params` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `modified_date`
