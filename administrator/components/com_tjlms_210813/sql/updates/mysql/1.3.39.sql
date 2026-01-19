ALTER TABLE `#__tjlms_course_track` ADD COLUMN `last_accessed_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `status`;
ALTER TABLE `#__tjlms_course_track` ADD COLUMN `cert_gen_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `last_accessed_date`;
