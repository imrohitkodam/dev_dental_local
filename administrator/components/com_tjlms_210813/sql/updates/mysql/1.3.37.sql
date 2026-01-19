ALTER TABLE `#__tjlms_courses` DROP INDEX `cat_id`;
ALTER TABLE `#__tjlms_courses` CHANGE `catid` `catid` int(11) NOT NULL;
ALTER TABLE `#__tjlms_courses` ADD INDEX `catid` (`catid`);
