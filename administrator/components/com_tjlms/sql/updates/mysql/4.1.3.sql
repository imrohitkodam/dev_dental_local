ALTER TABLE `#__tjlms_courses` ADD COLUMN `admin_approval` tinyint(1) NOT NULL DEFAULT 0 AFTER `group_id`;
ALTER TABLE `#__tjlms_courses` ADD COLUMN `auto_enroll` tinyint(1) NOT NULL DEFAULT 0 AFTER `admin_approval`;
