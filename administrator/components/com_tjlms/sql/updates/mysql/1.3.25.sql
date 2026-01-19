ALTER TABLE `#__tjlms_coupons` DROP INDEX `coupon_code`;
ALTER TABLE `#__tjlms_courses` DROP INDEX `course_alias`;
ALTER TABLE `#__tjlms_lessons` DROP INDEX `lesson_alias`;
ALTER TABLE `#__tjlms_storage_s3` DROP INDEX `storageid`;

ALTER TABLE `#__tjlms_coupons` ADD UNIQUE KEY `coupon_code` (`code`(100));
ALTER TABLE `#__tjlms_courses` ADD UNIQUE KEY `course_alias` (`alias`(100));
ALTER TABLE `#__tjlms_lessons` ADD UNIQUE KEY `lesson_alias` (`alias`(100));
ALTER TABLE `#__tjlms_storage_s3` ADD UNIQUE KEY (`storageid`(100));